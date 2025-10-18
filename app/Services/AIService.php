<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
        // Utiliser Gemini 2.0 Flash Experimental (le plus récent et gratuit)
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';
    }

    /**
     * Générer un événement à partir d'une description utilisateur
     */
    public function generateEvent(string $userInput): array
    {
        try {
            $prompt = $this->buildEventPrompt($userInput);
            
            Log::info('AI Request', [
                'url' => $this->apiUrl . '?key=' . substr($this->apiKey, 0, 10) . '...',
                'prompt_length' => strlen($prompt)
            ]);
            
            $response = Http::withOptions([
                'verify' => false, // Désactive la vérification SSL pour le développement
            ])->timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            Log::info('AI Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                return $this->parseEventResponse($content);
            }

            Log::error('AI Service Error', ['response' => $response->body()]);
            return $this->getDefaultResponse();

        } catch (\Exception $e) {
            Log::error('AI Service Exception', ['error' => $e->getMessage()]);
            return $this->getDefaultResponse();
        }
    }

    /**
     * Construire le prompt pour la génération d'événement
     */
    private function buildEventPrompt(string $userInput): string
    {
        $currentDate = date('Y-m-d');
        $currentDateTime = date('Y-m-d H:i:s');
        
        return <<<PROMPT
Tu es un assistant expert en organisation d'événements écologiques pour l'application Sylva (plateforme environnementale tunisienne).

Date actuelle : {$currentDateTime}

L'utilisateur souhaite créer un événement et a donné cette description :
"{$userInput}"

Génère un événement structuré avec les informations suivantes au format JSON strictement :

{
  "title": "Un titre accrocheur et engageant (maximum 100 caractères)",
  "description": "Une description détaillée et motivante (200-300 mots) qui inclut : objectifs, déroulement, bénéfices, public cible",
  "type": "Un parmi : Tree Planting, Maintenance, Awareness, Workshop",
  "date": "Date et heure de l'événement au format YYYY-MM-DD HH:MM (ex: 2025-11-15 09:00). Si mentionnée dans la description, utilise-la. Sinon, propose une date future pertinente selon le type d'événement",
  "location_suggestion": "Un lieu approprié en Tunisie avec la ville",
  "best_period": "Meilleure période/saison pour organiser cet événement",
  "recommendations": [
    "Conseil pratique 1",
    "Conseil pratique 2",
    "Conseil pratique 3"
  ],
  "materials_needed": "Liste du matériel nécessaire",
  "duration": "Durée estimée de l'événement"
}

Types d'événements :
- Tree Planting : Plantation d'arbres, reboisement, jardinage
- Maintenance : Entretien d'espaces verts, nettoyage, réparation
- Awareness : Sensibilisation, conférence, campagne de communication
- Workshop : Atelier pratique, formation, apprentissage

IMPORTANT pour la date :
- Si l'utilisateur mentionne une date spécifique (ex: "samedi prochain", "le 2 novembre", "dans 2 semaines"), calcule la date exacte
- Si aucune date n'est mentionnée, propose une date future appropriée (généralement 1-4 semaines dans le futur)
- Pour Tree Planting: privilégier automne/printemps, matin tôt (8h-9h)
- Pour Maintenance: weekend matin (8h-9h)
- Pour Awareness: semaine après-midi ou soirée (14h ou 18h)
- Pour Workshop: weekend matin ou après-midi (9h ou 14h)

Réponds UNIQUEMENT avec le JSON, sans texte additionnel avant ou après.
PROMPT;
    }

    /**
     * Parser la réponse de l'IA
     */
    private function parseEventResponse(string $content): array
    {
        try {
            // Nettoyer la réponse pour extraire le JSON
            $content = trim($content);
            
            // Retirer les backticks markdown si présents
            $content = preg_replace('/^```json\s*|\s*```$/m', '', $content);
            $content = trim($content);
            
            $data = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                // Formater la date pour le champ datetime-local HTML5
                $formattedDate = $this->formatDateForInput($data['date'] ?? '');
                
                return [
                    'success' => true,
                    'data' => [
                        'title' => $data['title'] ?? '',
                        'description' => $data['description'] ?? '',
                        'type' => $this->validateType($data['type'] ?? ''),
                        'date' => $formattedDate,
                        'location_suggestion' => $data['location_suggestion'] ?? '',
                        'best_period' => $data['best_period'] ?? '',
                        'recommendations' => $data['recommendations'] ?? [],
                        'materials_needed' => $data['materials_needed'] ?? '',
                        'duration' => $data['duration'] ?? '',
                    ]
                ];
            }
            
            return $this->getDefaultResponse();
            
        } catch (\Exception $e) {
            Log::error('Parse Error', ['error' => $e->getMessage(), 'content' => $content]);
            return $this->getDefaultResponse();
        }
    }

    /**
     * Valider le type d'événement
     */
    private function validateType(string $type): string
    {
        $validTypes = ['Tree Planting', 'Maintenance', 'Awareness', 'Workshop'];
        
        foreach ($validTypes as $validType) {
            if (stripos($type, $validType) !== false || stripos($validType, $type) !== false) {
                return $validType;
            }
        }
        
        return 'Awareness'; // Type par défaut
    }

    /**
     * Formater la date pour le champ datetime-local HTML5
     * Format attendu : YYYY-MM-DDTHH:MM
     */
    private function formatDateForInput(string $dateString): string
    {
        if (empty($dateString)) {
            return '';
        }

        try {
            // Essayer de parser différents formats de date
            $date = null;
            
            // Format 1: "YYYY-MM-DD HH:MM" ou "YYYY-MM-DD HH:MM:SS"
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/', $dateString)) {
                $date = \DateTime::createFromFormat('Y-m-d H:i', substr($dateString, 0, 16));
            }
            
            // Format 2: "DD/MM/YYYY HH:MM"
            if (!$date && preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}/', $dateString)) {
                $date = \DateTime::createFromFormat('d/m/Y H:i', substr($dateString, 0, 16));
            }
            
            // Format 3: Timestamp ou format ISO
            if (!$date) {
                $date = new \DateTime($dateString);
            }
            
            if ($date) {
                // Retourner au format datetime-local HTML5: YYYY-MM-DDTHH:MM
                return $date->format('Y-m-d\TH:i');
            }
            
        } catch (\Exception $e) {
            Log::warning('Date parsing error', ['date' => $dateString, 'error' => $e->getMessage()]);
        }
        
        return '';
    }

    /**
     * Réponse par défaut en cas d'erreur
     */
    private function getDefaultResponse(): array
    {
        return [
            'success' => false,
            'message' => 'Impossible de générer l\'événement automatiquement. Veuillez réessayer.',
            'data' => [
                'title' => '',
                'description' => '',
                'type' => 'Tree Planting',
                'date' => '',
                'location_suggestion' => '',
                'best_period' => '',
                'recommendations' => [],
                'materials_needed' => '',
                'duration' => '',
            ]
        ];
    }

    /**
     * Enrichir une description existante
     */
    public function enrichDescription(string $currentDescription): array
    {
        try {
            $prompt = <<<PROMPT
Tu es un rédacteur expert en événements écologiques.

Enrichis cette description d'événement pour la rendre plus engageante et détaillée :
"{$currentDescription}"

Ajoute :
- Des détails pratiques
- Des phrases motivantes
- Des bénéfices concrets
- Des informations sur ce qui sera appris/accompli

Garde un ton professionnel mais chaleureux. Maximum 300 mots.

Réponds UNIQUEMENT avec le texte enrichi, sans préambule.
PROMPT;

            $response = Http::withOptions([
                'verify' => false, // Désactive la vérification SSL pour le développement
            ])->timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'maxOutputTokens' => 512,
                ]
            ]);

            if ($response->successful()) {
                $enrichedText = $response->json('candidates.0.content.parts.0.text');
                return [
                    'success' => true,
                    'description' => trim($enrichedText)
                ];
            }

            return [
                'success' => false,
                'description' => $currentDescription
            ];

        } catch (\Exception $e) {
            Log::error('AI Enrich Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'description' => $currentDescription
            ];
        }
    }

    /**
     * Répondre aux questions sur un événement spécifique
     */
    public function answerEventQuestion(array $eventData, string $question, array $conversationHistory = []): array
    {
        try {
            $prompt = $this->buildEventChatbotPrompt($eventData, $question, $conversationHistory);
            
            Log::info('AI Chatbot Request', [
                'event_id' => $eventData['id'] ?? 'unknown',
                'question_length' => strlen($question)
            ]);
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 512,
                ]
            ]);

            if ($response->successful()) {
                $answer = $response->json('candidates.0.content.parts.0.text');
                return [
                    'success' => true,
                    'answer' => trim($answer)
                ];
            }

            Log::error('AI Chatbot Error', ['response' => $response->body()]);
            return [
                'success' => false,
                'answer' => 'Désolé, je ne peux pas répondre à cette question pour le moment. Veuillez contacter l\'organisateur pour plus d\'informations.'
            ];

        } catch (\Exception $e) {
            Log::error('AI Chatbot Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'answer' => 'Une erreur s\'est produite. Veuillez réessayer ou contacter l\'organisateur.'
            ];
        }
    }

    /**
     * Générer des posts pour les réseaux sociaux à partir d'un événement
     */
    public function generateSocialMediaPosts(array $eventData, array $platforms = ['facebook', 'twitter', 'instagram', 'linkedin']): array
    {
        try {
            $prompt = $this->buildSocialMediaPrompt($eventData, $platforms);
            
            Log::info('AI Social Media Request', [
                'event_id' => $eventData['id'] ?? 'unknown',
                'platforms' => $platforms
            ]);
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $postsText = $response->json('candidates.0.content.parts.0.text');
                
                // Parser le JSON retourné
                $postsText = trim($postsText);
                $postsText = preg_replace('/```json\s*/', '', $postsText);
                $postsText = preg_replace('/```\s*$/', '', $postsText);
                
                $posts = json_decode($postsText, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($posts['posts'])) {
                    return [
                        'success' => true,
                        'posts' => $posts['posts']
                    ];
                }
                
                Log::error('AI Social Media JSON Parse Error', ['response' => $postsText]);
                return [
                    'success' => false,
                    'message' => 'Erreur de parsing de la réponse'
                ];
            }

            Log::error('AI Social Media Error', ['response' => $response->body()]);
            return [
                'success' => false,
                'message' => 'Erreur lors de la génération des posts'
            ];

        } catch (\Exception $e) {
            Log::error('AI Social Media Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la génération'
            ];
        }
    }

    /**
     * Construire le prompt pour générer des posts réseaux sociaux
     */
    private function buildSocialMediaPrompt(array $eventData, array $platforms): string
    {
        $platformsList = implode(', ', $platforms);
        
        return <<<PROMPT
Tu es un expert en marketing digital et communication sur les réseaux sociaux, spécialisé dans les événements écologiques et communautaires.

INFORMATIONS SUR L'ÉVÉNEMENT:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📌 Titre: {$eventData['title']}
📝 Description: {$eventData['description']}
📅 Date: {$eventData['date']}
📍 Lieu: {$eventData['location']}
🏷️ Type: {$eventData['type']}
👤 Organisateur: {$eventData['organizer']}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

MISSION:
Génère des posts optimisés pour promouvoir cet événement sur les plateformes suivantes: {$platformsList}

INSTRUCTIONS POUR CHAQUE PLATEFORME:

📘 FACEBOOK (si demandé):
- Longueur: 150-300 caractères
- Ton: Engageant et communautaire
- Inclure: Call-to-action clair, émojis, questions pour engager
- Hashtags: 2-3 pertinents

🐦 TWITTER/X (si demandé):
- Longueur: Maximum 280 caractères
- Ton: Concis et percutant
- Inclure: Émojis, 2-3 hashtags pertinents, appel à l'action court

📸 INSTAGRAM (si demandé):
- Longueur: 100-150 caractères accrocheurs + caption détaillée
- Ton: Visuel et inspirant
- Inclure: 5-8 hashtags pertinents, émojis, appel à partager
- Mention: Suggérer un type de visuel à accompagner

💼 LINKEDIN (si demandé):
- Longueur: 200-400 caractères
- Ton: Professionnel mais engageant
- Inclure: Impact environnemental/social, 2-3 hashtags professionnels
- Focus sur: Bénéfices communautaires et professionnels

RÈGLES IMPORTANTES:
✅ Adapter le ton et le style à chaque plateforme
✅ Mettre en avant l'impact écologique/social
✅ Inclure un appel à l'action clair (participer, partager, s'inscrire)
✅ Utiliser des émojis de manière appropriée
✅ Créer un sentiment d'urgence positif
✅ Mentionner la date, le lieu et le type d'événement
✅ Rendre les posts partageables et engageants

