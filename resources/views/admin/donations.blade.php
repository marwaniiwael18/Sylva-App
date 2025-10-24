@extends('layouts.admin')

@section('title', 'Gestion Donations - Admin')
@section('page-title', 'Gestion des Donations')
@section('page-subtitle', 'Superviser et gérer les donations de la plateforme')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Gestion des Donations</h1>
                    <p class="text-gray-400 mt-2">Supervisez et gérez les donations de votre plateforme</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-gray-800 px-4 py-2 rounded-lg border border-gray-700">
                        <div class="text-sm text-gray-400">Dernière mise à jour</div>
                        <div class="text-white font-medium">{{ now()->format('H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
        <!-- Key Metrics Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Amount -->
            <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Montant Total</p>
                        <p class="text-white text-3xl font-bold mt-2">{{ number_format($totalAmount ?? 0, 2) }}€</p>
                        <div class="flex items-center mt-2">
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-200 mr-1"></i>
                            <span class="text-green-200 text-xs">+12% ce mois</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="euro" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Total Donations -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Donations</p>
                        <p class="text-white text-3xl font-bold mt-2">{{ number_format($totalDonations ?? 0) }}</p>
                        <div class="flex items-center mt-2">
                            <i data-lucide="users" class="w-4 h-4 text-blue-200 mr-1"></i>
                            <span class="text-blue-200 text-xs">{{ number_format($totalDonations ?? 0) }} donateurs</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="heart" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Donations -->
            <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">En Attente</p>
                        <p class="text-white text-3xl font-bold mt-2">{{ number_format($pendingDonations ?? 0) }}</p>
                        <div class="flex items-center mt-2">
                            <i data-lucide="clock" class="w-4 h-4 text-yellow-200 mr-1"></i>
                            <span class="text-yellow-200 text-xs">À traiter</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Monthly Growth -->
            <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Ce Mois</p>
                        <p class="text-white text-3xl font-bold mt-2">{{ number_format($monthlyAmount ?? 0, 2) }}€</p>
                        <div class="flex items-center mt-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-purple-200 mr-1"></i>
                            <span class="text-purple-200 text-xs">{{ now()->format('M Y') }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Refunds Overview -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Remboursements</h3>
                    <i data-lucide="refresh-cw" class="w-5 h-5 text-gray-400"></i>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Total</span>
                        <span class="text-white font-medium">{{ number_format($totalRefunds ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">En attente</span>
                        <span class="text-orange-400 font-medium">{{ number_format($pendingRefunds ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Complétés</span>
                        <span class="text-green-400 font-medium">{{ number_format($completedRefunds ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Actions Rapides</h3>
                    <i data-lucide="zap" class="w-5 h-5 text-gray-400"></i>
                </div>
                <div class="space-y-3">
                    <button onclick="generateCampaignRecommendations()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                        <i data-lucide="lightbulb" class="w-4 h-4 mr-2"></i>
                        Générer Campagnes
                    </button>
                    <button onclick="exportDonations()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Exporter Données
                    </button>
                </div>
            </div>

            <!-- AI Insights Preview -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i data-lucide="brain" class="w-5 h-5 mr-2 text-purple-400"></i>
                        IA Insights
                    </h3>
                    <span class="bg-purple-600 text-xs px-2 py-1 rounded-full text-white">ACTIF</span>
                </div>
                <div class="space-y-2">
                    @if(isset($aiInsights) && !empty($aiInsights) && isset($aiInsights['insights']) && count($aiInsights['insights']) > 0)
                        <p class="text-gray-400 text-sm">{{ $aiInsights['insights'][0] }}</p>
                    @else
                        <p class="text-gray-400 text-sm">Cliquez ci-dessous pour analyser vos données de donations avec l'IA.</p>
                    @endif
                    <div class="pt-2">
                        <button onclick="openAIInsightsModal()" class="text-purple-400 hover:text-purple-300 text-sm flex items-center">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Voir les insights IA
                            <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Filtres et Recherche</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-400 text-sm">{{ $donations->total() ?? 0 }} résultats</span>
                </div>
            </div>

            <form method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Rechercher par nom, email ou projet..."
                               class="w-full pl-10 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors">
                    </div>
                </div>

                <div class="flex gap-4">
                    <select name="status" class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        <option value="">Tous les statuts</option>
                        <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>Réussi</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoué</option>
                    </select>

                    <select name="type" class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        <option value="">Tous les types</option>
                        <option value="tree_planting" {{ request('type') === 'tree_planting' ? 'selected' : '' }}>Plantation d'arbres</option>
                        <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="awareness" {{ request('type') === 'awareness' ? 'selected' : '' }}>Sensibilisation</option>
                    </select>

                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors flex items-center">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Rechercher
                    </button>

                    @if(request('search') || request('status') || request('type'))
                    <a href="{{ route('admin.donations') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-colors flex items-center">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Effacer
                    </a>
                    @endif
                </div>
            </form>
        </div>

    <!-- Refunds Management Section -->
    @if(isset($pendingRefunds) && $pendingRefunds > 0)
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-yellow-400"></i>
                Remboursements en Attente ({{ $pendingRefunds }})
            </h3>
            <span class="bg-yellow-600 text-xs px-3 py-1 rounded-full text-white">ACTION REQUISE</span>
        </div>

        <div class="space-y-4">
            @foreach($pendingRefundRecords ?? [] as $refund)
            <div class="bg-gray-700 rounded-lg p-4 border border-gray-600" data-refund-id="{{ $refund->id }}">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div>
                                <h4 class="text-white font-medium">{{ $refund->donation->user->name ?? 'Anonyme' }}</h4>
                                <p class="text-gray-400 text-sm">{{ $refund->donation->user->email ?? '' }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-red-400">{{ number_format($refund->amount, 2) }}€</div>
                                <p class="text-gray-400 text-sm">sur {{ number_format($refund->donation->amount, 2) }}€</p>
                            </div>
                            <div>
                                <p class="text-white text-sm">{{ $refund->donation->event->title ?? 'Général' }}</p>
                                <p class="text-gray-400 text-xs">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @if($refund->reason)
                        <div class="mt-3 p-3 bg-gray-600 rounded-lg">
                            <p class="text-gray-300 text-sm"><strong>Raison:</strong> {{ $refund->reason }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3 ml-4">
                        <button onclick="approveRefund({{ $refund->id }})"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors flex items-center">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                            Approuver
                        </button>
                        <button onclick="rejectRefund({{ $refund->id }})"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors flex items-center">
                            <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                            Rejeter
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Liste des donations -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Liste des Donations</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Donateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Projet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Remboursements</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($donations ?? [] as $donation)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $donation->user->name ?? 'Anonyme' }}</div>
                            <div class="text-sm text-gray-400">{{ $donation->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-green-400">{{ number_format($donation->amount, 2) }}€</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-white">{{ $donation->event->title ?? 'Général' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($donation->payment_status === 'succeeded') bg-green-900 text-green-200
                                @elseif($donation->payment_status === 'pending') bg-yellow-900 text-yellow-200
                                @else bg-red-900 text-red-200
                                @endif">
                                @if($donation->payment_status === 'succeeded')
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Réussi
                                @elseif($donation->payment_status === 'pending')
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    En attente
                                @else
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    Échoué
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($donation->refunds->count() > 0)
                                <div class="space-y-1">
                                    @foreach($donation->refunds as $refund)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                        @if($refund->status === 'completed') bg-green-900 text-green-200
                                        @elseif($refund->status === 'pending') bg-yellow-900 text-yellow-200
                                        @elseif($refund->status === 'processing') bg-blue-900 text-blue-200
                                        @else bg-red-900 text-red-200
                                        @endif">
                                        {{ number_format($refund->amount, 2) }}€ - {{ $refund->status_name }}
                                    </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500 text-sm">Aucun</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $donation->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if($donation->payment_status === 'succeeded' && $donation->canRefund())
                                <button onclick="openRefundModal({{ $donation->id }}, {{ $donation->getTotalRefundableAmount() }})" 
                                        class="text-red-400 hover:text-red-300 transition-colors"
                                        title="Demander remboursement">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                </button>
                                @endif
                                
                                <button onclick="generateThankYou({{ $donation->id }})" 
                                        class="text-purple-400 hover:text-purple-300 transition-colors"
                                        title="Générer message de remerciement">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                </button>
                                
                                @if($donation->payment_status === 'succeeded')
                                <button onclick="analyzeRefundRisk({{ $donation->id }})" 
                                        class="text-orange-400 hover:text-orange-300 transition-colors"
                                        title="Analyser risque de remboursement">
                                    <i data-lucide="shield" class="w-4 h-4"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <i data-lucide="heart" class="w-12 h-12 mx-auto mb-3 text-gray-600"></i>
                            <p>Aucune donation trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($donations) && $donations->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $donations->links() }}
        </div>
        @endif
    </div>
</div>

<!-- AI Insights Section - Hidden on page, shown in modal -->
@if(isset($aiInsights) && !empty($aiInsights))
<div id="ai-section" class="hidden">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-white flex items-center">
            <i data-lucide="brain" class="w-6 h-6 mr-3 text-purple-400"></i>
            Insights IA Détaillés
        </h3>
        <span class="bg-purple-600 text-xs px-3 py-1 rounded-full text-white">ANALYSE COMPLÈTE</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Insights -->
        <div class="space-y-4">
            <h4 class="text-lg font-medium text-white">Analyse des Données</h4>
            @if(isset($aiInsights['insights']) && is_array($aiInsights['insights']))
                @foreach($aiInsights['insights'] as $insight)
                <div class="bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-300">{{ $insight }}</p>
                </div>
                @endforeach
            @else
                <div class="bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-300">Aucune analyse détaillée disponible pour le moment.</p>
                </div>
            @endif
        </div>

        <!-- Recommendations -->
        <div class="space-y-4">
            <h4 class="text-lg font-medium text-white">Recommandations</h4>
            @if(isset($aiInsights['recommendations']) && is_array($aiInsights['recommendations']))
                @foreach($aiInsights['recommendations'] as $recommendation)
                <div class="bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-300">{{ $recommendation }}</p>
                </div>
                @endforeach
            @else
                <div class="bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-300">Aucune recommandation disponible.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Additional AI Data -->
    @if(isset($aiInsights['campaigns']) || isset($aiInsights['risks']))
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(isset($aiInsights['campaigns']) && is_array($aiInsights['campaigns']))
        <div class="space-y-4">
            <h4 class="text-lg font-medium text-white">Campagnes Suggérées</h4>
            @foreach($aiInsights['campaigns'] as $campaign)
            <div class="bg-gray-700 rounded-lg p-4">
                @if(is_array($campaign))
                    <h5 class="text-white font-medium">{{ $campaign['name'] ?? 'Campagne' }}</h5>
                    <p class="text-gray-300 text-sm">{{ $campaign['message'] ?? '' }}</p>
                    <div class="mt-2 text-xs text-gray-400">
                        <span>Audience: {{ $campaign['audience'] ?? 'N/A' }}</span><br>
                        <span>Impact: {{ $campaign['impact'] ?? 'N/A' }}</span><br>
                        <span>Timeline: {{ $campaign['timeline'] ?? 'N/A' }}</span>
                    </div>
                @else
                    <p class="text-gray-300">{{ $campaign }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        @if(isset($aiInsights['risks']) && is_array($aiInsights['risks']))
        <div class="space-y-4">
            <h4 class="text-lg font-medium text-white">Risques Identifiés</h4>
            @foreach($aiInsights['risks'] as $risk)
            <div class="bg-gray-700 rounded-lg p-4">
                <p class="text-gray-300">{{ $risk }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif
</div>
@endif

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Demander un remboursement</h3>
                <button onclick="closeRefundModal()" class="text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="refundForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Montant à rembourser (€)</label>
                        <input type="number" id="refundAmount" step="0.01" min="0.01" 
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <p class="text-xs text-gray-400 mt-1">Maximum: <span id="maxRefundAmount"></span>€</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Raison du remboursement</label>
                        <textarea id="refundReason" rows="3" 
                                  class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                                  placeholder="Expliquez la raison du remboursement..."></textarea>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeRefundModal()" 
                            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Demander remboursement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Campaign Recommendations Modal -->
<div id="campaignModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="closeCampaignModal()">
    <div class="flex items-center justify-center min-h-screen p-4" onclick="event.stopPropagation()">
        <div class="bg-gray-800 rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <h3 class="text-lg font-semibold text-white">Recommandations de Campagnes IA</h3>
                <button onclick="closeCampaignModal()" class="text-gray-400 hover:text-white flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div id="campaignContent" class="space-y-4 flex-1 overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t border-gray-700 flex-shrink-0">
                <button onclick="closeCampaignModal()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Thank You Message Modal -->
<div id="thankYouModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl max-w-lg w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i data-lucide="message-circle" class="w-5 h-5 mr-2 text-green-400"></i>
                    Message de Remerciement
                </h3>
                <button onclick="closeThankYouModal()" class="text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="thankYouContent" class="text-gray-300 leading-relaxed">
                <!-- Content will be loaded here -->
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeThankYouModal()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Risk Analysis Modal -->
<div id="riskAnalysisModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i data-lucide="shield" class="w-5 h-5 mr-2 text-orange-400"></i>
                    Analyse du Risque de Remboursement
                </h3>
                <button onclick="closeRiskAnalysisModal()" class="text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="riskAnalysisContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeRiskAnalysisModal()" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Insights Modal -->
<div id="aiInsightsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl max-w-4xl w-full p-6 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i data-lucide="brain" class="w-5 h-5 mr-2 text-purple-400"></i>
                    Insights IA Détaillés
                </h3>
                <button onclick="closeAIInsightsModal()" class="text-gray-400 hover:text-white flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div id="aiInsightsContent" class="space-y-6 flex-1 overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t border-gray-700 flex-shrink-0">
                <button onclick="closeAIInsightsModal()"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="notificationTitle" class="text-lg font-semibold text-white flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2 text-blue-400"></i>
                    Notification
                </h3>
                <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="notificationContent" class="text-gray-300 leading-relaxed">
                <!-- Content will be loaded here -->
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeNotificationModal()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDonationId = null;

function openRefundModal(donationId, maxAmount) {
    currentDonationId = donationId;
    document.getElementById('maxRefundAmount').textContent = maxAmount.toFixed(2);
    document.getElementById('refundAmount').max = maxAmount;
    document.getElementById('refundAmount').value = maxAmount;
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundForm').reset();
    currentDonationId = null;
}

function openCampaignModal() {
    console.log('Opening campaign modal');
    const modal = document.getElementById('campaignModal');
    if (modal) {
        modal.classList.remove('hidden');
        // Focus the close button for accessibility
        const closeButton = modal.querySelector('button[onclick="closeCampaignModal()"]');
        if (closeButton) {
            closeButton.focus();
        }
        console.log('Campaign modal opened');
    } else {
        console.error('Campaign modal element not found');
    }
}

function closeCampaignModal() {
    console.log('Closing campaign modal');
    const modal = document.getElementById('campaignModal');
    if (modal) {
        modal.classList.add('hidden');
        // Reset content when closing
        const content = document.getElementById('campaignContent');
        if (content) {
            content.innerHTML = '';
        }
        console.log('Campaign modal closed and content reset');
    } else {
        console.error('Campaign modal element not found');
    }
}

// AI Insights Modal Functions
function openAIInsightsModal() {
    console.log('Opening AI insights modal');
    const modal = document.getElementById('aiInsightsModal');
    if (modal) {
        modal.classList.remove('hidden');
        loadAIInsights();
        // Focus the close button for accessibility
        const closeButton = modal.querySelector('button[onclick="closeAIInsightsModal()"]');
        if (closeButton) {
            closeButton.focus();
        }
        console.log('AI insights modal opened');
    } else {
        console.error('AI insights modal element not found');
    }
}

function closeAIInsightsModal() {
    console.log('Closing AI insights modal');
    const modal = document.getElementById('aiInsightsModal');
    if (modal) {
        modal.classList.add('hidden');
        console.log('AI insights modal closed');
    } else {
        console.error('AI insights modal element not found');
    }
}

async function loadAIInsights() {
    const content = document.getElementById('aiInsightsContent');
    if (!content) return;

    content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto"></div><p class="text-gray-400 mt-2">Chargement des insights IA...</p></div>';

    try {
        const response = await fetch('/admin/donations?show_ai=1', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const html = await response.text();
            // Extract the AI insights section from the HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const aiSection = doc.getElementById('ai-section');

            if (aiSection) {
                content.innerHTML = aiSection.innerHTML;
            } else {
                content.innerHTML = '<div class="text-center py-8 text-red-400">Aucun insight IA disponible pour le moment.</div>';
            }
        } else {
            content.innerHTML = '<div class="text-center py-8 text-red-400">Erreur lors du chargement des insights IA.</div>';
        }
    } catch (error) {
        console.error('Error loading AI insights:', error);
        content.innerHTML = '<div class="text-center py-8 text-red-400">Erreur lors du chargement des insights IA.</div>';
    }
}

// Add keyboard support for closing modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCampaignModal();
        closeRefundModal();
        closeAIInsightsModal();
        closeThankYouModal();
        closeRiskAnalysisModal();
        closeNotificationModal();
    }
});

// Handle refund form submission
document.getElementById('refundForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('refundAmount').value);
    const reason = document.getElementById('refundReason').value;
    
    if (!amount || !reason.trim()) {
        showNotification('Veuillez remplir tous les champs.', 'Erreur de validation', 'error');
        return;
    }
    
    try {
        const response = await fetch(`/admin/donations/${currentDonationId}/refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ amount, reason })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Demande de remboursement créée avec succès!', 'Succès', 'success');
            closeRefundModal();
            location.reload();
        } else {
            showNotification('Erreur: ' + result.message, 'Erreur', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de la création du remboursement.', 'Erreur', 'error');
        console.error(error);
    }
});

// Generate thank you message
async function generateThankYou(donationId) {
    try {
        const response = await fetch(`/admin/donations/${donationId}/thank-you`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('thankYouContent').innerHTML = result.message.replace(/\n/g, '<br>');
            document.getElementById('thankYouModal').classList.remove('hidden');
        } else {
            // Show error in modal instead of alert
            document.getElementById('thankYouContent').innerHTML = `<div class="text-red-400"><strong>Erreur:</strong> ${result.message}</div>`;
            document.getElementById('thankYouModal').classList.remove('hidden');
        }
    } catch (error) {
        // Show error in modal instead of alert
        document.getElementById('thankYouContent').innerHTML = '<div class="text-red-400"><strong>Erreur:</strong> Impossible de générer le message de remerciement.</div>';
        document.getElementById('thankYouModal').classList.remove('hidden');
        console.error(error);
    }
}

function closeThankYouModal() {
    document.getElementById('thankYouModal').classList.add('hidden');
}

// Analyze refund risk
async function analyzeRefundRisk(donationId) {
    try {
        const response = await fetch(`/admin/donations/${donationId}/analyze-risk`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            const analysis = result.analysis;
            const content = document.getElementById('riskAnalysisContent');
            content.innerHTML = `
                <div class="bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <span class="text-lg font-semibold text-white mr-2">Niveau de Risque:</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            ${analysis.risk_level === 'low' ? 'bg-green-900 text-green-200' : 
                              analysis.risk_level === 'medium' ? 'bg-yellow-900 text-yellow-200' : 
                              'bg-red-900 text-red-200'}">
                            ${analysis.risk_level.toUpperCase()}
                        </span>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-white font-medium mb-2">Raison:</h4>
                        <p class="text-gray-300">${analysis.reasoning}</p>
                    </div>
                    <div>
                        <h4 class="text-white font-medium mb-2">Recommandations:</h4>
                        <ul class="text-gray-300 space-y-1">
                            ${analysis.recommendations.map(rec => `<li>• ${rec}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
            document.getElementById('riskAnalysisModal').classList.remove('hidden');
        } else {
            // Show error in modal instead of alert
            const content = document.getElementById('riskAnalysisContent');
            content.innerHTML = `
                <div class="bg-red-900 border border-red-700 rounded-lg p-4">
                    <div class="text-red-200">
                        <strong>Erreur lors de l'analyse:</strong> ${result.message}
                    </div>
                </div>
            `;
            document.getElementById('riskAnalysisModal').classList.remove('hidden');
        }
    } catch (error) {
        // Show error in modal instead of alert
        const content = document.getElementById('riskAnalysisContent');
        content.innerHTML = `
            <div class="bg-red-900 border border-red-700 rounded-lg p-4">
                <div class="text-red-200">
                    <strong>Erreur:</strong> Impossible d'analyser le risque de remboursement.
                </div>
            </div>
        `;
        document.getElementById('riskAnalysisModal').classList.remove('hidden');
        console.error(error);
    }
}

function closeRiskAnalysisModal() {
    document.getElementById('riskAnalysisModal').classList.add('hidden');
}

// Notification modal functions
function showNotification(message, title = 'Notification', type = 'info') {
    const titleElement = document.getElementById('notificationTitle');
    const contentElement = document.getElementById('notificationContent');
    
    titleElement.textContent = title;
    contentElement.innerHTML = message;
    
    // Set icon based on type
    const iconClass = type === 'success' ? 'text-green-400' : 
                     type === 'error' ? 'text-red-400' : 
                     type === 'warning' ? 'text-yellow-400' : 'text-blue-400';
    
    titleElement.innerHTML = `<i data-lucide="info" class="w-5 h-5 mr-2 ${iconClass}"></i>${title}`;
    
    document.getElementById('notificationModal').classList.remove('hidden');
}

function closeNotificationModal() {
    document.getElementById('notificationModal').classList.add('hidden');
}

// Generate campaign recommendations
async function generateCampaignRecommendations() {
    console.log('Starting campaign recommendations generation');
    openCampaignModal();

    const content = document.getElementById('campaignContent');
    if (!content) {
        console.error('Campaign content element not found');
        return;
    }

    content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto"></div><p class="text-gray-400 mt-2">Génération des recommandations...</p></div>';

    try {
        console.log('Making API call to campaign recommendations');
        const response = await fetch('/admin/donations/campaign-recommendations', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        console.log('API response status:', response.status);
        const result = await response.json();
        console.log('API response:', result);

        if (result.success) {
            console.log('Generating HTML for', result.recommendations.length, 'recommendations');
            let html = '';
            result.recommendations.forEach((campaign, index) => {
                html += `
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-white mb-2">${campaign.name || `Campagne ${index + 1}`}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-400">Audience cible:</span>
                                <p class="text-white">${campaign.audience || 'Non spécifié'}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Message clé:</span>
                                <p class="text-white">${campaign.message || 'Non spécifié'}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Impact attendu:</span>
                                <p class="text-white">${campaign.impact || 'Non spécifié'}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Timeline:</span>
                                <div class="text-white text-xs">
                                    ${(() => {
                                        if (typeof campaign.timeline === 'object' && campaign.timeline) {
                                            let timelineHtml = `<p><strong>${campaign.timeline.start_date || 'N/A'} - ${campaign.timeline.end_date || 'N/A'}</strong></p>`;
                                            if (campaign.timeline.key_dates && Array.isArray(campaign.timeline.key_dates)) {
                                                timelineHtml += '<ul class="mt-1 space-y-1">';
                                                campaign.timeline.key_dates.forEach(date => {
                                                    timelineHtml += `<li>• ${date}</li>`;
                                                });
                                                timelineHtml += '</ul>';
                                            }
                                            return timelineHtml;
                                        } else {
                                            return `<p>${campaign.timeline || 'Non spécifié'}</p>`;
                                        }
                                    })()}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            content.innerHTML = html;
            console.log('Campaign recommendations loaded successfully');
        } else {
            console.error('API returned error:', result.message);
            content.innerHTML = '<div class="text-center py-8 text-red-400">Erreur lors de la génération des recommandations. <button onclick="closeCampaignModal()" class="underline">Fermer</button></div>';
        }
    } catch (error) {
        console.error('Error generating campaign recommendations:', error);
        const content = document.getElementById('campaignContent');
        if (content) {
            content.innerHTML = '<div class="text-center py-8 text-red-400">Erreur lors de la génération des recommandations. <button onclick="closeCampaignModal()" class="underline">Fermer</button></div>';
        }
    }
}

// Helper functions for updating refund UI
function removeRefundFromUI(refundId) {
    // Find and remove the refund item from the pending refunds section
    const refundElement = document.querySelector(`[data-refund-id="${refundId}"]`);
    if (refundElement) {
        refundElement.remove();
    }
}

function updatePendingRefundsCount() {
    // Update the pending refunds count in the UI
    const pendingRefundsSection = document.querySelector('.bg-gray-800.border.border-gray-700.rounded-xl.p-6.mb-8');
    if (pendingRefundsSection) {
        const refundItems = pendingRefundsSection.querySelectorAll('.bg-gray-700.rounded-lg.p-4.border.border-gray-600');
        const currentCount = refundItems.length;
        
        // Update the header count
        const headerElement = pendingRefundsSection.querySelector('h3');
        if (headerElement) {
            const countText = headerElement.textContent.replace(/\(\d+\)/, `(${currentCount})`);
            headerElement.innerHTML = countText;
        }
        
        // If no more pending refunds, hide the entire section
        if (currentCount === 0) {
            pendingRefundsSection.style.display = 'none';
        }
    }
}

// Approve refund
async function approveRefund(refundId) {
    if (!confirm('Êtes-vous sûr de vouloir approuver ce remboursement ?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/refunds/${refundId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Remboursement approuvé avec succès!', 'Succès', 'success');
            // Remove the refund item from the UI
            removeRefundFromUI(refundId);
            // Update the pending refunds count
            updatePendingRefundsCount();
        } else {
            showNotification('Erreur: ' + result.message, 'Erreur', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de l\'approbation du remboursement.', 'Erreur', 'error');
        console.error(error);
    }
}

// Reject refund
async function rejectRefund(refundId) {
    const reason = prompt('Raison du rejet (optionnel):');
    
    try {
        const response = await fetch(`/admin/refunds/${refundId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Remboursement rejeté.', 'Succès', 'success');
            // Remove the refund item from the UI
            removeRefundFromUI(refundId);
            // Update the pending refunds count
            updatePendingRefundsCount();
        } else {
            showNotification('Erreur: ' + result.message, 'Erreur', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors du rejet du remboursement.', 'Erreur', 'error');
        console.error(error);
    }
}

// Export donations data
async function exportDonations() {
    try {
        const response = await fetch('/admin/donations/export', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `donations-export-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } else {
            const result = await response.json();
            showNotification('Erreur lors de l\'export: ' + (result.message || 'Erreur inconnue'), 'Erreur d\'export', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de l\'export des données.', 'Erreur d\'export', 'error');
        console.error(error);
    }
}
</script>
@endsection