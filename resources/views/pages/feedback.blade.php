@extends('layouts.dashboard')

@section('page-title', 'Feedback')
@section('page-subtitle', 'Share your thoughts and suggestions')

@section('page-content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="message-circle" class="w-8 h-8 text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">We'd love to hear from you!</h2>
            <p class="text-gray-600">Your feedback helps us improve Sylva and create better environmental solutions.</p>
        </div>
        
        <form class="space-y-6" x-data="{ rating: 0 }">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Overall Experience</label>
                <div class="flex items-center space-x-2">
                    <template x-for="star in [1,2,3,4,5]" :key="star">
                        <button type="button" 
                                x-on:click="rating = star"
                                :class="star <= rating ? 'text-yellow-400' : 'text-gray-300'"
                                class="text-2xl hover:text-yellow-400 transition-colors">
                            ⭐
                        </button>
                    </template>
                    <span x-show="rating > 0" x-text="rating + '/5'" class="ml-2 text-sm text-gray-600"></span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" id="name" name="name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           value="{{ Auth::user()->name }}" readonly>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           value="{{ Auth::user()->email }}" readonly>
                </div>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Feedback Category</label>
                <select id="category" name="category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select a category</option>
                    <option value="general">General Feedback</option>
                    <option value="bug">Bug Report</option>
                    <option value="feature">Feature Request</option>
                    <option value="usability">Usability Issue</option>
                    <option value="content">Content Suggestion</option>
                </select>
            </div>
            
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                <textarea id="message" name="message" rows="6" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Tell us about your experience, suggestions, or any issues you've encountered..."></textarea>
            </div>
            
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="newsletter" name="newsletter" type="checkbox" 
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                </div>
                <div class="ml-3">
                    <label for="newsletter" class="text-sm text-gray-700">
                        Keep me updated about new features and environmental initiatives
                    </label>
                </div>
            </div>
            
            <div class="pt-4">
                <button type="submit" 
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Submit Feedback
                </button>
            </div>
        </form>
    </div>
</div>
@endsection