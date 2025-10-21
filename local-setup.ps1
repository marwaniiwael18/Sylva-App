# Local Docker Compose Setup Script
# This script mimics the CI build job to set up containers locally

Write-Host "Setting up local Docker containers..."

# Generate Laravel APP_KEY
$APP_KEY = "base64:" + [Convert]::ToBase64String((Get-Random -Count 32 -InputObject (0..255)))

# Write to .env.ci
$envContent = "APP_KEY=$APP_KEY"
$envContent | Out-File -FilePath ".env.ci" -Encoding UTF8

Write-Host "Generated APP_KEY and saved to .env.ci"

# Build and start services
Write-Host "Building and starting services..."
docker compose --env-file .env.ci build --pull --parallel

Write-Host "Starting containers..."
docker compose --env-file .env.ci up -d --force-recreate --renew-anon-volumes mysql prometheus grafana app

Write-Host "Waiting for app to be healthy..."
for ($i = 1; $i -le 30; $i++) {
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8000" -Method Head -TimeoutSec 5
        if ($response.StatusCode -eq 200) {
            Write-Host "App is responding"
            break
        }
    } catch {
        Write-Host "Waiting for app... ($i/30)"
        Start-Sleep -Seconds 2
    }
}

Write-Host "Running database migrations..."
for ($i = 1; $i -le 30; $i++) {
    $result = docker compose --env-file .env.ci exec -T app php artisan migrate --force 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Migrations ran successfully"
        break
    } else {
        Write-Host "Waiting for migrations to succeed... ($i/30)"
        Start-Sleep -Seconds 2
    }
}

Write-Host "Setup complete. App should be available at http://localhost:8000"
Write-Host "Prometheus at http://localhost:9090"
Write-Host "Grafana at http://localhost:3000"