FORMAT DE RÉPONSE (JSON STRICT):
{
  "posts": [
    {
      "platform": "facebook",
      "content": "Texte du post Facebook complet",
      "hashtags": ["#hashtag1", "#hashtag2"],
      "suggested_image": "Description du type d'image suggéré"
    },
    {
      "platform": "twitter",
      "content": "Texte du post Twitter complet avec hashtags",
      "hashtags": ["#hashtag1", "#hashtag2"]
    },
    {
      "platform": "instagram",
      "content": "Caption Instagram complète",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3", "#hashtag4", "#hashtag5"],
      "suggested_image": "Description détaillée du visuel à créer"
    },
    {
      "platform": "linkedin",
      "content": "Post LinkedIn professionnel",
      "hashtags": ["#hashtag1", "#hashtag2"]
    }
  ]
}

Génère UNIQUEMENT les posts pour les plateformes demandées: {$platformsList}
Réponds UNIQUEMENT avec le JSON, sans texte avant ou après.
PROMPT;
    }

    /**
     * Construire le prompt pour le chatbot FAQ événement
     */
    private function buildEventChatbotPrompt(array $eventData, string $question, array $conversationHistory): string
    {
        // Déterminer si c'est le premier message de la conversation
        $isFirstMessage = empty($conversationHistory);
        
        // Construire l'historique si existant
        $historyText = '';
        if (!empty($conversationHistory)) {
            $historyText = "\n\n📜 HISTORIQUE DE LA CONVERSATION:\n";
            foreach ($conversationHistory as $msg) {
                $historyText .= "Q: {$msg['question']}\nR: {$msg['answer']}\n\n";
            }
        }

        // Analyser le type d'événement pour des suggestions contextuelles
        $eventType = strtolower($eventData['type']);
        $contextualHints = $this->getContextualHints($eventType, $eventData['description']);

        return <<<PROMPT
🤖 Tu es un assistant virtuel intelligent spécialisé dans les événements écologiques et communautaires.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📋 INFORMATIONS SUR L'ÉVÉNEMENT:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📌 Titre: {$eventData['title']}
📝 Description complète: {$eventData['description']}
📅 Date: {$eventData['date']}
📍 Lieu: {$eventData['location']}
🏷️ Type: {$eventData['type']}
👤 Organisateur: {$eventData['organizer']}
👥 Participants inscrits: {$eventData['participants_count']}

{$contextualHints}
{$historyText}

❓ QUESTION ACTUELLE:
{$question}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
⚙️ RÈGLES STRICTES DE RÉPONSE:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. 🎯 SALUTATIONS:
   - Si c'est le PREMIER message (historique vide): Commence par "Bonjour 👋 !"
   - Si c'est une CONVERSATION EN COURS (historique présent): N'utilise PAS de salutation, réponds directement

2. 🧠 INTELLIGENCE CONTEXTUELLE:
   - ANALYSE LA DESCRIPTION en profondeur pour extraire des informations implicites
   - DÉDUIS des réponses logiques basées sur le contexte (type d'événement, lieu, description)
   - Pour Tree Planting: suppose gants, pelles, eau fournis; tenue de travail recommandée
   - Pour Workshop: suppose matériel fourni; arrivée 15 min avant; ambiance intérieure
   - Pour Maintenance: suppose outils fournis; tenue adaptée aux travaux
   - Pour Awareness: suppose présentation/conférence; ambiance décontractée

3. 📊 DÉDUCTION INTELLIGENTE:
   - Parking: Si parc/lieu public → "Parking public généralement disponible à proximité"
   - Transports: Si ville mentionnée → "Accessible en transports en commun de [ville]"
   - Durée: Calcule depuis les horaires donnés dans la description
   - Repas: Si "déjeuner", "repas", "buffet" dans description → Oui confirmé
   - Gratuit: Si aucun prix mentionné → Généralement gratuit, confirmer avec organisateur
   - Enfants: Pour événements familiaux/communautaires → Généralement bienvenus
   - Accessibilité: Parc public → Généralement accessible PMR

4. ⛔ ÉVITE "INFORMATION NON DISPONIBLE":
   - Au lieu de dire "non précisé", DÉDUIS une réponse probable
   - Fournis des recommandations générales basées sur le type d'événement
   - Exemple: "Pour ce type d'événement, je te recommande..."
   - Exemple: "Généralement pour les plantations d'arbres..."
   - Suggère de contacter l'organisateur SEULEMENT si vraiment aucune déduction possible

5. 💬 TON ET STYLE:
   - Ton amical et engageant (tutoiement)
   - Emojis pertinents (1-3 par réponse)
   - Concis: 50-150 mots maximum
   - Personnalisé selon le contexte de l'événement
   - Enthousiaste pour l'écologie et l'engagement communautaire

6. 📞 CONTACT ORGANISATEUR:
   - Ne suggère de contacter {$eventData['organizer']} QUE si:
     * Question très spécifique impossible à déduire
     * Information sensible (tarifs exacts, règlement strict)
     * Besoin d'accommodation spéciale (handicap, allergie)

7. ✅ INFORMATIONS PRIORITAIRES À DÉDUIRE:
   - Durée: Analyse les horaires dans description
   - Tenue: Base-toi sur le type d'événement
   - Matériel: Déduis du type d'activité
   - Repas: Cherche mots-clés dans description
   - Accessibilité: Type de lieu (parc = accessible)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Réponds maintenant à la question de manière intelligente et personnalisée:
PROMPT;
    }

    /**
     * Obtenir des indices contextuels selon le type d'événement
     */
    private function getContextualHints(string $eventType, string $description): string
    {
        $hints = "\n🔍 CONTEXTE INTELLIGENT:\n";
        
        // Analyser la description pour extraire des infos
        $descLower = strtolower($description);
        
        if (str_contains($eventType, 'tree planting') || str_contains($descLower, 'plantation')) {
            $hints .= "- Événement de plantation d'arbres\n";
            $hints .= "- Matériel typique: gants, pelles, plants fournis par l'organisation\n";
            $hints .= "- Tenue recommandée: vêtements confortables adaptés aux travaux extérieurs\n";
            $hints .= "- Eau généralement fournie, mais bouteille personnelle recommandée\n";
        } elseif (str_contains($eventType, 'workshop') || str_contains($descLower, 'atelier')) {
            $hints .= "- Événement de type atelier/formation\n";
            $hints .= "- Matériel pédagogique généralement fourni\n";
            $hints .= "- Arrivée 15 minutes avant conseillée\n";
        } elseif (str_contains($eventType, 'maintenance') || str_contains($descLower, 'entretien')) {
            $hints .= "- Événement de maintenance/entretien\n";
            $hints .= "- Outils de base fournis, mais gants personnels recommandés\n";
            $hints .= "- Tenue adaptée aux travaux manuels\n";
        } elseif (str_contains($eventType, 'awareness') || str_contains($descLower, 'sensibilisation')) {
            $hints .= "- Événement de sensibilisation/conférence\n";
            $hints .= "- Ambiance décontractée, tenue libre\n";
            $hints .= "- Supports informatifs fournis\n";
        }
        
        // Déduire info repas
        if (str_contains($descLower, 'déjeuner') || str_contains($descLower, 'repas') || 
            str_contains($descLower, 'buffet') || str_contains($descLower, 'nourriture')) {
            $hints .= "- 🍽️ Repas/collation mentionné dans la description\n";
        }
        
        // Déduire durée depuis les horaires
        if (preg_match('/(\d+)h\d*.*?(\d+)h\d*/i', $description, $matches)) {
            $hints .= "- ⏰ Horaires détectés dans la description\n";
        }
        
        return $hints;
    }
}

