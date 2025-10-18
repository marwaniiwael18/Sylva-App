<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageAnalysisController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Analyze an uploaded image and get AI-generated description and suggestions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzeImage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
                'context' => 'nullable|string|max:500',
            ]);

            // Store the uploaded image temporarily
            $image = $request->file('image');
            $tempPath = $image->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);

            // Get additional context if provided
            $context = $request->input('context', '');

            // Analyze the image
            $analysis = $this->geminiService->analyzeImageForReport($fullPath, $context);

            // Clean up temporary file
            Storage::disk('public')->delete($tempPath);

            if (!$analysis['success']) {
                // Check if it's a non-environmental image
                if (isset($analysis['error']) && $analysis['error'] === 'not_environmental') {
                    return response()->json([
                        'success' => false,
                        'error' => 'not_environmental',
                        'message' => $analysis['message'] ?? "This photo is not related to environmental issues. Please upload images showing trees, nature, pollution, green spaces, or environmental concerns."
                    ], 422);
                }
                
                // Check if it's a rate limit error
                if (isset($analysis['error']) && $analysis['error'] === 'rate_limit') {
                    return response()->json([
                        'success' => false,
                        'error' => 'rate_limit',
                        'message' => $analysis['message'] ?? "API rate limit reached. Please wait 60 seconds before trying again."
                    ], 429);
                }
                
                // Check if it's a quota exceeded error
                if (isset($analysis['error']) && $analysis['error'] === 'quota_exceeded') {
                    return response()->json([
                        'success' => false,
                        'error' => 'quota_exceeded',
                        'message' => $analysis['message'] ?? "Daily API quota exceeded. Please try again tomorrow."
                    ], 429);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $analysis['message'] ?? 'Failed to analyze image',
                    'error' => $analysis['error'] ?? 'Unknown error'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image analyzed successfully',
                'data' => [
                    'description' => $analysis['description'],
                    'suggested_type' => $analysis['suggestions']['type'],
                    'suggested_urgency' => $analysis['suggestions']['urgency'],
                    'recommendations' => $analysis['suggestions']['recommendations'],
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Image Analysis Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while analyzing the image',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Analyze multiple images and get combined analysis
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzeMultipleImages(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'images' => 'required|array|min:1|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
                'context' => 'nullable|string|max:500',
            ]);

            $context = $request->input('context', '');
            $tempPaths = [];
            $fullPaths = [];

            // Store all images temporarily
            foreach ($request->file('images') as $image) {
                $tempPath = $image->store('temp', 'public');
                $tempPaths[] = $tempPath;
                $fullPaths[] = storage_path('app/public/' . $tempPath);
            }

            // Analyze all images
            $analysis = $this->geminiService->analyzeMultipleImages($fullPaths, $context);

            // Clean up temporary files
            foreach ($tempPaths as $tempPath) {
                Storage::disk('public')->delete($tempPath);
            }

            if (!$analysis['success']) {
                // Check if any image is non-environmental
                if (isset($analysis['error']) && $analysis['error'] === 'not_environmental') {
                    return response()->json([
                        'success' => false,
                        'error' => 'not_environmental',
                        'message' => $analysis['message'] ?? "One or more photos are not related to environmental issues. Please upload only environment-related images."
                    ], 422);
                }
                
                // Check if it's a rate limit error
                if (isset($analysis['error']) && $analysis['error'] === 'rate_limit') {
                    return response()->json([
                        'success' => false,
                        'error' => 'rate_limit',
                        'message' => $analysis['message'] ?? "API rate limit reached. Please wait 60 seconds before trying again."
                    ], 429);
                }
                
                // Check if it's a quota exceeded error
                if (isset($analysis['error']) && $analysis['error'] === 'quota_exceeded') {
                    return response()->json([
                        'success' => false,
                        'error' => 'quota_exceeded',
                        'message' => $analysis['message'] ?? "Daily API quota exceeded. Please try again tomorrow."
                    ], 429);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $analysis['message'] ?? 'Failed to analyze images',
                    'error' => 'No successful analyses'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Images analyzed successfully',
                'data' => [
                    'description' => $analysis['description'],
                    'suggested_type' => $analysis['suggestions']['type'],
                    'suggested_urgency' => $analysis['suggestions']['urgency'],
                    'recommendations' => $analysis['suggestions']['recommendations'],
                    'individual_analyses' => $analysis['individual_analyses']
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Multiple Image Analysis Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while analyzing the images',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
