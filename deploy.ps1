# Script automatique pour push + deploiement local
# Ce script fait le push, surveille le workflow CI, et lance les containers automatiquement

param(
    [string]$Branch = "main",
    [int]$TimeoutMinutes = 30
)

Write-Host "=== Deploiement automatique Sylva-App ===" -ForegroundColor Cyan

# Verifier si GitHub CLI est installe
if (!(Get-Command gh -ErrorAction SilentlyContinue)) {
    Write-Host "Installation automatique de GitHub CLI..." -ForegroundColor Yellow
    try {
        Invoke-WebRequest -Uri "https://github.com/cli/cli/releases/download/v2.40.1/gh_2.40.1_windows_amd64.zip" -OutFile "gh.zip"
        Expand-Archive -Path "gh.zip" -DestinationPath "gh-temp" -Force
        Remove-Item "gh.zip" -Force
        $env:Path += ";$(Get-Location)\gh-temp\bin"
        Write-Host "GitHub CLI installe temporairement" -ForegroundColor Green
    } catch {
        Write-Host "Erreur installation GitHub CLI: $_" -ForegroundColor Red
        Write-Host "Installez-le manuellement depuis https://cli.github.com/" -ForegroundColor Yellow
        exit 1
    }
}

# Verifier l'authentification GitHub
Write-Host "Verification authentification GitHub..." -ForegroundColor Yellow
try {
    $authStatus = gh auth status 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Vous n'etes pas authentifie. Lancez 'gh auth login'" -ForegroundColor Red
        exit 1
    }
    Write-Host "Authentification GitHub OK" -ForegroundColor Green
} catch {
    Write-Host "Erreur verification authentification: $_" -ForegroundColor Red
    exit 1
}

# Verifier si Docker est installe
if (!(Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "Docker n'est pas installe." -ForegroundColor Red
    exit 1
}

# Verifier si on est dans un repo git
if (!(Test-Path .git)) {
    Write-Host "Ce n'est pas un depot git." -ForegroundColor Red
    exit 1
}

Write-Host "Verification statut du repo..." -ForegroundColor Yellow

# Verifier s'il y a des changements a commiter
$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "Changements detectes. Commit automatique..." -ForegroundColor Yellow
    git add .
    git commit -m "Auto-commit: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
} else {
    Write-Host "Aucun changement a commiter." -ForegroundColor Green
}

# Push vers GitHub
Write-Host "Push vers GitHub (branche: $Branch)..." -ForegroundColor Yellow
try {
    git push origin $Branch
    Write-Host "Push reussi !" -ForegroundColor Green
} catch {
    Write-Host "Echec du push: $_" -ForegroundColor Red
    exit 1
}

# Attendre que le workflow commence
Write-Host "Attente demarrage workflow GitHub Actions..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Surveiller le workflow
Write-Host "Surveillance workflow CI..." -ForegroundColor Yellow
$workflowName = "CI/CD Pipeline"
$startTime = Get-Date
$timeout = $TimeoutMinutes * 60

while (((Get-Date) - $startTime).TotalSeconds -lt $timeout) {
    try {
        $workflowRuns = gh run list --workflow=$workflowName --json status,conclusion,createdAt --limit 1 | ConvertFrom-Json

        if ($workflowRuns.Count -eq 0) {
            Write-Host "Workflow pas encore demarre..." -ForegroundColor Yellow
            Start-Sleep -Seconds 10
            continue
        }

        $latestRun = $workflowRuns[0]
        $status = $latestRun.status
        $conclusion = $latestRun.conclusion

        Write-Host "Statut workflow: $status" -ForegroundColor Cyan

        if ($status -eq "completed") {
            if ($conclusion -eq "success") {
                Write-Host "Workflow reussi ! Lancement containers Docker..." -ForegroundColor Green
                break
            } else {
                Write-Host "Workflow echoue: $conclusion" -ForegroundColor Red
                exit 1
            }
        } elseif ($status -eq "in_progress" -or $status -eq "queued") {
            Write-Host "Workflow en cours ($status)..." -ForegroundColor Yellow
        }

    } catch {
        Write-Host "Erreur verification workflow: $_" -ForegroundColor Yellow
    }

    Start-Sleep -Seconds 15
}

