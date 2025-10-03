@extends('layouts.dashboard')

@section('title', 'Modifier le Post - Forum')

@section('page-content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('forum.show', $forumPost) }}" 
               class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour au post
            </a>
        </div>
        <div class="mt-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-5 h-5 text-white"></i>
                </div>
                Modifier le post
            </h1>
            <p class="mt-1 text-sm text-gray-600">Modifiez votre post pour améliorer son contenu</p>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
            <form method="POST" action="{{ route('forum.update', $forumPost) }}" class="p-8">
                @csrf
                @method('PUT')
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre du post *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $forumPost->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('title') border-red-500 @enderror"
                           placeholder="Ex: Comment entretenir les oliviers plantés la semaine dernière ?"
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Related Event -->
                <div class="mb-6">
                    <label for="related_event_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Événement associé (optionnel)
                    </label>
                    <select id="related_event_id" 
                            name="related_event_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('related_event_id') border-red-500 @enderror">
                        <option value="">Aucun événement associé</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('related_event_id', $forumPost->related_event_id) == $event->id ? 'selected' : '' }}>
                                {{ $event->title }} - {{ $event->created_at->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('related_event_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-8">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenu *
                    </label>
                    <textarea id="content" 
                              name="content" 
                              rows="12"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('content') border-red-500 @enderror"
                              placeholder="Décrivez votre question, partagez votre expérience ou lancez une discussion..."
                              required>{{ old('content', $forumPost->content) }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('forum.show', $forumPost) }}" 
                           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors font-medium">
                            <i data-lucide="x" class="w-5 h-5"></i>
                            Annuler
                        </a>
                        
                        <!-- Delete Button -->
                        <button type="button" 
                                onclick="confirmDelete()"
                                class="inline-flex items-center gap-2 text-red-600 hover:text-red-800 transition-colors font-medium">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                            Supprimer le post
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-colors shadow-lg">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-8 h-8 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Supprimer le post</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce post ? Cette action est irréversible et supprimera également tous les commentaires associés.</p>
            
            <div class="flex items-center gap-3 justify-center">
                <button onclick="closeDeleteModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                    Annuler
                </button>
                <form method="POST" action="{{ route('forum.destroy', $forumPost) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif
@endsection