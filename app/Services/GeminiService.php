<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected string $model = 'gemini-2.0-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured');
        }
    }

    /**
     * Analyze an image and generate a description for environmental reports
     *
     * @param string $imagePath Local file path or URL to the image
     * @param string $context Additional context (e.g., report type, location)
     * @return array ['success' => bool, 'description' => string, 'suggestions' => array]
     */
    public function analyzeImageForReport(string $imagePath, string $context = ''): array
    {
        try {
            // Convert image to base64
            $imageData = $this->getImageBase64($imagePath);
            
            if (!$imageData) {
                return [
                    'success' => false,
                    'error' => 'Failed to read image file'
                ];
            }

            // Create prompt for environmental report analysis
            $prompt = $this->buildReportAnalysisPrompt($context);

            // Call Gemini API
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $imageData['mime_type'],
                                        'data' => $imageData['base64']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 500,
                    ]
                ]);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $statusCode = $response->status();
                
                Log::error('Gemini API Error', [
                    'status' => $statusCode,
                    'body' => $errorBody
                ]);
                
                // Handle rate limiting
                if ($statusCode === 429 || 
                    (isset($errorBody['error']['status']) && $errorBody['error']['status'] === 'RESOURCE_EXHAUSTED')) {
                    return [
                        'success' => false,
                        'error' => 'rate_limit',
                        'message' => 'API rate limit reached. Please wait 60 seconds before trying again. The free API allows only 2-3 requests per minute.'
                    ];
                }
                
                // Handle quota exceeded
                if (isset($errorBody['error']['message']) && 
                    stripos($errorBody['error']['message'], 'quota') !== false) {
                    return [
                        'success' => false,
                        'error' => 'quota_exceeded',
                        'message' => 'Daily API quota exceeded. Please try again tomorrow or upgrade to a paid plan.'
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => 'api_error',
                    'message' => $errorBody['error']['message'] ?? 'API request failed with status ' . $statusCode
                ];
            }

            $data = $response->json();
            
            // Extract the generated text
            $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Check if image is not environmental
            if (stripos($generatedText, 'NOT_ENVIRONMENTAL') !== false) {
                return [
                    'success' => false,
                    'error' => 'not_environmental',
                    'message' => "This photo doesn't appear to be related to environmental issues. Please upload images showing trees, nature, pollution, green spaces, or environmental concerns. We can only accept environment-related photos."
                ];
            }
            
            // Parse the response
            $parsed = $this->parseAnalysisResponse($generatedText);

            return [
                'success' => true,
                'description' => $parsed['description'],
                'suggestions' => $parsed['suggestions'],
                'raw_analysis' => $generatedText
            ];

        } catch (Exception $e) {
            Log::error('Gemini Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get base64 encoded image data
     */
    protected function getImageBase64(string $path): ?array
    {
        try {
            // Check if it's a URL or local file
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $imageContent = file_get_contents($path);
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imageContent);
            } else {
                // Handle storage path
                if (strpos($path, 'storage/') === 0) {
                    $path = public_path($path);
                } elseif (strpos($path, 'reports/') === 0) {
                    $path = storage_path('app/public/' . $path);
                }

                if (!file_exists($path)) {
                    return null;
                }

                $imageContent = file_get_contents($path);
                $mimeType = mime_content_type($path);
            }

            return [
                'base64' => base64_encode($imageContent),
                'mime_type' => $mimeType
            ];

        } catch (Exception $e) {
            Log::error('Image encoding error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Build analysis prompt for environmental reports
     */
    protected function buildReportAnalysisPrompt(string $context): string
    {
        $basePrompt = "You are an AI assistant for an environmental reporting platform called Sylva-App. ";
        $basePrompt .= "This platform is ONLY for environmental issues: trees, nature, pollution, green spaces, parks, gardens, waste management, etc.\n\n";
        
        $basePrompt .= "IMPORTANT - FIRST CHECK IF IMAGE IS ENVIRONMENT-RELATED:\n";
        $basePrompt .= "If the image shows NON-environmental content (people's faces, food, animals, buildings without nature, indoor scenes, selfies, products, vehicles alone, etc.), ";
        $basePrompt .= "respond with ONLY:\n";
        $basePrompt .= "NOT_ENVIRONMENTAL: This image is not related to environmental issues. Please upload images showing trees, nature, pollution, green spaces, or environmental concerns.\n\n";
        
        $basePrompt .= "If the image IS environment-related (trees, plants, pollution, waste, parks, green spaces, environmental damage, nature, etc.), analyze it and provide:\n\n";
        $basePrompt .= "1. A clear, concise description (2-3 sentences) of what you see in the image\n";
        $basePrompt .= "2. Identify any environmental issues visible (pollution, damaged trees, waste, etc.)\n";
        $basePrompt .= "3. Suggest the report type (tree_planting, maintenance, pollution, or green_space_suggestion)\n";
        $basePrompt .= "4. Suggest urgency level (low, medium, or high)\n";
        $basePrompt .= "5. Provide 2-3 actionable recommendations\n\n";

        if (!empty($context)) {
            $basePrompt .= "Additional context: {$context}\n\n";
        }

        $basePrompt .= "Format your response as:\n";
        $basePrompt .= "DESCRIPTION: [your description]\n";
        $basePrompt .= "TYPE: [tree_planting/maintenance/pollution/green_space_suggestion]\n";
        $basePrompt .= "URGENCY: [low/medium/high]\n";
        $basePrompt .= "RECOMMENDATIONS:\n- [recommendation 1]\n- [recommendation 2]\n- [recommendation 3]";

        return $basePrompt;
    }

    /**
     * Parse the AI response into structured data
     */
    protected function parseAnalysisResponse(string $text): array
    {
        $description = '';
        $suggestions = [
            'type' => null,
            'urgency' => null,
            'recommendations' => []
        ];

        // Extract description
        if (preg_match('/DESCRIPTION:\s*(.+?)(?=TYPE:|URGENCY:|RECOMMENDATIONS:|$)/is', $text, $matches)) {
            $description = trim($matches[1]);
        }

        // Extract type
        if (preg_match('/TYPE:\s*(\w+)/i', $text, $matches)) {
            $type = strtolower(trim($matches[1]));
            $validTypes = ['tree_planting', 'maintenance', 'pollution', 'green_space_suggestion'];
            if (in_array($type, $validTypes)) {
                $suggestions['type'] = $type;
            }
        }

        // Extract urgency
        if (preg_match('/URGENCY:\s*(\w+)/i', $text, $matches)) {
            $urgency = strtolower(trim($matches[1]));
            $validUrgencies = ['low', 'medium', 'high'];
            if (in_array($urgency, $validUrgencies)) {
                $suggestions['urgency'] = $urgency;
            }
        }

        // Extract recommendations
        if (preg_match('/RECOMMENDATIONS:\s*(.+?)$/is', $text, $matches)) {
            $recommendationsText = trim($matches[1]);
            preg_match_all('/-\s*(.+?)(?=\n-|\n\n|$)/s', $recommendationsText, $recMatches);
            if (!empty($recMatches[1])) {
                $suggestions['recommendations'] = array_map('trim', $recMatches[1]);
            }
        }

        // Fallback: use entire text as description if parsing failed
        if (empty($description)) {
            $description = trim($text);
        }

        return [
            'description' => $description,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Generate a summary for multiple images
     */
    public function analyzeMultipleImages(array $imagePaths, string $context = ''): array
    {
        $analyses = [];
        $combinedDescription = '';
        $allRecommendations = [];
        $hasNonEnvironmental = false;

        foreach ($imagePaths as $index => $imagePath) {
            $result = $this->analyzeImageForReport($imagePath, $context);
            
            // Check if any image is non-environmental
            if (!$result['success'] && isset($result['error']) && $result['error'] === 'not_environmental') {
                $hasNonEnvironmental = true;
                break;
            }
            
            if ($result['success']) {
                $analyses[] = $result;
                $combinedDescription .= ($index > 0 ? ' ' : '') . $result['description'];
                
                if (!empty($result['suggestions']['recommendations'])) {
                    $allRecommendations = array_merge($allRecommendations, $result['suggestions']['recommendations']);
                }
            }
        }
        
        // If any image is non-environmental, return error
        if ($hasNonEnvironmental) {
            return [
                'success' => false,
                'error' => 'not_environmental',
                'message' => 'One or more photos are not related to environmental issues. Please upload only images showing trees, nature, pollution, green spaces, or environmental concerns.'
            ];
        }

        // Get most common type and urgency from all analyses
        $types = array_filter(array_column(array_column($analyses, 'suggestions'), 'type'));
        $urgencies = array_filter(array_column(array_column($analyses, 'suggestions'), 'urgency'));
        
        $suggestedType = !empty($types) ? array_values(array_count_values($types))[0] : null;
        $suggestedUrgency = !empty($urgencies) ? array_values(array_count_values($urgencies))[0] : null;

        return [
            'success' => !empty($analyses),
            'description' => $combinedDescription,
            'suggestions' => [
                'type' => $types[0] ?? null,
                'urgency' => $urgencies[0] ?? null,
                'recommendations' => array_unique($allRecommendations)
            ],
            'individual_analyses' => $analyses
        ];
    }
}
