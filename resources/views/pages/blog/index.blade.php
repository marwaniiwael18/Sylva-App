@extends('layouts.dashboard')

@section('title', 'Blog - Sylva')
@section('page-title', 'Blog Communautaire')
@section('page-subtitle', 'Partagez vos expériences, posez vos questions et échangez avec la communauté')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('blog.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all transform hover:scale-105">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Nouvel Article
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('blog.filter.event') }}" class="flex items-center gap-4">
            <div class="flex-1">
                <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Filtrer par événement
                </label>
                <select id="event_id" 
                        name="event_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        onchange="this.form.submit()">
                    <option value="">Tous les événements</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ (request('event_id') ?? $eventId ?? '') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }} - {{ $event->created_at->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('event_id'))
                <div class="flex items-end">
                    <a href="{{ route('blog.index') }}" 
                       class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Effacer le filtre
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Blog Posts -->
    <div class="space-y-6">
        @forelse($blogPosts as $post)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                <div class="p-6">
                    <!-- Post Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center">
                                <i data-lucide="user" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $post->author?->name ?? 'Utilisateur supprimé' }}</p>
                                <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($post->relatedEvent)
                                <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                    <span class="text-sm font-medium">{{ $post->relatedEvent->title }}</span>
                                </div>
                            @endif
                            @if($post->author_id === Auth::id())
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('blog.edit', $post) }}" 
                                       class="p-2 text-gray-400 hover:text-emerald-600 transition-colors rounded-lg hover:bg-emerald-50"
                                       title="Modifier">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Post Title -->
                    <h2 class="text-xl font-bold text-gray-900 mb-3 hover:text-emerald-600 transition-colors">
                        <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                    </h2>

                    <!-- Post Preview -->
                    <div class="text-gray-700 mb-4">
                        <p class="line-clamp-3">{{ Str::limit($post->content, 200) }}</p>
                    </div>

                    <!-- Post Stats -->
                    <div class="flex items-center gap-6 pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-gray-500">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            <span class="font-medium">{{ $post->comments->count() }}</span>
                            <span>commentaire{{ $post->comments->count() !== 1 ? 's' : '' }}</span>
                        </div>
                        <a href="{{ route('blog.show', $post) }}" 
                           class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
                            Lire la suite →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="message-square" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun article pour le moment</h3>
                <p class="text-gray-600 mb-6">Soyez le premier à partager quelque chose avec la communauté !</p>
                <a href="{{ route('blog.create') }}" 
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Créer le premier article
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($blogPosts->hasPages())
        <div class="mt-8">
            {{ $blogPosts->links() }}
        </div>
    @endif
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif
@endsection
