<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Courier+Prime&family=Inter:wght@400;500&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-['Inter'] bg-gray-900">
        <div class="min-h-screen bg-gray-900">
            <div class="relative flex min-h-screen flex-col items-center justify-center p-6">
                <!-- Logo -->
                <div class="mb-2 w-20 h-20">
                    <img src="{{ asset('scale.svg') }}" alt="Logo" class="w-full h-full" />
                </div>

                <!-- Title -->
                <h1 class="mb-4 text-4xl font-light text-white font-['Courier_Prime']">
                    HackTrack
                </h1>

                <!-- Description -->
                <p class="mb-8 max-w-md text-center text-gray-400">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
                <p class="mb-8 max-w-md text-center text-gray-400">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>

                <!-- Buttons -->
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" 
                           class="rounded-lg bg-white px-6 py-3 text-gray-900 hover:bg-gray-100">
                            Go to Dashboard
                        </a>
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="rounded-lg border border-white px-6 py-3 text-white hover:bg-gray-800">
                                Log Out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="rounded-lg bg-white px-6 py-3 text-gray-900 hover:bg-gray-100">
                            Log In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="rounded-lg border border-white px-6 py-3 text-white hover:bg-gray-800">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </body>
</html>
