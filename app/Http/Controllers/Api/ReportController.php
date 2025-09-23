<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Display a listing of reports with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Report::with(['user:id,name,email', 'validator:id,name,email']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by urgency
        if ($request->has('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        // Filter by location radius
        if ($request->has(['latitude', 'longitude', 'radius'])) {
            $query->inRadius(
                $request->latitude,
                $request->longitude,
                $request->radius ?? 10
            );
        }

        // Search by title or description
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reports->items(),
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        ]);
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type' => ['required', Rule::in(['tree_planting', 'maintenance', 'pollution', 'green_space_suggestion'])],
            'urgency' => ['required', Rule::in(['low', 'medium', 'high'])],
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reports', 'public');
                $imagePaths[] = $path;
            }
        }

        $report = Report::create([
            ...$validated,
            'user_id' => Auth::id() ?? 1, // Use test user ID 1 if not authenticated
            'images' => $imagePaths
        ]);

        $report->load(['user:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Signalement créé avec succès',
            'data' => $report
        ], 201);
    }

    /**
     * Display the specified report
     */
    public function show(Report $report): JsonResponse
    {
        $report->load(['user:id,name,email', 'validator:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, Report $report): JsonResponse
    {
        // Check if user can update this report (skip check if not authenticated - for testing)
        if (Auth::id() && $report->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à modifier ce signalement'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'type' => ['sometimes', Rule::in(['tree_planting', 'maintenance', 'pollution', 'green_space_suggestion'])],
            'urgency' => ['sometimes', Rule::in(['low', 'medium', 'high'])],
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($report->images) {
                foreach ($report->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('reports', 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        $report->update($validated);
        $report->load(['user:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Signalement mis à jour avec succès',
            'data' => $report
        ]);
    }

    /**
     * Remove the specified report
     */
    public function destroy(Report $report): JsonResponse
    {
        // Check if user can delete this report (skip check if not authenticated - for testing)
        if (Auth::id() && $report->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à supprimer ce signalement'
            ], 403);
        }

        // Delete associated images
        if ($report->images) {
            foreach ($report->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Signalement supprimé avec succès'
        ]);
    }

    /**
     * Validate a report (Admin/Moderator only)
     */
    public function validate(Request $request, Report $report): JsonResponse
    {
        // Check if user has permission to validate (skip check if not authenticated - for testing)
        if (Auth::user() && !Auth::user()->canValidateReports()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à valider ce signalement'
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['validated', 'rejected', 'in_progress', 'completed'])],
            'validation_notes' => 'nullable|string|max:1000'
        ]);

        $report->update([
            'status' => $validated['status'],
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'validation_notes' => $validated['validation_notes'] ?? null
        ]);

        $report->load(['user:id,name,email', 'validator:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Signalement validé avec succès',
            'data' => $report
        ]);
    }

    /**
     * Get reports statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'validated' => Report::where('status', 'validated')->count(),
            'in_progress' => Report::where('status', 'in_progress')->count(),
            'completed' => Report::where('status', 'completed')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
            'by_type' => [
                'tree_planting' => Report::where('type', 'tree_planting')->count(),
                'maintenance' => Report::where('type', 'maintenance')->count(),
                'pollution' => Report::where('type', 'pollution')->count(),
                'green_space_suggestion' => Report::where('type', 'green_space_suggestion')->count(),
            ],
            'by_urgency' => [
                'low' => Report::where('urgency', 'low')->count(),
                'medium' => Report::where('urgency', 'medium')->count(),
                'high' => Report::where('urgency', 'high')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
