# Sylva-App

A Laravel-based donation system with AI-powered insights.

## Features

- Donation management with Stripe integration
- AI-powered campaign recommendations and insights
- Admin dashboard with analytics
- Refund management
- Code quality analysis with SonarQube
- Containerized deployment with Docker
- Monitoring with Prometheus and Grafana

## Setup

1. Clone the repository
2. Copy `.env.example` to `.env` and configure your environment variables
3. Run `composer install`
4. Run `npm install && npm run build`
5. Run `php artisan migrate`
6. Run `php artisan serve`

## Docker Setup

To run the application with Docker:

```bash
docker-compose up --build
```

This will start the Laravel application, MySQL database, Prometheus, and Grafana.

- Application: http://localhost:8000
- Grafana: http://localhost:3000 (admin/admin)
- Prometheus: http://localhost:9090

## CI/CD Pipeline

This project uses GitHub Actions for CI/CD with the following components:

- **GitHub Actions**: Automated workflows for testing, building, and deployment
- **Docker**: Containerization for consistent environments
- **SonarQube**: Code quality and security analysis
- **Prometheus**: Metrics collection and monitoring
- **Grafana**: Visualization of metrics and dashboards

### Setting up Secrets

For the CI/CD pipeline to work, you need to set up the following secrets in your GitHub repository:

- `SONAR_TOKEN`: Your SonarQube/SonarCloud token
- `SONAR_HOST_URL`: Your SonarQube server URL (e.g., https://sonarcloud.io)

### Pipeline Jobs

1. **Test**: Runs PHP and JavaScript tests, builds assets
2. **Sonar**: Performs code quality analysis
3. **Build**: Builds and pushes Docker images to GitHub Container Registry
4. **Monitoring Setup**: Builds monitoring stack images

## Environment Variables

Add the following to your `.env` file:

```
APP_NAME=Sylva-App
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sylva_app
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

GEMINI_API_KEY=your_gemini_api_key
```

## Testing

Run the test suite:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Sylva-App

A Laravel-based donation system with AI-powered insights.

## Features

- Donation management with Stripe integration
- AI-powered campaign recommendations and insights
- Admin dashboard with analytics
- Refund management
- Code quality analysis with SonarQube
- Containerized deployment with Docker
- Monitoring with Prometheus and Grafana

## Setup

1. Clone the repository
2. Copy `.env.example` to `.env` and configure your environment variables
3. Run `composer install`
4. Run `npm install && npm run build`
5. Run `php artisan migrate`
6. Run `php artisan serve`

## Docker Setup

To run the application with Docker:

```bash
docker-compose up --build
```

This will start the Laravel application, MySQL database, Prometheus, and Grafana.

- Application: http://localhost:8000
- Grafana: http://localhost:3000 (admin/admin)
- Prometheus: http://localhost:9090

## CI/CD Pipeline

This project uses GitHub Actions for CI/CD with the following components:

- **GitHub Actions**: Automated workflows for testing, building, and deployment
- **Docker**: Containerization for consistent environments
- **SonarQube**: Code quality and security analysis
- **Prometheus**: Metrics collection and monitoring
- **Grafana**: Visualization of metrics and dashboards

### Setting up Secrets

For the CI/CD pipeline to work, you need to set up the following secrets in your GitHub repository:

- `SONAR_TOKEN`: Your SonarQube/SonarCloud token
- `SONAR_HOST_URL`: Your SonarQube server URL (e.g., https://sonarcloud.io)

### Pipeline Jobs

1. **Test**: Runs PHP and JavaScript tests, builds assets
2. **Sonar**: Performs code quality analysis
3. **Build**: Builds and pushes Docker images to GitHub Container Registry
4. **Monitoring Setup**: Builds monitoring stack images

## Environment Variables

Add the following to your `.env` file:

```
APP_NAME=Sylva-App
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sylva_app
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

GEMINI_API_KEY=your_gemini_api_key
```

## Testing

Run the test suite:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
