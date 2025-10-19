<?php

namespace App\Http\Controllers;

use App\Services\HuggingFaceService;
use App\Models\BlogPost;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogAIController extends Controller
{
    protected $huggingFace;

    public function __construct(HuggingFaceService $huggingFace)
    {
        $this->huggingFace = $huggingFace;
    }

    /**
     * Show AI blog generator page
     */
    public function create()
    {
        $events = Event::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.blog.ai-create', compact('events'));
    }

    /**
     * Generate blog content using AI
     */
    public function generate(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'tone' => 'nullable|string|in:professional,casual,informative,enthusiastic',
            'length' => 'nullable|string|in:short,medium,long',
        ]);

        try {
            $topic = $request->input('topic');
            $tone = $request->input('tone', 'informative');
            $length = $request->input('length', 'medium');

            // Build a more detailed prompt
            $lengthMap = [
                'short' => 'Write a concise blog post (around 150 words)',
                'medium' => 'Write a detailed blog post (around 300 words)',
                'long' => 'Write an in-depth blog post (around 500 words)',
            ];

            $prompt = "{$lengthMap[$length]} about: {$topic}. Use a {$tone} tone. Focus on environmental sustainability and tree conservation if relevant.";

            $generatedContent = $this->huggingFace->generateText($prompt);

            // Generate a title from the topic
            $titlePrompt = "Generate a catchy, SEO-friendly blog title for: {$topic}";
            $generatedTitle = $this->huggingFace->generateText($titlePrompt);

            return response()->json([
                'success' => true,
                'title' => trim($generatedTitle),
                'content' => trim($generatedContent),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store AI-generated blog post
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'related_event_id' => 'nullable|exists:events,id',
        ]);

        $blogPost = BlogPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => Auth::id(),
            'related_event_id' => $request->related_event_id,
        ]);

        return redirect()->route('blog.show', $blogPost)->with('success', 'Article généré par IA créé avec succès!');
    }

    /**
     * Improve existing content with AI
     */
    public function improve(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'instruction' => 'required|string|in:shorten,expand,simplify,formalize',
        ]);

        try {
            $content = $request->input('content');
            $instruction = $request->input('instruction');

            $instructionMap = [
                'shorten' => 'Shorten this text while keeping the main ideas:',
                'expand' => 'Expand this text with more details and examples:',
                'simplify' => 'Simplify this text to make it easier to understand:',
                'formalize' => 'Make this text more formal and professional:',
            ];

            $prompt = "{$instructionMap[$instruction]} {$content}";
            $improvedContent = $this->huggingFace->generateText($prompt);

            return response()->json([
                'success' => true,
                'content' => trim($improvedContent),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'amélioration: ' . $e->getMessage(),
            ], 500);
        }
    }
}