if (((Get-Date) - $startTime).TotalSeconds -ge $timeout) {
    Write-Host "Timeout atteint ($TimeoutMinutes minutes)." -ForegroundColor Red
    exit 1
}

# Vérifier l'état des containers existants
Write-Host "Verification etat containers..." -ForegroundColor Yellow
$mysqlRunning = docker ps --filter "name=sylva-app-mysql" --filter "status=running" -q
$prometheusRunning = docker ps --filter "name=sylva-app-prometheus" --filter "status=running" -q
$grafanaRunning = docker ps --filter "name=sylva-app-grafana" --filter "status=running" -q
$appRunning = docker ps --filter "name=sylva-app-app" --filter "status=running" -q

# Generer APP_KEY si necessaire
if (!(Test-Path .env.ci)) {
    Write-Host "Generation APP_KEY..." -ForegroundColor Yellow
    $appKey = "base64:$(openssl rand -base64 32)"
    "APP_KEY=$appKey" | Out-File -FilePath .env.ci -Encoding UTF8
}

# Démarrer seulement les containers manquants
$containersToStart = @()

if (!$mysqlRunning) {
    $containersToStart += "mysql"
    Write-Host "MySQL sera demarre" -ForegroundColor Cyan
} else {
    Write-Host "MySQL deja en cours" -ForegroundColor Green
}

if (!$prometheusRunning) {
    $containersToStart += "prometheus"
    Write-Host "Prometheus sera demarre" -ForegroundColor Cyan
} else {
    Write-Host "Prometheus deja en cours" -ForegroundColor Green
}

if (!$grafanaRunning) {
    $containersToStart += "grafana"
    Write-Host "Grafana sera demarre" -ForegroundColor Cyan
} else {
    Write-Host "Grafana deja en cours" -ForegroundColor Green
}

# Toujours redémarrer l'application
$containersToStart += "app"
Write-Host "Application sera (re)demarree" -ForegroundColor Cyan

# Lancer les containers nécessaires
if ($containersToStart.Count -gt 0) {
    Write-Host "Lancement containers: $($containersToStart -join ', ')" -ForegroundColor Yellow
    try {
        $startCommand = "docker compose --env-file .env.ci up -d $($containersToStart -join ' ')"
        Invoke-Expression $startCommand
        Write-Host "Containers demarres !" -ForegroundColor Green
    } catch {
        Write-Host "Erreur lancement containers: $_" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "Tous les containers sont deja en cours" -ForegroundColor Green
}

# Attendre que l'app soit prete
Write-Host "Attente application prete..." -ForegroundColor Yellow
$maxRetries = 30
$retryCount = 0

while ($retryCount -lt $maxRetries) {
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8000" -TimeoutSec 5 -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-Host "Application prete ! http://localhost:8000" -ForegroundColor Green

            # Exécuter les migrations une fois que l'app est prête
            Write-Host "Execution migrations base de donnees..." -ForegroundColor Yellow
            try {
                docker compose --env-file .env.ci exec -T app php artisan migrate --force
                Write-Host "Migrations executees avec succes !" -ForegroundColor Green
            } catch {
                Write-Host "Erreur lors des migrations: $_" -ForegroundColor Red
                # Ne pas quitter, les migrations peuvent déjà être faites
            }

            Write-Host "Monitoring:" -ForegroundColor Cyan
            Write-Host "  - Prometheus: http://localhost:9090" -ForegroundColor White
            Write-Host "  - Grafana: http://localhost:3000 (admin/admin)" -ForegroundColor White
            break
        }
    } catch {
        Write-Host "Attente application... ($($retryCount + 1)/$maxRetries)" -ForegroundColor Yellow
    }

    $retryCount++
    Start-Sleep -Seconds 5
}

if ($retryCount -eq $maxRetries) {
    Write-Host "Application non disponible." -ForegroundColor Red
    docker compose logs app
    exit 1
}

Write-Host ""
Write-Host "Deploiement termine avec succes !" -ForegroundColor Green
Write-Host "Application accessible sur http://localhost:8000" -ForegroundColor Cyan

# Nettoyage fichiers temporaires
Write-Host "Nettoyage fichiers temporaires..." -ForegroundColor Yellow
if (Test-Path "gh-temp") {
    Remove-Item "gh-temp" -Recurse -Force
    Write-Host "Fichiers temporaires nettoyes" -ForegroundColor Green
}