<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Liste des événements
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['organizer:id,name', 'participants:id,name']);

        // Filtrage par type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtrage par date
        if ($request->has('upcoming')) {
            $query->where('date', '>', now());
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('date', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    /**
     * Créer un événement
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'type' => 'required|in:Tree Planting,Maintenance,Awareness,Workshop',
        ]);

        $validated['organized_by_user_id'] = Auth::id();

        $event = Event::create($validated);
        $event->load(['organizer:id,name', 'participants:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Événement créé avec succès',
            'data' => $event,
        ], 201);
    }

    /**
     * Afficher un événement
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['organizer:id,name', 'participants:id,name']);
        
        $isParticipant = Auth::check() && $event->hasParticipant(Auth::user());
        $canEdit = Auth::check() && (Auth::user()->isAdmin() || Auth::id() === $event->organized_by_user_id);

        return response()->json([
            'success' => true,
            'data' => array_merge($event->toArray(), [
                'is_participant' => $isParticipant,
                'can_edit' => $canEdit,
                'participants_count' => $event->participants_count,
                'is_past' => $event->is_past,
                'formatted_date' => $event->formatted_date,
            ]),
        ]);
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        // Vérifier les permissions
        if (!Auth::user()->isAdmin() && Auth::id() !== $event->organized_by_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cet événement.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'type' => 'required|in:Tree Planting,Maintenance,Awareness,Workshop',
        ]);

        $event->update($validated);
        $event->load(['organizer:id,name', 'participants:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Événement mis à jour avec succès',
            'data' => $event,
        ]);
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event): JsonResponse
    {
        // Vérifier les permissions
        if (!Auth::user()->isAdmin() && Auth::id() !== $event->organized_by_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer cet événement.',
            ], 403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Événement supprimé avec succès',
        ]);
    }

    /**
     * S'inscrire à un événement
     */
    public function join(Event $event): JsonResponse
    {
        $user = Auth::user();

        if ($event->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous participez déjà à cet événement.',
            ], 400);
        }

        if ($event->is_past) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de s\'inscrire à un événement passé.',
            ], 400);
        }

        $event->participants()->attach($user->id, ['registered_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie ! Vous participez maintenant à cet événement.',
        ]);
    }

    /**
     * Se désinscrire d'un événement
     */
    public function leave(Event $event): JsonResponse
    {
        $user = Auth::user();

        if (!$event->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne participez pas à cet événement.',
            ], 400);
        }

        $event->participants()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Vous vous êtes désinscrit de cet événement.',
        ]);
    }

    /**
     * Mes événements
     */
    public function myEvents(): JsonResponse
    {
        $user = Auth::user();

        $organizedEvents = $user->organizedEvents()
                                ->with('participants:id,name')
                                ->orderBy('date', 'desc')
                                ->get();

        $participatingEvents = $user->participatingEvents()
                                   ->with('organizer:id,name')
                                   ->orderBy('date', 'desc')
                                   ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'organized' => $organizedEvents,
                'participating' => $participatingEvents,
            ],
        ]);
    }

    /**
     * Statistiques des événements
     */
    public function statistics(): JsonResponse
    {
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('date', '>', now())->count();
        $pastEvents = Event::where('date', '<', now())->count();
        $totalParticipations = \DB::table('event_user')->count();

        $eventsByType = Event::select('type', \DB::raw('count(*) as count'))
                             ->groupBy('type')
                             ->get()
                             ->pluck('count', 'type')
                             ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'total_events' => $totalEvents,
                'upcoming_events' => $upcomingEvents,
                'past_events' => $pastEvents,
                'total_participations' => $totalParticipations,
                'events_by_type' => $eventsByType,
            ],
        ]);
    }
}