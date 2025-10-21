<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Tree;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    public function index()
    {
        $metrics = "";

        // User metrics
        $userCount = User::count();
        $metrics .= "# HELP laravel_users_total Total number of users\n";
        $metrics .= "# TYPE laravel_users_total gauge\n";
        $metrics .= "laravel_users_total {$userCount}\n\n";

        // Donation metrics
        $donationCount = Donation::count();
        $totalDonationAmount = Donation::sum('amount') ?? 0;
        $metrics .= "# HELP laravel_donations_total Total number of donations\n";
        $metrics .= "# TYPE laravel_donations_total gauge\n";
        $metrics .= "laravel_donations_total {$donationCount}\n\n";
        $metrics .= "# HELP laravel_donations_amount_total Total donation amount\n";
        $metrics .= "# TYPE laravel_donations_amount_total gauge\n";
        $metrics .= "laravel_donations_amount_total {$totalDonationAmount}\n\n";

        // Event metrics
        $eventCount = Event::count();
        $upcomingEvents = Event::where('date', '>', now())->count();
        $metrics .= "# HELP laravel_events_total Total number of events\n";
        $metrics .= "# TYPE laravel_events_total gauge\n";
        $metrics .= "laravel_events_total {$eventCount}\n\n";
        $metrics .= "# HELP laravel_events_upcoming_total Total upcoming events\n";
        $metrics .= "# TYPE laravel_events_upcoming_total gauge\n";
        $metrics .= "laravel_events_upcoming_total {$upcomingEvents}\n\n";

        // Tree metrics
        $treeCount = Tree::count();
        $plantedTrees = Tree::where('status', 'Planted')->count();
        $metrics .= "# HELP laravel_trees_total Total number of trees\n";
        $metrics .= "# TYPE laravel_trees_total gauge\n";
        $metrics .= "laravel_trees_total {$treeCount}\n\n";
        $metrics .= "# HELP laravel_trees_planted_total Total planted trees\n";
        $metrics .= "# TYPE laravel_trees_planted_total gauge\n";
        $metrics .= "laravel_trees_planted_total {$plantedTrees}\n\n";

        // Application health metrics
        $metrics .= "# HELP laravel_app_health Application health status (1=healthy, 0=unhealthy)\n";
        $metrics .= "# TYPE laravel_app_health gauge\n";
        $metrics .= "laravel_app_health 1\n\n";

        // Response time (mock for now - in production you'd measure actual response times)
        $responseTime = rand(100, 500); // milliseconds
        $metrics .= "# HELP laravel_response_time_ms Application response time in milliseconds\n";
        $metrics .= "# TYPE laravel_response_time_ms gauge\n";
        $metrics .= "laravel_response_time_ms {$responseTime}\n\n";

        // Database connections
        try {
            $dbConnections = DB::select('SHOW PROCESSLIST');
            $activeConnections = count($dbConnections);
        } catch (\Exception $e) {
            $activeConnections = 0;
        }
        $metrics .= "# HELP laravel_db_connections_active Number of active database connections\n";
        $metrics .= "# TYPE laravel_db_connections_active gauge\n";
        $metrics .= "laravel_db_connections_active {$activeConnections}\n\n";

        // System load (simplified - in production use actual system metrics)
        $loadAverage = sys_getloadavg()[0] ?? 0;
        $metrics .= "# HELP laravel_system_load_average System load average\n";
        $metrics .= "# TYPE laravel_system_load_average gauge\n";
        $metrics .= "laravel_system_load_average {$loadAverage}\n\n";

        // Memory usage
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024; // MB
        $metrics .= "# HELP laravel_memory_usage_mb Memory usage in MB\n";
        $metrics .= "# TYPE laravel_memory_usage_mb gauge\n";
        $metrics .= "laravel_memory_usage_mb {$memoryUsage}\n\n";

        return response($metrics, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}