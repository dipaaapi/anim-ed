<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'anim-ed | Watch Anime & Read Manga' }}</title>
    <meta name="description" content="Discover, watch and read your favorite anime and manga on anim-ed. Clean, fast, and modern interface.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f0f15;
        }
        ::-webkit-scrollbar-thumb {
            background: #27273a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0b0b0f] text-gray-200 min-h-screen flex flex-col selection:bg-indigo-600 selection:text-white overflow-x-hidden">

    <!-- Header Navbar -->
    <header class="sticky top-0 z-50 bg-[#0b0b0f]/80 backdrop-blur-md border-b border-gray-800/60 px-4 lg:px-8 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-all">
                    A
                </div>
                <span class="text-2xl font-black tracking-tight text-white group-hover:text-indigo-400 transition-colors">
                    anim<span class="text-indigo-500">-ed</span>
                </span>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors {{ request()->routeIs('home') ? 'text-indigo-400 font-bold' : 'text-gray-400' }}">Home</a>
                <a href="{{ route('browse', ['type' => 'anime']) }}" class="hover:text-white transition-colors {{ request()->routeIs('browse') && request('type') == 'anime' ? 'text-indigo-400 font-bold' : 'text-gray-400' }}">Anime</a>
                <a href="{{ route('browse', ['type' => 'manga']) }}" class="hover:text-white transition-colors {{ request()->routeIs('browse') && request('type') == 'manga' ? 'text-indigo-400 font-bold' : 'text-gray-400' }}">Manga</a>
            </nav>

            <!-- User Auth Controls -->
            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3 bg-gray-900/60 border border-gray-800/80 px-3 py-1.5 rounded-full">
                        <img src="{{ auth()->user()->avatar ?? 'https://api.dicebear.com/7.x/adventurer/svg?seed='.urlencode(auth()->user()->name) }}" class="w-7 h-7 rounded-full border border-indigo-500/30" alt="Avatar">
                        <span class="text-sm font-medium text-gray-300 hidden sm:inline">{{ auth()->user()->name }}</span>
                        
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-2 py-0.5 rounded" title="Admin Dashboard">Admin</a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors p-1" title="Log Out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <button onclick="document.getElementById('login-modal').classList.remove('hidden')" class="relative group overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 active:scale-95 transition-all">
                        Sign In
                    </button>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-xl flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#070709] border-t border-gray-900 py-8 px-4 mt-12 text-center text-gray-500 text-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-white font-extrabold">anim-ed.io</span>
                <span>© {{ date('Y') }} All rights reserved.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('browse', ['type' => 'anime']) }}" class="hover:text-gray-300 transition-colors">Anime</a>
                <a href="{{ route('browse', ['type' => 'manga']) }}" class="hover:text-gray-300 transition-colors">Manga</a>
                <span class="text-gray-700">|</span>
                <span class="text-gray-600 text-xs">API provided by Kitsu.io</span>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="login-modal" class="{{ session('show-login') ? '' : 'hidden' }} fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm transition-all duration-300">
        <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl w-full max-w-md p-6 relative shadow-2xl overflow-hidden">
            <!-- Background Glows -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Close btn -->
            <button onclick="document.getElementById('login-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-gray-800/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-2xl mx-auto shadow-lg shadow-indigo-600/20 mb-3">
                    A
                </div>
                <h3 class="text-xl font-bold text-white">Welcome back to anim-ed</h3>
                <p class="text-gray-400 text-sm mt-1">Sign in to sync your watch and read history</p>
            </div>

            <!-- Login buttons -->
            <div class="flex flex-col gap-4">
                <!-- Google Login -->
                <a href="{{ route('auth.redirect', ['provider' => 'google']) }}" class="flex items-center justify-center gap-3 bg-white hover:bg-gray-100 text-gray-900 font-bold py-3 px-4 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                        <g transform="matrix(1, 0, 0, 1, 0, 0)">
                            <path d="M21.35,11.1H12v2.7h5.38c-0.24,1.28 -0.96,2.37 -2.04,3.1v2.58h3.3c1.93,-1.78 3.04,-4.4 3.04,-7.38C21.68,11.83 21.56,11.44 21.35,11.1z" fill="#4285F4"/>
                            <path d="M12,20.88c2.43,0 4.47,-0.8 5.96,-2.19l-3.3,-2.58c-0.9,0.6 -2.07,0.97 -3.27,0.97 -2.33,0 -4.3,-1.57 -5,-3.69H3.01v2.67C4.49,19.01 8,20.88 12,20.88z" fill="#34A853"/>
                            <path d="M7,13.39c-0.18,-0.54 -0.28,-1.11 -0.28,-1.7s0.1,-1.16 0.28,-1.7V7.32H3.01C2.37,8.6 2,10.04 2,11.69c0,1.65 0.37,3.09 1.01,4.37L7,13.39z" fill="#FBBC05"/>
                            <path d="M12,6.43c1.32,0 2.51,0.45 3.44,1.35l2.58,-2.58C16.46,3.64 14.43,2.82 12,2.82c-4,0 -7.51,1.87 -8.99,4.5l3.99,3.07C7.7,8.27 9.67,6.43 12,6.43z" fill="#EA4335"/>
                        </g>
                    </svg>
                    Continue with Google
                </a>

                <!-- Facebook Login -->
                <a href="{{ route('auth.redirect', ['provider' => 'facebook']) }}" class="flex items-center justify-center gap-3 bg-[#1877F2] hover:bg-[#166FE5] text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Continue with Facebook
                </a>
            </div>
        </div>
    </div>

</body>
</html>
