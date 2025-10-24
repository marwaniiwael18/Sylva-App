# Script de deploiement local Sylva-App
# Ce script pull les images Docker Hub et lance les containers

param(
    [string]$Branch = "main"
)

Write-Host "=== Deploiement local Sylva-App ===" -ForegroundColor Cyan

# Verifier si Docker est installe
if (!(Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "Docker n'est pas installe." -ForegroundColor Red
    exit 1
}



# Generer APP_KEY si necessaire
if (!(Test-Path .env.ci)) {
    Write-Host "Generation APP_KEY..." -ForegroundColor Yellow
    $appKey = "base64:$(openssl rand -base64 32)"
    "APP_KEY=$appKey" | Out-File -FilePath .env.ci -Encoding UTF8
}

# Lancer les containers
Write-Host "Lancement containers Docker..." -ForegroundColor Yellow
try {
    # Pull latest images from Docker Hub
    Write-Host "Pulling latest images from Docker Hub..." -ForegroundColor Cyan
    docker compose --env-file .env.ci pull app

    # Start containers
    docker compose --env-file .env.ci up -d  app mysql prometheus grafana
    Write-Host "Containers demarres !" -ForegroundColor Green
} catch {
    Write-Host "Erreur lancement containers: $_" -ForegroundColor Red
    exit 1
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