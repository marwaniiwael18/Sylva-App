@extends('layouts.app')

@section('title', $forumPost->title . ' - Forum')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('forum.index') }}" 
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                Retour au forum
            </a>
        </div>
    </div>

    <!-- Forum Post -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg mb-8">
            <div class="p-8">
                <!-- Post Header -->
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">{{ $forumPost->author->name }}</p>
                            <p class="text-sm text-gray-500">{{ $forumPost->created_at->format('d M Y à H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($forumPost->relatedEvent)
                            <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-full">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span class="font-medium">{{ $forumPost->relatedEvent->title }}</span>
                            </div>
                        @endif
                        @if($forumPost->author_id === Auth::id())
                            <div class="flex items-center gap-1">
                                <a href="{{ route('forum.edit', $forumPost) }}" 
                                   class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition-colors px-4 py-2 rounded-lg border border-emerald-200 hover:bg-emerald-50">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Modifier
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Post Title -->
                <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $forumPost->title }}</h1>

                <!-- Post Content -->
                <div class="prose max-w-none">
                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $forumPost->content }}</div>
                </div>

                <!-- Post Stats -->
                <div class="flex items-center gap-6 pt-6 mt-6 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-gray-500">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span class="font-medium">{{ $forumPost->comments->count() }}</span>
                        <span>commentaire{{ $forumPost->comments->count() !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <i data-lucide="message-square" class="w-6 h-6 text-emerald-600"></i>
                    Commentaires ({{ $forumPost->comments->count() }})
                </h2>

                <!-- Add Comment Form -->
                @auth
                <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                    <form method="POST" action="{{ route('forum.comments.store', $forumPost) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Ajouter un commentaire
                            </label>
                            <textarea id="content" 
                                      name="content" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('content') border-red-500 @enderror"
                                      placeholder="Partagez votre avis, votre expérience ou posez une question..."
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Publier le commentaire
                            </button>
                        </div>
                    </form>
                </div>
                @endauth

                <!-- Comments List -->
                <div class="space-y-6">
                    @forelse($forumPost->comments as $comment)
                        <div class="border border-gray-200 rounded-xl p-6 bg-white">
                            <!-- Comment Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $comment->author->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if($comment->author_id === Auth::id())
                                    <div class="flex items-center gap-1">
                                        <button onclick="editComment({{ $comment->id }})" 
                                                class="p-2 text-gray-400 hover:text-emerald-600 transition-colors rounded-lg hover:bg-emerald-50"
                                                title="Modifier">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="deleteComment({{ $comment->id }})" 
                                                class="p-2 text-gray-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                                title="Supprimer">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Comment Content -->
                            <div id="comment-content-{{ $comment->id }}" class="text-gray-700 leading-relaxed whitespace-pre-line ml-13">
                                {{ $comment->content }}
                            </div>

                            <!-- Edit Form (hidden by default) -->
                            <div id="edit-form-{{ $comment->id }}" class="hidden ml-13">
                                <form method="POST" action="{{ route('forum.comments.update', $comment) }}">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" 
                                              rows="4"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                              required>{{ $comment->content }}</textarea>
                                    <div class="flex items-center gap-3 mt-4">
                                        <button type="submit" 
                                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                            <i data-lucide="save" class="w-4 h-4"></i>
                                            Enregistrer
                                        </button>
                                        <button type="button" 
                                                onclick="cancelEdit({{ $comment->id }})"
                                                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors px-4 py-2 rounded-lg border border-gray-300">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="message-circle" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun commentaire</h3>
                            <p class="text-gray-600">Soyez le premier à commenter ce post !</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif

<!-- Delete Comment Modal -->
<div id="deleteCommentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-8 h-8 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Supprimer le commentaire</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.</p>
            
            <div class="flex items-center gap-3 justify-center">
                <button onclick="closeDeleteCommentModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                    Annuler
                </button>
                <form id="deleteCommentForm" method="POST" action="" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editComment(commentId) {
    document.getElementById('comment-content-' + commentId).classList.add('hidden');
    document.getElementById('edit-form-' + commentId).classList.remove('hidden');
}

function cancelEdit(commentId) {
    document.getElementById('comment-content-' + commentId).classList.remove('hidden');
    document.getElementById('edit-form-' + commentId).classList.add('hidden');
}

function deleteComment(commentId) {
    const form = document.getElementById('deleteCommentForm');
    form.action = '/comments/' + commentId;
    document.getElementById('deleteCommentModal').classList.remove('hidden');
    document.getElementById('deleteCommentModal').classList.add('flex');
}

function closeDeleteCommentModal() {
    document.getElementById('deleteCommentModal').classList.add('hidden');
    document.getElementById('deleteCommentModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('deleteCommentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteCommentModal();
    }
});
</script>
@endsection