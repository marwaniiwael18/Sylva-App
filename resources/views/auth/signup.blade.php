@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-md w-full space-y-8" 
             x-data="{ 
                showPassword: false,
                showConfirmPassword: false,
                isSubmitting: false 
             }">
            
            <!-- Logo and Header -->
            <div class="text-center">
                <div class="flex justify-center items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="leaf" class="w-7 h-7 text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Sylva</h1>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Create your account</h2>
                <p class="text-gray-600">Join the green revolution today</p>
            </div>

            <!-- Form -->
            <form class="space-y-6" method="POST" action="{{ route('signup') }}" x-on:submit="isSubmitting = true">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            autocomplete="name"
                            placeholder="Enter your full name"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-100' : '' }}"
                            value="{{ old('name') }}"
                            required
                        />
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                            placeholder="Enter your email"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-100' : '' }}"
                            value="{{ old('email') }}"
                            required
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input
                            id="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            autocomplete="new-password"
                            placeholder="Create a password"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors {{ $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-red-100' : '' }}"
                            required
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            x-on:click="showPassword = !showPassword"
                        >
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="h-5 w-5 text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            autocomplete="new-password"
                            placeholder="Confirm your password"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                            required
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            x-on:click="showConfirmPassword = !showConfirmPassword"
                        >
                            <i :data-lucide="showConfirmPassword ? 'eye-off' : 'eye'" class="h-5 w-5 text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                            required
                        />
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-sm text-gray-700">
                            I agree to the 
                            <a href="#" class="text-green-600 hover:text-green-500 font-medium">Terms of Service</a>
                            and 
                            <a href="#" class="text-green-600 hover:text-green-500 font-medium">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isSubmitting"
                    x-text="isSubmitting ? 'Creating account...' : 'Create account'"
                >
                </button>

                <div class="text-center">
                    <span class="text-gray-600">Already have an account? </span>
                    <a href="{{ route('login') }}" 
                       class="font-medium text-green-600 hover:text-green-500 transition-colors">
                        Sign in
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Right side - Image/Background -->
    <div class="hidden lg:block flex-1 relative">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&q=80')">
            <div class="absolute inset-0 bg-green-900/40"></div>
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <div class="text-white text-center max-w-lg">
                    <h3 class="text-4xl font-bold mb-6">Make a Difference</h3>
                    <p class="text-xl leading-relaxed">
                        Join thousands of environmental enthusiasts working together to create 
                        a greener future for our cities and communities.
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