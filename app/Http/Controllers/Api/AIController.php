<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Générer un événement à partir d'une description
     */
    public function generateEvent(Request $request): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|min:10|max:500',
        ]);

        $result = $this->aiService->generateEvent($request->description);

        return response()->json($result);
    }

    /**
     * Enrichir une description existante
     */
    public function enrichDescription(Request $request): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|min:10|max:1000',
        ]);

        $result = $this->aiService->enrichDescription($request->description);

        return response()->json($result);
    }

    /**
     * Répondre aux questions sur un événement spécifique (Chatbot FAQ)
     */
    public function askEventQuestion(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'question' => 'required|string|min:3|max:500',
            'conversation_history' => 'sometimes|array|max:10',
            'conversation_history.*.question' => 'required|string',
            'conversation_history.*.answer' => 'required|string',
        ]);

        // Récupérer l'événement avec ses relations
        $event = \App\Models\Event::with(['organizer', 'participants'])
            ->findOrFail($request->event_id);

        // Préparer les données de l'événement pour l'IA
        $eventData = [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->date->format('d/m/Y à H:i'),
            'location' => $event->location,
            'type' => $event->type,
            'organizer' => $event->organizer->name,
            'participants_count' => $event->participants->count(),
        ];

        $result = $this->aiService->answerEventQuestion(
            $eventData,
            $request->question,
            $request->get('conversation_history', [])
        );

        return response()->json($result);
    }

    /**
     * Générer des posts réseaux sociaux pour un événement
     */
    public function generateSocialPosts(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'platforms' => 'sometimes|array|min:1',
            'platforms.*' => 'in:facebook,twitter,instagram,linkedin',
        ]);

        // Récupérer l'événement avec ses relations
        $event = \App\Models\Event::with('organizer')->findOrFail($request->event_id);

        // Préparer les données de l'événement pour l'IA
        $eventData = [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->date->format('d/m/Y à H:i'),
            'location' => $event->location,
            'type' => $event->type,
            'organizer' => $event->organizer->name,
        ];

        $platforms = $request->get('platforms', ['facebook', 'twitter', 'instagram', 'linkedin']);

        $result = $this->aiService->generateSocialMediaPosts($eventData, $platforms);

        return response()->json($result);
    }
}
