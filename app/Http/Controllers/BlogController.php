<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Event;
use App\Services\HuggingFaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display the blog index page.
     */
    public function index()
    {
        $blogPosts = BlogPost::with(['author', 'relatedEvent', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $events = Event::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.blog.index', compact('blogPosts', 'events'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        $events = Event::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.blog.create', compact('events'));
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => [
                'required',
                'string',
                'min:10',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\'\"\?\!\,\.àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ]+$/'
            ],
            'content' => [
                'required',
                'string',
                'min:50',
                'max:10000',
            ],
            'related_event_id' => 'nullable|exists:events,id',
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'title.min' => 'Le titre doit contenir au moins 10 caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'title.regex' => 'Le titre contient des caractères non autorisés.',
            'content.required' => 'Le contenu est obligatoire.',
            'content.min' => 'Le contenu doit contenir au moins 50 caractères pour être significatif.',
            'content.max' => 'Le contenu ne peut pas dépasser 10,000 caractères.',
            'related_event_id.exists' => 'L\'événement sélectionné n\'existe pas.',
        ]);

        // Additional validation: Check for spam patterns
        $content = $request->content;
        $title = $request->title;
        
        // Block excessive capitalization (spam detection)
        if (preg_match('/[A-Z]{10,}/', $title . $content)) {
            return back()->withErrors(['content' => 'Veuillez éviter d\'écrire tout en majuscules.'])->withInput();
        }
        
        // Block repeated characters (spam detection)
        if (preg_match('/(.)\1{9,}/', $content)) {
            return back()->withErrors(['content' => 'Le contenu contient trop de caractères répétés.'])->withInput();
        }
        
        // Block URLs in excessive amounts (spam detection)
        if (substr_count(strtolower($content), 'http') > 3) {
            return back()->withErrors(['content' => 'Le contenu contient trop de liens. Maximum 3 liens autorisés.'])->withInput();
        }

        BlogPost::create([
            'title' => trim($request->title),
            'content' => trim($request->content),
            'author_id' => Auth::id(),
            'related_event_id' => $request->related_event_id,
        ]);

        return redirect()->route('blog.index')->with('success', 'Article créé avec succès!');
    }

    /**
     * Display the specified blog post.
     */
    public function show(BlogPost $blogPost)
    {
        $blogPost->load(['author', 'relatedEvent', 'comments.author']);
        
        return view('pages.blog.show', compact('blogPost'));
    }

    /**
     * Store a new comment.
     */
    public function storeComment(Request $request, BlogPost $blogPost, HuggingFaceService $huggingFace)
    {
        $request->validate([
            'content' => [
                'required',
                'string',
                'min:3',
                'max:2000',
            ],
        ], [
            'content.required' => 'Le commentaire ne peut pas être vide.',
            'content.min' => 'Le commentaire doit contenir au moins 3 caractères.',
            'content.max' => 'Le commentaire ne peut pas dépasser 2,000 caractères.',
        ]);

        $content = $request->content;
        
        // Anti-spam validations
        // Block excessive capitalization
        if (preg_match('/[A-Z]{15,}/', $content)) {
            return back()->withErrors(['content' => 'Veuillez éviter d\'écrire tout en majuscules.'])->withInput();
        }
        
        // Block repeated characters (spam detection)
        if (preg_match('/(.)\1{9,}/', $content)) {
            return back()->withErrors(['content' => 'Le commentaire contient trop de caractères répétés.'])->withInput();
        }
        
        // Block excessive URLs
        if (substr_count(strtolower($content), 'http') > 2) {
            return back()->withErrors(['content' => 'Le commentaire contient trop de liens. Maximum 2 liens autorisés.'])->withInput();
        }
        
        // Block very short repeated words (spam)
        if (preg_match('/\b(\w+)\s+\1\s+\1\s+\1\b/i', $content)) {
            return back()->withErrors(['content' => 'Le commentaire contient trop de répétitions.'])->withInput();
        }

        // Analyze sentiment
        $sentiment = null;
        $sentimentScore = null;
        
        try {
            $analysis = $huggingFace->analyzeSentiment($content);
            $sentiment = $analysis['label'];
            $sentimentScore = $analysis['score'];
        } catch (\Exception $e) {
            // If sentiment analysis fails, just continue without it
            \Log::warning('Sentiment analysis failed: ' . $e->getMessage());
        }

        Comment::create([
            'content' => trim($content),
            'author_id' => Auth::id(),
            'forum_post_id' => $blogPost->id,
            'sentiment' => $sentiment,
            'sentiment_score' => $sentimentScore,
        ]);

        return redirect()->route('blog.show', $blogPost)->with('success', 'Commentaire ajouté avec succès!');
    }

    /**
     * Show the form for editing a blog post.
     */
    public function edit(BlogPost $blogPost)
    {
        // Check if user owns the post
        if ($blogPost->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres articles.');
        }

        $events = Event::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.blog.edit', compact('blogPost', 'events'));
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        // Check if user owns the post
        if ($blogPost->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres articles.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'related_event_id' => 'nullable|exists:events,id',
        ]);

        $blogPost->update([
            'title' => $request->title,
            'content' => $request->content,
            'related_event_id' => $request->related_event_id,
        ]);

        return redirect()->route('blog.show', $blogPost)->with('success', 'Article modifié avec succès!');
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(BlogPost $blogPost)
    {
        // Check if user owns the post
        if ($blogPost->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres articles.');
        }

        $blogPost->delete();

        return redirect()->route('blog.index')->with('success', 'Article supprimé avec succès!');
    }

    /**
     * Update the specified comment.
     */
    public function updateComment(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres commentaires.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return redirect()->route('blog.show', $comment->forum_post_id)->with('success', 'Commentaire modifié avec succès!');
    }

    /**
     * Remove the specified comment.
     */
    public function destroyComment(Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres commentaires.');
        }

        $blogPostId = $comment->forum_post_id;
        $comment->delete();

        return redirect()->route('blog.show', $blogPostId)->with('success', 'Commentaire supprimé avec succès!');
    }

    /**
     * Filter posts by event.
     */
    public function filterByEvent(Request $request)
    {
        $eventId = $request->get('event_id');
        
        $query = BlogPost::with(['author', 'relatedEvent', 'comments']);
        
        if ($eventId) {
            $query->where('related_event_id', $eventId);
        }
        
        $blogPosts = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $events = Event::where('status', 'active')
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.blog.index', compact('blogPosts', 'events', 'eventId'));
    }
}
