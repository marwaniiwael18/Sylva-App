@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-md w-full space-y-8" 
             x-data="{ isSubmitting: false }">
            
            <!-- Logo and Header -->
            <div class="text-center">
                <div class="flex justify-center items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="leaf" class="w-7 h-7 text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Sylva</h1>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Forgot your password?</h2>
                <p class="text-gray-600">No worries, we'll send you reset instructions</p>
            </div>

            <!-- Form -->
            <form class="space-y-6" method="POST" action="{{ route('forgot-password') }}" x-on:submit="isSubmitting = true">
                @csrf
                
                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            placeholder="Enter your email address"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-100' : '' }}"
                            value="{{ old('email') }}"
                            required
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isSubmitting"
                    x-text="isSubmitting ? 'Sending reset link...' : 'Send reset link'"
                >
                </button>

                <div class="text-center space-y-2">
                    <a href="{{ route('login') }}" 
                       class="text-sm font-medium text-green-600 hover:text-green-500 transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Back to sign in
                    </a>
                    <div class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('signup') }}" class="font-medium text-green-600 hover:text-green-500 transition-colors">
                            Sign up
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right side - Image/Background -->
    <div class="hidden lg:block flex-1 relative">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&q=80')">
            <div class="absolute inset-0 bg-green-900/40"></div>
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <div class="text-white text-center max-w-lg">
                    <h3 class="text-4xl font-bold mb-6">We've Got You Covered</h3>
                    <p class="text-xl leading-relaxed">
                        Forgot your password? No problem! We'll help you get back to 
                        making a positive impact on the environment.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush