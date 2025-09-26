@extends('layouts.dashboard')

@section('page-title', 'Impact')
@section('page-subtitle', 'See the positive change we\'re making together')

@section('page-content')
<div class="p-6 space-y-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-8 text-white">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Our Collective Impact</h1>
            <p class="text-xl text-green-100 mb-8">Together, we're creating a greener, more sustainable future</p>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white/10 rounded-lg p-6">
                    <div class="text-3xl font-bold mb-2">{{ $impactData['trees_planted'] }}</div>
                    <div class="text-green-100">Trees Planted</div>
                </div>
                <div class="bg-white/10 rounded-lg p-6">
                    <div class="text-3xl font-bold mb-2">{{ $impactData['co2_saved'] }}T</div>
                    <div class="text-green-100">CO₂ Saved</div>
                </div>
                <div class="bg-white/10 rounded-lg p-6">
                    <div class="text-3xl font-bold mb-2">{{ $impactData['community_members'] }}</div>
                    <div class="text-green-100">Community Members</div>
                </div>
                <div class="bg-white/10 rounded-lg p-6">
                    <div class="text-3xl font-bold mb-2">{{ $impactData['projects_completed'] }}</div>
                    <div class="text-green-100">Projects Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Environmental Impact -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Environmental Impact</h2>
            
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-medium text-gray-700">Carbon Absorption</span>
                        <span class="text-2xl font-bold text-green-600">{{ $impactData['co2_saved'] }} tons</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: 75%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">75% of annual goal achieved</p>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-medium text-gray-700">Biodiversity Improvement</span>
                        <span class="text-2xl font-bold text-blue-600">85%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width: 85%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Species diversity in project areas</p>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-medium text-gray-700">Air Quality Index</span>
                        <span class="text-2xl font-bold text-purple-600">Good</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-purple-500 h-3 rounded-full" style="width: 80%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">20% improvement in target areas</p>
                </div>
            </div>
        </div>

        <!-- Community Growth -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Community Growth</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Active Members</p>
                            <p class="text-sm text-gray-600">Monthly growth: +{{ $impactData['monthly_growth'] }}%</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-green-600">{{ $impactData['community_members'] }}</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Events This Month</p>
                            <p class="text-sm text-gray-600">Average attendance: 45 people</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-blue-600">12</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i data-lucide="target" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Projects Completed</p>
                            <p class="text-sm text-gray-600">Success rate: 95%</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-purple-600">{{ $impactData['projects_completed'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Stories -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Impact Stories</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="tree-pine" class="w-8 h-8 text-green-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Urban Forest Initiative</h3>
                <p class="text-sm text-gray-600">
                    Our community planted over 50 trees in downtown areas, creating cooler microclimates and improving air quality for thousands of residents.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="droplets" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Water Conservation</h3>
                <p class="text-sm text-gray-600">
                    Implemented rainwater harvesting systems in 12 community gardens, reducing water usage by 40% while maintaining healthy plant growth.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="heart" class="w-8 h-8 text-purple-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Community Engagement</h3>
                <p class="text-sm text-gray-600">
                    Engaged over 200 volunteers in environmental activities, fostering a stronger sense of community and environmental stewardship.
                </p>
            </div>
        </div>
    </div>

    <!-- Future Goals -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Our 2025 Goals</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">500</div>
                <div class="text-sm text-gray-600">More Trees to Plant</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">10T</div>
                <div class="text-sm text-gray-600">Additional CO₂ to Absorb</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">1000</div>
                <div class="text-sm text-gray-600">New Community Members</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600 mb-2">25</div>
                <div class="text-sm text-gray-600">New Projects to Complete</div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <button class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                Join Our Mission
            </button>
        </div>
    </div>
</div>
@endsection