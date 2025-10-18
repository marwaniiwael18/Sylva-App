<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PlantIdentificationService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.plant.id/v2';

    public function __construct()
    {
        $this->apiKey = config('services.plantid.api_key');
        
        if (empty($this->apiKey)) {
            throw new Exception('Plant.id API key is not configured. Please add PLANT_ID_API_KEY to your .env file');
        }
    }

    /**
     * Identify a plant/tree from an image
     *
     * @param string $imagePath Local file path or URL to the image
     * @return array ['success' => bool, 'name' => string, 'scientific_name' => string, 'confidence' => float, 'details' => array]
     */
    public function identifyPlant(string $imagePath): array
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

            // Call Plant.id API
            $response = Http::timeout(30)
                ->withHeaders([
                    'Api-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/identify", [
                    'images' => [$imageData],
                    'modifiers' => ['similar_images'],
                    'plant_details' => [
                        'common_names',
                        'taxonomy',
                        'url',
                        'wiki_description',
                        'edible_parts',
                        'watering'
                    ]
                ]);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $statusCode = $response->status();
                
                Log::error('Plant.id API Error', [
                    'status' => $statusCode,
                    'body' => $errorBody
                ]);
                
                // Handle rate limiting
                if ($statusCode === 429) {
                    return [
                        'success' => false,
                        'error' => 'rate_limit',
                        'message' => 'API rate limit reached. Free tier allows 100 identifications per month.'
                    ];
                }
                
                // Handle quota exceeded
                if ($statusCode === 402 || $statusCode === 403) {
                    return [
                        'success' => false,
                        'error' => 'quota_exceeded',
                        'message' => 'Monthly API quota exceeded. Please try again next month or upgrade to a paid plan.'
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => 'api_error',
                    'message' => $errorBody['error'] ?? 'API request failed with status ' . $statusCode
                ];
            }

            $data = $response->json();
            
            // Check if plant was identified
            if (empty($data['suggestions'])) {
                return [
                    'success' => false,
                    'error' => 'no_identification',
                    'message' => 'Could not identify the plant from this image. Please try a clearer photo showing leaves, bark, or distinctive features.'
                ];
            }

            // Get the top suggestion
            $topSuggestion = $data['suggestions'][0];
            $probability = $topSuggestion['probability'] ?? 0;
            
            // Only accept if confidence is above 30%
            if ($probability < 0.3) {
                return [
                    'success' => false,
                    'error' => 'low_confidence',
                    'message' => 'Plant identification confidence too low. Please upload a clearer image.'
                ];
            }

            // Extract plant details
            $plantDetails = $topSuggestion['plant_details'] ?? [];
            $plantName = $topSuggestion['plant_name'] ?? 'Unknown';
            
            // Get common names
            $commonNames = [];
            if (isset($plantDetails['common_names']) && is_array($plantDetails['common_names'])) {
                $commonNames = array_slice($plantDetails['common_names'], 0, 3); // Get first 3 common names
            }
            
            // Get scientific name
            $scientificName = $plantDetails['scientific_name'] ?? 
                             ($plantDetails['taxonomy']['genus'] ?? '') . ' ' . 
                             ($plantDetails['taxonomy']['species'] ?? '');
            
            // Get taxonomy
            $taxonomy = [
                'kingdom' => $plantDetails['taxonomy']['kingdom'] ?? null,
                'phylum' => $plantDetails['taxonomy']['phylum'] ?? null,
                'class' => $plantDetails['taxonomy']['class'] ?? null,
                'order' => $plantDetails['taxonomy']['order'] ?? null,
                'family' => $plantDetails['taxonomy']['family'] ?? null,
                'genus' => $plantDetails['taxonomy']['genus'] ?? null,
            ];

            // Determine tree type based on characteristics
            $suggestedType = $this->determineTreeType($plantDetails, $plantName, $scientificName);

            return [
                'success' => true,
                'name' => $commonNames[0] ?? $plantName,
                'common_names' => $commonNames,
                'scientific_name' => trim($scientificName),
                'confidence' => round($probability * 100, 2), // Convert to percentage
                'taxonomy' => $taxonomy,
                'description' => $plantDetails['wiki_description']['value'] ?? null,
                'watering' => $plantDetails['watering'] ?? null,
                'edible_parts' => $plantDetails['edible_parts'] ?? null,
                'suggested_type' => $suggestedType,
                'url' => $plantDetails['url'] ?? null,
                'similar_images' => $topSuggestion['similar_images'] ?? [],
                'all_suggestions' => array_slice($data['suggestions'], 0, 5), // Top 5 suggestions
                'raw_data' => $data
            ];

        } catch (Exception $e) {
            Log::error('Plant Identification Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'exception',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get base64 encoded image data
     */
    protected function getImageBase64(string $path): ?string
    {
        try {
            // Check if it's a URL or local file
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $imageContent = file_get_contents($path);
            } else {
                // Handle storage path
                if (strpos($path, 'storage/') === 0) {
                    $path = public_path($path);
                } elseif (strpos($path, 'trees/') === 0) {
                    $path = storage_path('app/public/' . $path);
                }

                if (!file_exists($path)) {
                    Log::warning('Image file not found', ['path' => $path]);
                    return null;
                }

                $imageContent = file_get_contents($path);
            }

            return 'data:image/jpeg;base64,' . base64_encode($imageContent);

        } catch (Exception $e) {
            Log::error('Image encoding error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get health assessment for a tree (using Plant.id health assessment API)
     *
     * @param string $imagePath Local file path or URL to the image
     * @return array ['success' => bool, 'is_healthy' => bool, 'diseases' => array, 'suggestions' => array]
     */
    public function assessHealth(string $imagePath): array
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

            // Call Plant.id health assessment API
            $response = Http::timeout(30)
                ->withHeaders([
                    'Api-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/health_assessment", [
                    'images' => [$imageData],
                    'modifiers' => ['similar_images'],
                    'disease_details' => [
                        'cause',
                        'common_names',
                        'classification',
                        'description',
                        'treatment',
                        'url'
                    ]
                ]);

            if (!$response->successful()) {
                $errorBody = $response->json();
                Log::error('Plant.id Health Assessment Error', [
                    'status' => $response->status(),
                    'body' => $errorBody
                ]);
                
                return [
                    'success' => false,
                    'error' => 'api_error',
                    'message' => 'Health assessment failed'
                ];
            }

            $data = $response->json();
            
            $isHealthy = $data['is_healthy']['binary'] ?? true;
            $healthProbability = $data['is_healthy']['probability'] ?? 1.0;
            
            $diseases = [];
            if (!$isHealthy && isset($data['suggestions'])) {
                foreach ($data['suggestions'] as $suggestion) {
                    if (isset($suggestion['disease_details'])) {
                        $diseases[] = [
                            'name' => $suggestion['name'] ?? 'Unknown disease',
                            'probability' => round(($suggestion['probability'] ?? 0) * 100, 2),
                            'common_names' => $suggestion['disease_details']['common_names'] ?? [],
                            'description' => $suggestion['disease_details']['description'] ?? null,
                            'treatment' => $suggestion['disease_details']['treatment'] ?? null,
                            'cause' => $suggestion['disease_details']['cause'] ?? null,
                        ];
                    }
                }
            }

            return [
                'success' => true,
                'is_healthy' => $isHealthy,
                'health_probability' => round($healthProbability * 100, 2),
                'diseases' => $diseases,
                'suggestions' => !empty($diseases) ? $this->generateHealthSuggestions($diseases) : ['Tree appears healthy!'],
                'raw_data' => $data
            ];

        } catch (Exception $e) {
            Log::error('Plant Health Assessment Error', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'exception',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate health suggestions based on detected diseases
     */
    protected function generateHealthSuggestions(array $diseases): array
    {
        $suggestions = [];
        
        foreach ($diseases as $disease) {
            if (!empty($disease['treatment'])) {
                $suggestions[] = $disease['treatment'];
            }
        }
        
        if (empty($suggestions)) {
            $suggestions[] = 'Consult with a local arborist or tree specialist';
            $suggestions[] = 'Monitor the tree regularly for changes';
            $suggestions[] = 'Ensure proper watering and nutrition';
        }
        
        return array_unique($suggestions);
    }

    /**
     * Identify plant and assess health in one call
     */
    public function identifyAndAssessHealth(string $imagePath): array
    {
        $identification = $this->identifyPlant($imagePath);
        
        if (!$identification['success']) {
            return $identification;
        }
        
        $health = $this->assessHealth($imagePath);
        
        return [
            'success' => true,
            'identification' => $identification,
            'health' => $health
        ];
    }

    /**
     * Determine tree type based on plant characteristics
     */
    protected function determineTreeType(array $plantDetails, string $plantName, string $scientificName): string
    {
        $name = strtolower($plantName . ' ' . $scientificName);
        
        // Check for fruit trees
        $fruitKeywords = ['apple', 'orange', 'lemon', 'cherry', 'peach', 'pear', 'plum', 'apricot', 
                          'fig', 'olive', 'avocado', 'mango', 'citrus', 'prunus', 'malus', 'pyrus',
                          'date', 'palm', 'banana', 'coconut', 'pomegranate', 'grape'];
        
        // Check if has edible parts
        if (!empty($plantDetails['edible_parts']) && is_array($plantDetails['edible_parts'])) {
            if (in_array('fruit', $plantDetails['edible_parts']) || 
                in_array('seeds', $plantDetails['edible_parts'])) {
                return 'Fruit';
            }
        }
        
        foreach ($fruitKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return 'Fruit';
            }
        }
        
        // Check for medicinal trees
        $medicinalKeywords = ['eucalyptus', 'neem', 'willow', 'ginkgo', 'tea tree', 'moringa', 
                             'aloe', 'chamomile', 'lavender', 'sage'];
        
        foreach ($medicinalKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return 'Medicinal';
            }
        }
        
        // Check for ornamental trees (flowering, decorative)
        $ornamentalKeywords = ['magnolia', 'cherry blossom', 'dogwood', 'flowering', 'ornamental',
                               'rose', 'lilac', 'azalea', 'camellia', 'jasmine', 'hibiscus',
                               'bougainvillea', 'wisteria', 'hydrangea'];
        
        foreach ($ornamentalKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return 'Ornamental';
            }
        }
        
        // Check taxonomy for conifers and large trees (likely Forest)
        $forestFamilies = ['pinaceae', 'cupressaceae', 'fagaceae', 'betulaceae'];
        $forestKeywords = ['pine', 'oak', 'maple', 'birch', 'spruce', 'fir', 'cedar', 'redwood',
                          'sequoia', 'cypress', 'juniper', 'beech', 'ash', 'elm', 'poplar',
                          'quercus', 'pinus', 'acer', 'betula'];
        
        // Check family
        if (isset($plantDetails['taxonomy']['family'])) {
            $family = strtolower($plantDetails['taxonomy']['family']);
            if (in_array($family, $forestFamilies)) {
                return 'Forest';
            }
        }
        
        foreach ($forestKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return 'Forest';
            }
        }
        
        // Default to Ornamental for unknown types
        return 'Ornamental';
    }
}
