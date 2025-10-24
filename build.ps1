# Script de build et push Docker pour Sylva-App
# Ce script build l'image Docker et la push vers Docker Hub

param(
    [string]$Branch = "main"
)

Write-Host "=== Build et Push Docker Sylva-App ===" -ForegroundColor Cyan

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

Write-Host "Build et push effectues via GitHub Actions !" -ForegroundColor Green
Write-Host "Attendez la completion du workflow CI/CD avant de deployer." -ForegroundColor Cyan