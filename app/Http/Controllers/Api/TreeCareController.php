<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TreeCare;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TreeCareController extends Controller
{
    /**
     * Display a listing of tree care records.
     * Can filter by tree_id, user_id, event_id, activity_type
     */
    public function index(Request $request): JsonResponse
    {
        $query = TreeCare::with(['tree', 'maintainer', 'event'])
            ->orderBy('performed_at', 'desc');

        // Apply filters
        if ($request->has('tree_id')) {
            $query->byTree($request->tree_id);
        }

        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->has('event_id')) {
            $query->byEvent($request->event_id);
        }

        if ($request->has('activity_type')) {
            $query->byActivityType($request->activity_type);
        }

        if ($request->has('condition')) {
            $query->byCondition($request->condition);
        }

        if ($request->has('days')) {
            $query->recent($request->days);
        }

        $perPage = $request->get('per_page', 15);
        $maintenances = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $maintenances,
            'message' => 'Maintenance records retrieved successfully'
        ]);
    }

    /**
     * Store a newly created maintenance record.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tree_id' => 'required|exists:trees,id',
            'activity_type' => 'required|in:watering,pruning,fertilizing,disease_treatment,inspection,other',
            'performed_at' => 'required|date',
            'condition_after' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
            'event_id' => 'nullable|exists:events,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('tree-maintenance', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $maintenance = TreeCare::create($data);
        $maintenance->load(['tree', 'maintainer', 'event']);

        // Update tree status based on condition
        if (isset($data['condition_after'])) {
            $tree = Tree::find($data['tree_id']);
            if ($data['condition_after'] === 'poor') {
                $tree->update(['status' => 'Sick']);
            } elseif (in_array($data['condition_after'], ['excellent', 'good'])) {
                if ($tree->status === 'Sick') {
                    $tree->update(['status' => 'Planted']);
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $maintenance,
            'message' => 'Maintenance record created successfully'
        ], 201);
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(string $id): JsonResponse
    {
        $maintenance = TreeCare::with(['tree', 'maintainer', 'event'])->find($id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance record not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $maintenance,
            'message' => 'Maintenance record retrieved successfully'
        ]);
    }

    /**
     * Update the specified maintenance record.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $maintenance = TreeCare::find($id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance record not found'
            ], 404);
        }

        // Check if user is the maintainer or admin
        if ($maintenance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this maintenance record'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'activity_type' => 'sometimes|in:watering,pruning,fertilizing,disease_treatment,inspection,other',
            'performed_at' => 'sometimes|date',
            'condition_after' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
            'event_id' => 'nullable|exists:events,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle new image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($maintenance->images) {
                foreach ($maintenance->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('tree-maintenance', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $maintenance->update($data);
        $maintenance->load(['tree', 'maintainer', 'event']);

        return response()->json([
            'success' => true,
            'data' => $maintenance,
            'message' => 'Maintenance record updated successfully'
        ]);
    }

    /**
     * Remove the specified maintenance record.
     */
    public function destroy(string $id): JsonResponse
    {
        $maintenance = TreeCare::find($id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance record not found'
            ], 404);
        }

        // Check if user is the maintainer or admin
        if ($maintenance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this maintenance record'
            ], 403);
        }

        // Delete associated images
        if ($maintenance->images) {
            foreach ($maintenance->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $maintenance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance record deleted successfully'
        ]);
    }

    /**
     * Get maintenance statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $query = TreeCare::query();

        if ($request->has('tree_id')) {
            $query->byTree($request->tree_id);
        }

        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        $stats = [
            'total_maintenances' => $query->count(),
            'this_month' => (clone $query)->thisMonth()->count(),
            'this_year' => (clone $query)->thisYear()->count(),
            'by_activity_type' => $query->get()->groupBy('activity_type')->map->count(),
            'by_condition' => $query->whereNotNull('condition_after')->get()->groupBy('condition_after')->map->count(),
            'recent_activities' => TreeCare::with(['tree', 'maintainer'])
                ->when($request->has('tree_id'), fn($q) => $q->byTree($request->tree_id))
                ->when($request->has('user_id'), fn($q) => $q->byUser($request->user_id))
                ->orderBy('performed_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

    /**
     * Get maintenance history for a specific tree
     */
    public function treeHistory(string $treeId): JsonResponse
    {
        $tree = Tree::find($treeId);

        if (!$tree) {
            return response()->json([
                'success' => false,
                'message' => 'Tree not found'
            ], 404);
        }

        $maintenances = TreeCare::with(['maintainer', 'event'])
            ->byTree($treeId)
            ->orderBy('performed_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tree' => $tree,
                'maintenance_history' => $maintenances,
                'total_maintenances' => $maintenances->count(),
                'last_maintenance' => $maintenances->first(),
                'health_score' => $tree->health_score
            ],
            'message' => 'Tree maintenance history retrieved successfully'
        ]);
    }

    /**
     * Get user's maintenance activities
     */
    public function userActivities(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', auth()->id());

        $maintenances = TreeCare::with(['tree', 'event'])
            ->byUser($userId)
            ->orderBy('performed_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_activities' => TreeCare::byUser($userId)->count(),
            'trees_maintained' => TreeCare::byUser($userId)->distinct('tree_id')->count('tree_id'),
            'this_month' => TreeCare::byUser($userId)->thisMonth()->count(),
            'favorite_activity' => TreeCare::byUser($userId)->get()->groupBy('activity_type')->sortByDesc->count()->keys()->first()
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'maintenances' => $maintenances,
                'stats' => $stats
            ],
            'message' => 'User maintenance activities retrieved successfully'
        ]);
    }
}
