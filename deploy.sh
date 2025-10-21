#!/bin/bash

# Script automatique pour push + déploiement local
# Ce script fait le push, surveille le workflow CI, et lance les containers automatiquement

BRANCH=${1:-"main"}
TIMEOUT_MINUTES=${2:-30}

echo "🚀 Déploiement automatique Sylva-App"
echo "====================================="

# Vérifier si GitHub CLI est installé
if ! command -v gh &> /dev/null; then
    echo "❌ GitHub CLI (gh) n'est pas installé. Installez-le depuis https://cli.github.com/"
    exit 1
fi

# Vérifier si Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé."
    exit 1
fi

# Vérifier si on est dans un repo git
if [ ! -d ".git" ]; then
    echo "❌ Ce n'est pas un dépôt git."
    exit 1
fi

echo "📋 Vérification du statut du repo..."

# Vérifier s'il y a des changements à commiter
if [ -n "$(git status --porcelain)" ]; then
    echo "📝 Il y a des changements non committés. Commit automatique..."
    git add .
    git commit -m "Auto-commit: $(date '+%Y-%m-%d %H:%M:%S')"
else
    echo "✅ Aucun changement à commiter."
fi

# Push vers GitHub
echo "⬆️ Push vers GitHub (branche: $BRANCH)..."
if git push origin "$BRANCH"; then
    echo "✅ Push réussi !"
else
    echo "❌ Échec du push"
    exit 1
fi

# Attendre que le workflow commence
echo "⏳ Attente du démarrage du workflow GitHub Actions..."
sleep 10

# Surveiller le workflow
echo "👀 Surveillance du workflow CI..."
WORKFLOW_NAME="ci"
START_TIME=$(date +%s)
TIMEOUT=$((TIMEOUT_MINUTES * 60))

while [ $(($(date +%s) - START_TIME)) -lt $TIMEOUT ]; do
    # Récupérer le dernier workflow run
    if WORKFLOW_STATUS=$(gh run list --workflow="$WORKFLOW_NAME" --json status,conclusion --limit 1 2>/dev/null); then
        if [ -n "$WORKFLOW_STATUS" ] && [ "$WORKFLOW_STATUS" != "[]" ]; then
            STATUS=$(echo "$WORKFLOW_STATUS" | jq -r '.[0].status')
            CONCLUSION=$(echo "$WORKFLOW_STATUS" | jq -r '.[0].conclusion')

            echo "📊 Statut workflow: $STATUS"

            if [ "$STATUS" = "completed" ]; then
                if [ "$CONCLUSION" = "success" ]; then
                    echo "🎉 Workflow réussi ! Lancement des containers Docker..."
                    break
                else
                    echo "❌ Workflow échoué avec conclusion: $CONCLUSION"
                    echo "📋 Détails du workflow:"
                    gh run view --json jobs 2>/dev/null | jq -r '.jobs[] | "  - \(.name): \(.conclusion)"' || echo "Impossible de récupérer les détails"
                    exit 1
                fi
            elif [ "$STATUS" = "in_progress" ] || [ "$STATUS" = "queued" ]; then
                echo "⏳ Workflow en cours ($STATUS)..."
            else
                echo "⚠️ Statut inconnu: $STATUS"
            fi
        else
            echo "⏳ Workflow pas encore démarré..."
        fi
    else
        echo "⚠️ Erreur lors de la vérification du workflow"
    fi

    sleep 15
done

if [ $(($(date +%s) - START_TIME)) -ge $TIMEOUT ]; then
    echo "⏰ Timeout atteint ($TIMEOUT_MINUTES minutes). Arrêt de la surveillance."
    exit 1
fi

# Vérifier l'état des containers existants
echo "🔍 Vérification de l'état des containers..."
MYSQL_RUNNING=$(docker ps --filter "name=sylva-app-mysql" --filter "status=running" -q)
PROMETHEUS_RUNNING=$(docker ps --filter "name=sylva-app-prometheus" --filter "status=running" -q)
GRAFANA_RUNNING=$(docker ps --filter "name=sylva-app-grafana" --filter "status=running" -q)
APP_RUNNING=$(docker ps --filter "name=sylva-app-app" --filter "status=running" -q)

# Générer APP_KEY si nécessaire
if [ ! -f ".env.ci" ]; then
    echo "🔑 Génération de l'APP_KEY..."
    APP_KEY="base64:$(openssl rand -base64 32)"
    echo "APP_KEY=$APP_KEY" > .env.ci
fi

# Déterminer quels containers démarrer
CONTAINERS_TO_START=""

if [ -z "$MYSQL_RUNNING" ]; then
    CONTAINERS_TO_START="$CONTAINERS_TO_START mysql"
    echo "🐬 MySQL sera démarré"
else
    echo "✅ MySQL déjà en cours"
fi

if [ -z "$PROMETHEUS_RUNNING" ]; then
    CONTAINERS_TO_START="$CONTAINERS_TO_START prometheus"
    echo "📊 Prometheus sera démarré"
else
    echo "✅ Prometheus déjà en cours"
fi

if [ -z "$GRAFANA_RUNNING" ]; then
    CONTAINERS_TO_START="$CONTAINERS_TO_START grafana"
    echo "📈 Grafana sera démarré"
else
    echo "✅ Grafana déjà en cours"
fi

# Toujours redémarrer l'application
CONTAINERS_TO_START="$CONTAINERS_TO_START app"
echo "🚀 Application sera (re)démarrée"

# Lancer les containers nécessaires
if [ -n "$CONTAINERS_TO_START" ]; then
    echo "🐳 Lancement des containers:$CONTAINERS_TO_START"
    if docker compose --env-file .env.ci up -d $CONTAINERS_TO_START; then
        echo "✅ Containers démarrés !"
    else
        echo "❌ Erreur lors du lancement des containers"
        exit 1
    fi
else
    echo "✅ Tous les containers sont déjà en cours"
fi

# Attendre que l'app soit prête
echo "⏳ Attente que l'application soit prête..."
MAX_RETRIES=30
RETRY_COUNT=0

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if curl -s --max-time 5 http://localhost:8000 > /dev/null 2>&1; then
        echo "🎉 Application prête ! http://localhost:8000"

        # Exécuter les migrations
        echo "🗃️ Exécution des migrations de base de données..."
        if docker compose --env-file .env.ci exec -T app php artisan migrate --force; then
            echo "✅ Migrations exécutées avec succès !"
        else
            echo "⚠️ Erreur lors des migrations (peuvent déjà être faites)"
        fi

        echo "📊 Monitoring disponible:"
        echo "  - Prometheus: http://localhost:9090"
        echo "  - Grafana: http://localhost:3000"
        echo "  - Admin Grafana: admin/admin"
        break
    else
        echo "⏳ Attente de l'application... ($((RETRY_COUNT + 1))/$MAX_RETRIES)"
    fi

    RETRY_COUNT=$((RETRY_COUNT + 1))
    sleep 5
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    echo "❌ L'application n'est pas devenue disponible dans le délai imparti."
    echo "📋 Logs des containers:"
    docker compose logs app
    exit 1
fi

echo ""
echo "🎊 Déploiement terminé avec succès !"
echo "💡 L'application est maintenant accessible sur http://localhost:8000"