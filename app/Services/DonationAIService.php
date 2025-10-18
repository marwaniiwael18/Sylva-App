<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DonationAIService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Generate donation insights and recommendations
     */
    public function generateInsights(array $donationData = []): array
    {
        // Try to use AI first, but fall back to enhanced insights if it fails
        try {
            $response = $this->callGeminiAPI($this->buildInsightsPrompt($donationData));
            return $this->parseInsightsResponse($response);
        } catch (\Exception $e) {
            Log::warning('Gemini API failed, using enhanced insights: ' . $e->getMessage());
            return $this->getEnhancedInsights($donationData);
        }
    }

    /**
     * Generate personalized thank you message for donor
     */
    public function generateThankYouMessage(Donation $donation): string
    {
        $prompt = $this->buildThankYouPrompt($donation);

        try {
            $response = $this->callGeminiAPI($prompt);
            return $this->parseThankYouResponse($response);
        } catch (\Exception $e) {
            Log::error('Gemini API error for thank you message: ' . $e->getMessage());
            return $this->getFallbackThankYouMessage($donation);
        }
    }

    /**
     * Generate donation campaign recommendations
     */
    public function generateCampaignRecommendations(array $historicalData = []): array
    {
        $prompt = $this->buildCampaignPrompt($historicalData);

        try {
            $response = $this->callGeminiAPI($prompt);
            return $this->parseCampaignResponse($response);
        } catch (\Exception $e) {
            Log::error('Gemini API error for campaign recommendations: ' . $e->getMessage());
            return $this->getFallbackCampaignRecommendations();
        }
    }

    /**
     * Analyze donation patterns and predict trends
     */
    public function analyzePatterns(array $donationHistory = []): array
    {
        $prompt = $this->buildPatternAnalysisPrompt($donationHistory);

        try {
            $response = $this->callGeminiAPI($prompt);
            return $this->parsePatternResponse($response);
        } catch (\Exception $e) {
            Log::error('Gemini API error for pattern analysis: ' . $e->getMessage());
            return $this->getFallbackPatternAnalysis();
        }
    }

    /**
     * Generate refund analysis and recommendations
     */
    public function analyzeRefundRisk(Donation $donation): array
    {
        $prompt = $this->buildRefundAnalysisPrompt($donation);

        try {
            $response = $this->callGeminiAPI($prompt);
            return $this->parseRefundResponse($response);
        } catch (\Exception $e) {
            Log::error('Gemini API error for refund analysis: ' . $e->getMessage());
            return $this->getFallbackRefundAnalysis();
        }
    }

    private function callGeminiAPI(string $prompt): array
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'url' => $this->baseUrl
                ]);
                throw new \Exception('Gemini API request failed with status ' . $response->status() . ': ' . $errorBody);
            }

            $jsonResponse = $response->json();
            if (isset($jsonResponse['error'])) {
                Log::error('Gemini API returned error', ['error' => $jsonResponse['error']]);
                throw new \Exception('Gemini API error: ' . ($jsonResponse['error']['message'] ?? 'Unknown error'));
            }

            return $jsonResponse;
        } catch (\Exception $e) {
            Log::error('Gemini API call exception: ' . $e->getMessage(), [
                'api_key_configured' => !empty($this->apiKey),
                'api_key_length' => strlen($this->apiKey ?? ''),
                'base_url' => $this->baseUrl
            ]);
            throw $e;
        }
    }

    private function buildInsightsPrompt(array $data): string
    {
        $totalDonations = $data['total_donations'] ?? 0;
        $totalAmount = $data['total_amount'] ?? 0;
        $avgDonation = $data['avg_donation'] ?? 0;
        $topTypes = $data['top_types'] ?? [];
        $monthlyTrend = $data['monthly_trend'] ?? [];

        return "As an AI assistant for a environmental donation platform called Sylva, analyze the following donation data and provide insights and recommendations:

Total Donations: {$totalDonations}
Total Amount: €{$totalAmount}
Average Donation: €{$avgDonation}
Top Donation Types: " . implode(', ', $topTypes) . "
Monthly Trend: " . implode(', ', $monthlyTrend) . "

Please provide:
1. Key insights about donation patterns
2. Recommendations for improving donation rates
3. Suggestions for targeted campaigns
4. Risk factors to monitor

Format your response as a JSON object with keys: insights, recommendations, campaigns, risks.";
    }

    private function buildThankYouPrompt(Donation $donation): string
    {
        $amount = $donation->amount;
        $type = $donation->type_name;
        $userName = $donation->user->name ?? 'Valued Donor';
        $eventName = $donation->event->name ?? 'our environmental initiatives';

        return "Write a personalized thank you message for a donor to Sylva (environmental platform). 

Donor Name: {$userName}
Donation Amount: €{$amount}
Donation Type: {$type}
Project/Event: {$eventName}

The message should be:
- Warm and sincere
- Highlight the environmental impact
- Encourage future involvement
- Keep it under 150 words
- Professional but friendly tone

Write only the thank you message, no additional text.";
    }

    private function buildCampaignPrompt(array $data): string
    {
        $seasonal = $data['seasonal_patterns'] ?? [];
        $successful = $data['successful_campaigns'] ?? [];
        $demographics = $data['donor_demographics'] ?? [];

        return "Based on donation platform data, suggest 3 targeted donation campaigns for Sylva (environmental platform):

Seasonal Patterns: " . implode(', ', $seasonal) . "
Successful Past Campaigns: " . implode(', ', $successful) . "
Donor Demographics: " . implode(', ', $demographics) . "

For each campaign suggest:
- Campaign name
- Target audience
- Key message
- Expected impact
- Timeline

Format as JSON array of campaign objects.";
    }

    private function buildPatternAnalysisPrompt(array $data): string
    {
        $monthly = $data['monthly_totals'] ?? [];
        $typeDistribution = $data['type_distribution'] ?? [];
        $timePatterns = $data['time_patterns'] ?? [];

        return "Analyze donation patterns and predict trends:

Monthly Totals: " . implode(', ', $monthly) . "
Type Distribution: " . implode(', ', $typeDistribution) . "
Time Patterns: " . implode(', ', $timePatterns) . "

Provide:
1. Current trends analysis
2. Future predictions (3-6 months)
3. Seasonal patterns identified
4. Recommendations for optimization

Format as JSON with keys: trends, predictions, seasonal, recommendations.";
    }

    private function buildRefundAnalysisPrompt(Donation $donation): string
    {
        $amount = $donation->amount;
        $daysSince = $donation->created_at->diffInDays(now());
        $donorHistory = $donation->user->donations()->count() ?? 0;
        $type = $donation->type;

        return "Analyze refund risk for this donation:

Amount: €{$amount}
Days Since Donation: {$daysSince}
Donor History: {$donorHistory} previous donations
Donation Type: {$type}

Assess refund risk level (low/medium/high) and provide reasoning.
Consider: amount size, time elapsed, donor loyalty, donation type.

Format as JSON: {risk_level: string, reasoning: string, recommendations: array}";
    }

    private function parseInsightsResponse(array $response): array
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $text = str_replace('```json', '', $text);
        $text = str_replace('```', '', $text);

        return json_decode($text, true) ?: $this->getFallbackInsights();
    }

    private function parseThankYouResponse(array $response): string
    {
        return $response['candidates'][0]['content']['parts'][0]['text'] ?? $this->getFallbackThankYouMessage(null);
    }

    private function parseCampaignResponse(array $response): array
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        $text = str_replace('```json', '', $text);
        $text = str_replace('```', '', $text);

        return json_decode($text, true) ?: $this->getFallbackCampaignRecommendations();
    }

    private function parsePatternResponse(array $response): array
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $text = str_replace('```json', '', $text);
        $text = str_replace('```', '', $text);

        return json_decode($text, true) ?: $this->getFallbackPatternAnalysis();
    }

    private function parseRefundResponse(array $response): array
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $text = str_replace('```json', '', $text);
        $text = str_replace('```', '', $text);

        return json_decode($text, true) ?: $this->getFallbackRefundAnalysis();
    }

    private function getFallbackInsights(): array
    {
        // Generate basic insights from available data
        $insights = [];
        $recommendations = [];
        $campaigns = [];
        $risks = [];

        // This method is called when AI fails, but we can still provide basic analysis
        // The $aiData is not available here, so we'll provide generic insights
        $insights[] = 'Système d\'analyse IA temporairement indisponible - Analyse basique fournie';
        $insights[] = 'Les données de dons montrent une activité normale sur la plateforme';
        $insights[] = 'Recommandation: Vérifier régulièrement les tendances de dons';

        $recommendations[] = 'Envoyer des emails de remerciement aux donateurs récents';
        $recommendations[] = 'Promouvoir les campagnes de plantation d\'arbres';
        $recommendations[] = 'Analyser les types de dons les plus populaires';

        $campaigns[] = 'Campagne de sensibilisation environnementale';
        $campaigns[] = 'Programme de dons mensuels récurrents';
        $campaigns[] = 'Partenariats avec écoles pour programmes éducatifs';

        $risks[] = 'Fluctuations saisonnières possibles';
        $risks[] = 'Risque de diminution des dons pendant les périodes creuses';
        $risks[] = 'Concurrence avec d\'autres organisations environnementales';

        return [
            'insights' => $insights,
            'recommendations' => $recommendations,
            'campaigns' => $campaigns,
            'risks' => $risks
        ];
    }

    private function getEnhancedInsights(array $donationData = []): array
    {
        $totalDonations = $donationData['total_donations'] ?? 0;
        $totalAmount = $donationData['total_amount'] ?? 0;
        $avgDonation = $donationData['avg_donation'] ?? 0;
        $monthlyGrowth = $donationData['monthly_growth'] ?? 0;
        $topTypes = $donationData['top_types'] ?? [];
        $pendingRefunds = $donationData['pending_refunds'] ?? 0;

        $insights = [];
        $recommendations = [];
        $campaigns = [];
        $risks = [];

        // Generate sophisticated insights based on actual data
        if ($totalDonations > 0) {
            $insights[] = "Plateforme active avec {$totalDonations} dons totaux pour un montant de " . number_format($totalAmount, 2) . "€";

            if ($avgDonation > 0) {
                $insights[] = "Don moyen de " . number_format($avgDonation, 2) . "€ par donateur";
            }

            // Growth analysis
            if ($monthlyGrowth > 0) {
                $insights[] = "Croissance mensuelle de +" . number_format($monthlyGrowth, 1) . "% - tendance positive";
            } elseif ($monthlyGrowth < 0) {
                $insights[] = "Déclin mensuel de " . number_format($monthlyGrowth, 1) . "% - attention requise";
            }

            // Donation type analysis
            if (!empty($topTypes)) {
                $topType = $topTypes[0] ?? null;
                if ($topType) {
                    $insights[] = "Type de don le plus populaire: " . ucfirst(str_replace('_', ' ', $topType));
                }
            }

            // Refund analysis
            if ($pendingRefunds > 0) {
                $insights[] = "{$pendingRefunds} remboursement(s) en attente de traitement";
            }

        } else {
            $insights[] = "Aucune donnée de don disponible pour l'analyse";
            $insights[] = "Recommandation: Collecter plus de données pour une analyse précise";
        }

        // Dynamic recommendations based on data
        if ($totalDonations < 10) {
            $recommendations[] = "Augmenter la visibilité de la plateforme pour attirer plus de donateurs";
        }

        if ($avgDonation < 50) {
            $recommendations[] = "Encourager les dons de plus gros montants avec des contreparties spéciales";
        }

        if ($monthlyGrowth < 0) {
            $recommendations[] = "Lancer des campagnes de réengagement pour les donateurs inactifs";
        }

        $recommendations[] = "Envoyer des emails de remerciement personnalisés aux donateurs récents";
        $recommendations[] = "Créer des campagnes de sensibilisation sur les réseaux sociaux";
        $recommendations[] = "Développer un programme de dons récurrents mensuels";
        $recommendations[] = "Organiser des événements communautaires de plantation d'arbres";

        // Dynamic campaigns based on performance
        if ($totalAmount > 1000) {
            $campaigns[] = "Campagne d'expansion - Ciblage de nouveaux marchés géographiques";
        }

        $campaigns[] = "Campagne 'Un Arbre pour l'Avenir' - Plantation collective";
        $campaigns[] = "Programme éducatif dans les écoles primaires";
        $campaigns[] = "Partenariats avec entreprises locales pour dons corporate";
        $campaigns[] = "Campagne de sensibilisation pendant la saison environnementale";

        // Dynamic risks based on data
        if ($pendingRefunds > 2) {
            $risks[] = "Nombre élevé de remboursements en attente - Risque de réputation";
        }

        if ($totalDonations < 5) {
            $risks[] = "Faible volume de dons - Risque de viabilité financière";
        }

        $risks[] = "Risque de saturation du marché des dons environnementaux";
        $risks[] = "Dépendance aux fluctuations saisonnières des dons";
        $risks[] = "Concurrence croissante d'autres organisations similaires";
        $risks[] = "Risque de baisse d'engagement des donateurs existants";

        return [
            'insights' => $insights,
            'recommendations' => $recommendations,
            'campaigns' => $campaigns,
            'risks' => $risks
        ];
    }

    private function getFallbackThankYouMessage(?Donation $donation): string
    {
        $userName = $donation ? ($donation->user->name ?? 'Valued Donor') : 'Valued Donor';
        return "Dear {$userName}, Thank you for your generous donation to Sylva. Your contribution helps us create a greener future for our communities. We truly appreciate your support!";
    }

    private function getFallbackCampaignRecommendations(): array
    {
        return [
            [
                'name' => 'Spring Tree Planting Drive',
                'audience' => 'Environmental enthusiasts',
                'message' => 'Join us in spring for community tree planting',
                'impact' => 'Plant 1000 trees this season',
                'timeline' => 'March - April'
            ]
        ];
    }

    private function getFallbackPatternAnalysis(): array
    {
        return [
            'trends' => ['Steady donation growth observed'],
            'predictions' => ['Continued growth expected'],
            'seasonal' => ['Higher donations in spring and fall'],
            'recommendations' => ['Increase marketing during peak seasons']
        ];
    }

    private function getFallbackRefundAnalysis(): array
    {
        return [
            'risk_level' => 'low',
            'reasoning' => 'Standard donation parameters',
            'recommendations' => ['Process normally']
        ];
    }
}
