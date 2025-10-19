<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class HuggingFaceService
{
    protected string $apiUrl = 'https://api-inference.huggingface.co/models/';
    protected string $routerUrl = 'https://router.huggingface.co/v1/chat/completions';
    protected string $model = 'meta-llama/Llama-3.2-3B-Instruct'; // Free model via Inference Providers

    public function generateText(string $prompt): string
    {
        $apiKey = config('services.huggingface.api_key');
        if (!$apiKey) {
            throw new \Exception('HuggingFace API key not configured');
        }

        // Use the new Inference Providers API (OpenAI-compatible) - FREE tier
        $response = Http::withoutVerifying()
            ->timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->retry(3, 3000)
            ->post($this->routerUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that writes engaging blog content about environmental topics, trees, and nature conservation.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
                'stream' => false
            ]);

        if ($response->failed()) {
            $this->handleApiErrors($response->status(), $response->body());
        }

        $result = $response->json();
        
        // OpenAI-compatible format response
        if (isset($result['choices'][0]['message']['content'])) {
            return trim((string) $result['choices'][0]['message']['content']);
        }
        
        throw new \Exception('Format de réponse inattendu');
    }

    public function generateChat(string $userInput, array $conversationHistory = [], array $params = []): array
    {
        $apiKey = config('services.huggingface.api_key');
        if (!$apiKey) {
            throw new \Exception('HuggingFace API key not configured');
        }

        // Build messages array from conversation history
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant.'
            ]
        ];

        // Add conversation history
        foreach ($conversationHistory as $turn) {
            if (isset($turn['question'])) {
                $messages[] = ['role' => 'user', 'content' => (string) $turn['question']];
            }
            if (isset($turn['answer'])) {
                $messages[] = ['role' => 'assistant', 'content' => (string) $turn['answer']];
            }
        }

        // Add current user input
        $messages[] = ['role' => 'user', 'content' => (string) $userInput];

        $maxTokens = $params['max_tokens'] ?? 200;
        $temperature = $params['temperature'] ?? 0.7;

        $response = Http::withoutVerifying()
            ->timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->retry(3, 3000)
            ->post($this->routerUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'stream' => false
            ]);

        if ($response->failed()) {
            $this->handleApiErrors($response->status(), $response->body());
        }

        $result = $response->json();
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \Exception('Format de réponse inattendu');
        }

        $generatedText = trim((string) $result['choices'][0]['message']['content']);

        // Build updated conversation history
        $pastUserInputs = array_column($conversationHistory, 'question');
        $pastUserInputs[] = $userInput;
        
        $generatedResponses = array_column($conversationHistory, 'answer');
        $generatedResponses[] = $generatedText;

        $historyOut = [];
        $count = count($pastUserInputs);
        for ($i = 0; $i < $count; $i++) {
            $historyOut[] = [
                'question' => $pastUserInputs[$i] ?? '',
                'answer' => $generatedResponses[$i] ?? '',
            ];
        }

        return [
            'text' => $generatedText,
            'conversation' => [
                'past_user_inputs' => $pastUserInputs,
                'generated_responses' => $generatedResponses,
            ],
            'history' => $historyOut,
        ];
    }

    /**
     * Analyze sentiment of text (positive, negative, neutral)
     * Uses free Hugging Face Inference API
     * 
     * @param string $text The text to analyze
     * @return array ['label' => 'POSITIVE'|'NEGATIVE'|'NEUTRAL', 'score' => float, 'emoji' => string]
     */
    public function analyzeSentiment(string $text): array
    {
        $apiKey = config('services.huggingface.api_key');
        if (!$apiKey) {
            throw new \Exception('HuggingFace API key not configured');
        }

        // Use a free sentiment analysis model
        $sentimentModel = 'cardiffnlp/twitter-roberta-base-sentiment-latest';
        
        $response = Http::withoutVerifying()
            ->timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->retry(2, 2000)
            ->post($this->apiUrl . $sentimentModel, [
                'inputs' => substr($text, 0, 500), // Limit to 500 chars
                'options' => [
                    'wait_for_model' => true,
                ]
            ]);

        if ($response->failed()) {
            // Return neutral if analysis fails
            return [
                'label' => 'NEUTRAL',
                'score' => 0.5,
                'emoji' => '😐'
            ];
        }

        $result = $response->json();
        
        // Parse response format: [0] => [['label' => 'positive', 'score' => 0.99]]
        if (isset($result[0]) && is_array($result[0])) {
            // Find highest score
            $topResult = collect($result[0])->sortByDesc('score')->first();
            
            if ($topResult) {
                $label = strtoupper($topResult['label']);
                $score = $topResult['score'];
                
                // Map to emoji
                $emojiMap = [
                    'POSITIVE' => '😊',
                    'NEGATIVE' => '😞',
                    'NEUTRAL' => '😐',
                ];
                
                return [
                    'label' => $label,
                    'score' => round($score, 2),
                    'emoji' => $emojiMap[$label] ?? '😐'
                ];
            }
        }

        // Default to neutral
        return [
            'label' => 'NEUTRAL',
            'score' => 0.5,
            'emoji' => '😐'
        ];
    }

    private function handleApiErrors(int $statusCode, string $body): void
    {
        if ($statusCode === 404) {
            throw new \Exception('Modèle non trouvé. Vérifiez le nom du modèle.');
        }
        if ($statusCode === 401 || $statusCode === 403) {
            throw new \Exception('Permission refusée. Vérifiez votre token HuggingFace. Créez un token avec les permissions "Make calls to Inference Providers" sur https://huggingface.co/settings/tokens');
        }
        if ($statusCode === 429) {
            throw new \Exception('Limite de taux API atteinte. Veuillez attendre quelques secondes avant de réessayer.');
        }
        if ($statusCode === 503) {
            throw new \Exception('Service temporairement indisponible. Réessayez dans quelques instants.');
        }
        
        // Try to parse error message from response
        $errorData = json_decode($body, true);
        $errorMsg = $errorData['error']['message'] ?? $body;
        
        throw new \Exception('Erreur HuggingFace API: ' . $errorMsg);
    }
}
