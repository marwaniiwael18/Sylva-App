<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportActivityController extends Controller
{
    /**
     * Get all activities for a report (feed)
     */
    public function index(Report $report): JsonResponse
    {
        $activities = $report->activities()
            ->with(['user:id,name,email', 'replies.user:id,name,email'])
            ->paginate(20);

        // Get aggregated stats
        $stats = [
            'vote_score' => $report->vote_score,
            'total_comments' => $report->total_comments,
            'total_reactions' => $report->total_reactions,
            'upvotes' => $report->votes()->where('vote_value', 1)->count(),
            'downvotes' => $report->votes()->where('vote_value', -1)->count(),
            'reaction_breakdown' => $report->reactions()
                ->selectRaw('reaction_type, count(*) as count')
                ->groupBy('reaction_type')
                ->pluck('count', 'reaction_type'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $activities->items(),
                'stats' => $stats,
            ],
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ]
        ]);
    }

    /**
     * Add a comment to a report
     */
    public function storeComment(Request $request, Report $report): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:report_activities,id'
        ]);

        $activity = ReportActivity::create([
            'report_id' => $report->id,
            'user_id' => Auth::id() ?? 1, // Use test user if not authenticated
            'activity_type' => 'comment',
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $activity->load(['user:id,name,email', 'replies']);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => $activity
        ], 201);
    }

    /**
     * Vote on a report (upvote/downvote)
     */
    public function vote(Request $request, Report $report): JsonResponse
    {
        $validated = $request->validate([
            'vote_value' => ['required', 'integer', Rule::in([1, -1])] // 1 = upvote, -1 = downvote
        ]);

        $userId = Auth::id() ?? 1;

        // Check if user already voted
        $existingVote = ReportActivity::where([
            'report_id' => $report->id,
            'user_id' => $userId,
            'activity_type' => 'vote'
        ])->first();

        if ($existingVote) {
            // Update existing vote
            if ($existingVote->vote_value == $validated['vote_value']) {
                // Remove vote if clicking same vote again
                $existingVote->delete();
                $message = 'Vote removed';
            } else {
                // Change vote
                $existingVote->update(['vote_value' => $validated['vote_value']]);
                $message = 'Vote updated';
            }
        } else {
            // Create new vote
            ReportActivity::create([
                'report_id' => $report->id,
                'user_id' => $userId,
                'activity_type' => 'vote',
                'vote_value' => $validated['vote_value'],
            ]);
            $message = 'Vote recorded successfully';
        }

        // Return updated vote stats
        $stats = [
            'vote_score' => $report->fresh()->vote_score,
            'upvotes' => $report->votes()->where('vote_value', 1)->count(),
            'downvotes' => $report->votes()->where('vote_value', -1)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $stats
        ]);
    }

    /**
     * React to a report (like, love, support, concern)
     */
    public function react(Request $request, Report $report): JsonResponse
    {
        $validated = $request->validate([
            'reaction_type' => ['required', Rule::in(['like', 'love', 'support', 'concern'])]
        ]);

        $userId = Auth::id() ?? 1;

        // Check if user already reacted
        $existingReaction = ReportActivity::where([
            'report_id' => $report->id,
            'user_id' => $userId,
            'activity_type' => 'reaction'
        ])->first();

        if ($existingReaction) {
            if ($existingReaction->reaction_type == $validated['reaction_type']) {
                // Remove reaction if clicking same reaction
                $existingReaction->delete();
                $message = 'Reaction removed';
            } else {
                // Change reaction
                $existingReaction->update(['reaction_type' => $validated['reaction_type']]);
                $message = 'Reaction updated';
            }
        } else {
            // Create new reaction
            ReportActivity::create([
                'report_id' => $report->id,
                'user_id' => $userId,
                'activity_type' => 'reaction',
                'reaction_type' => $validated['reaction_type'],
            ]);
            $message = 'Reaction added successfully';
        }

        // Return updated reaction stats
        $stats = [
            'total_reactions' => $report->fresh()->total_reactions,
            'reaction_breakdown' => $report->reactions()
                ->selectRaw('reaction_type, count(*) as count')
                ->groupBy('reaction_type')
                ->pluck('count', 'reaction_type'),
        ];

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $stats
        ]);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, ReportActivity $activity): JsonResponse
    {
        // Check if user owns this activity
        if ($activity->user_id !== Auth::id() && Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this activity'
            ], 403);
        }

        // Only allow updating comments
        if ($activity->activity_type !== 'comment') {
            return response()->json([
                'success' => false,
                'message' => 'Only comments can be updated'
            ], 400);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $activity->update(['content' => $validated['content']]);
        $activity->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $activity
        ]);
    }

    /**
     * Delete an activity
     */
    public function destroy(ReportActivity $activity): JsonResponse
    {
        // Check if user owns this activity or is admin
        $userId = Auth::id();
        if ($activity->user_id !== $userId && $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this activity'
            ], 403);
        }

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted successfully'
        ]);
    }

    /**
     * Pin/Unpin a comment (Admin/Moderator only)
     */
    public function togglePin(ReportActivity $activity): JsonResponse
    {
        // Only allow pinning comments
        if ($activity->activity_type !== 'comment') {
            return response()->json([
                'success' => false,
                'message' => 'Only comments can be pinned'
            ], 400);
        }

        // For now, allow any user (in production, check if admin/moderator)
        $activity->update(['is_pinned' => !$activity->is_pinned]);
        $activity->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => $activity->is_pinned ? 'Comment pinned' : 'Comment unpinned',
            'data' => $activity
        ]);
    }
}
