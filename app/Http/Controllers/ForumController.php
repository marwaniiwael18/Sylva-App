<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Display the forum index page.
     */
    public function index()
    {
        $forumPosts = ForumPost::with(['author', 'relatedEvent', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $events = Report::where('status', 'validated')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.forum.index', compact('forumPosts', 'events'));
    }

    /**
     * Show the form for creating a new forum post.
     */
    public function create()
    {
        $events = Report::where('status', 'validated')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.forum.create', compact('events'));
    }

    /**
     * Store a newly created forum post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'related_event_id' => 'nullable|exists:reports,id',
        ]);

        ForumPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => Auth::id(),
            'related_event_id' => $request->related_event_id,
        ]);

        return redirect()->route('forum.index')->with('success', 'Post créé avec succès!');
    }

    /**
     * Display the specified forum post.
     */
    public function show(ForumPost $forumPost)
    {
        $forumPost->load(['author', 'relatedEvent', 'comments.author']);
        
        return view('pages.forum.show', compact('forumPost'));
    }

    /**
     * Store a new comment.
     */
    public function storeComment(Request $request, ForumPost $forumPost)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        Comment::create([
            'content' => $request->content,
            'author_id' => Auth::id(),
            'forum_post_id' => $forumPost->id,
        ]);

        return redirect()->route('forum.show', $forumPost)->with('success', 'Commentaire ajouté avec succès!');
    }

    /**
     * Show the form for editing a forum post.
     */
    public function edit(ForumPost $forumPost)
    {
        // Check if user owns the post
        if ($forumPost->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres posts.');
        }

        $events = Report::where('status', 'validated')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.forum.edit', compact('forumPost', 'events'));
    }

    /**
     * Update the specified forum post.
     */
    public function update(Request $request, ForumPost $forumPost)
    {
        // Check if user owns the post
        if ($forumPost->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres posts.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'related_event_id' => 'nullable|exists:reports,id',
        ]);

        $forumPost->update([
            'title' => $request->title,
            'content' => $request->content,
            'related_event_id' => $request->related_event_id,
        ]);

        return redirect()->route('forum.show', $forumPost)->with('success', 'Post modifié avec succès!');
    }

    /**
     * Remove the specified forum post.
     */
    public function destroy(ForumPost $forumPost)
    {
        // Check if user owns the post
        if ($forumPost->author_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres posts.');
        }

        $forumPost->delete();

        return redirect()->route('forum.index')->with('success', 'Post supprimé avec succès!');
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

        return redirect()->route('forum.show', $comment->forum_post_id)->with('success', 'Commentaire modifié avec succès!');
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

        $forumPostId = $comment->forum_post_id;
        $comment->delete();

        return redirect()->route('forum.show', $forumPostId)->with('success', 'Commentaire supprimé avec succès!');
    }

    /**
     * Filter posts by event.
     */
    public function filterByEvent(Request $request)
    {
        $eventId = $request->get('event_id');
        
        $query = ForumPost::with(['author', 'relatedEvent', 'comments']);
        
        if ($eventId) {
            $query->where('related_event_id', $eventId);
        }
        
        $forumPosts = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $events = Report::where('status', 'validated')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.forum.index', compact('forumPosts', 'events', 'eventId'));
    }
}
