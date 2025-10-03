<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    /**
     * Afficher la liste des événements
     */
    public function index(): View
    {
        $events = Event::with(['organizer', 'participants'])
                       ->orderBy('date', 'desc')
                       ->paginate(10);

        return view('pages.events.index', compact('events'));
    }

    /**
     * Afficher le formulaire de création d'événement
     */
    public function create(): View
    {
        return view('pages.events.create');
    }

    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'type' => 'required|in:Tree Planting,Maintenance,Awareness,Workshop',
        ]);

        $validated['organized_by_user_id'] = Auth::id();

        Event::create($validated);

        return redirect()->route('events.index')
                        ->with('success', 'Événement créé avec succès !');
    }

    /**
     * Afficher un événement spécifique
     */
    public function show(Event $event): View
    {
        $event->load(['organizer', 'participants']);
        $isParticipant = Auth::check() && $event->hasParticipant(Auth::user());
        $canEdit = Auth::check() && (Auth::user()->isAdmin() || Auth::id() === $event->organized_by_user_id);

        return view('pages.events.show', compact('event', 'isParticipant', 'canEdit'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Event $event): View
    {
        // Seul l'organisateur ou un admin peut modifier
        if (!Auth::user()->isAdmin() && Auth::id() !== $event->organized_by_user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet événement.');
        }

        return view('pages.events.edit', compact('event'));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        // Seul l'organisateur ou un admin peut modifier
        if (!Auth::user()->isAdmin() && Auth::id() !== $event->organized_by_user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet événement.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'type' => 'required|in:Tree Planting,Maintenance,Awareness,Workshop',
        ]);

        $event->update($validated);

        return redirect()->route('events.show', $event)
                        ->with('success', 'Événement mis à jour avec succès !');
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event): RedirectResponse
    {
        // Seul l'organisateur ou un admin peut supprimer
        if (!Auth::user()->isAdmin() && Auth::id() !== $event->organized_by_user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cet événement.');
        }

        $event->delete();

        return redirect()->route('events.index')
                        ->with('success', 'Événement supprimé avec succès !');
    }

    /**
     * S'inscrire à un événement
     */
    public function join(Event $event): RedirectResponse
    {
        $user = Auth::user();

        if ($event->hasParticipant($user)) {
            return redirect()->back()
                            ->with('error', 'Vous participez déjà à cet événement.');
        }

        if ($event->is_past) {
            return redirect()->back()
                            ->with('error', 'Impossible de s\'inscrire à un événement passé.');
        }

        $event->participants()->attach($user->id, ['registered_at' => now()]);

        return redirect()->back()
                        ->with('success', 'Inscription réussie ! Vous participez maintenant à cet événement.');
    }

    /**
     * Se désinscrire d'un événement
     */
    public function leave(Event $event): RedirectResponse
    {
        $user = Auth::user();

        if (!$event->hasParticipant($user)) {
            return redirect()->back()
                            ->with('error', 'Vous ne participez pas à cet événement.');
        }

        $event->participants()->detach($user->id);

        return redirect()->back()
                        ->with('success', 'Vous vous êtes désinscrit de cet événement.');
    }

    /**
     * Afficher les événements organisés par l'utilisateur connecté
     */
    public function myEvents(): View
    {
        $organizedEvents = Auth::user()->organizedEvents()
                                      ->with('participants')
                                      ->orderBy('date', 'desc')
                                      ->get();

        $participatingEvents = Auth::user()->participatingEvents()
                                          ->with('organizer')
                                          ->orderBy('date', 'desc')
                                          ->get();

        return view('pages.events.my-events', compact('organizedEvents', 'participatingEvents'));
    }
}