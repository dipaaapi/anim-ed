@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative rounded-3xl overflow-hidden mb-12 py-20 px-8 text-center bg-gradient-to-br from-indigo-950/40 via-purple-950/20 to-black border border-indigo-900/20 shadow-2xl">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-600/10 via-transparent to-transparent pointer-events-none"></div>

    <div class="relative z-10 max-w-2xl mx-auto">
        <span class="bg-indigo-500/15 text-indigo-400 font-extrabold text-xs px-4 py-1.5 rounded-full uppercase tracking-wider mb-4 inline-block">
            Your Ultimate Anime & Manga Portal
        </span>
        <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight mb-4">
            Find Your Next <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400">Adventure</span>
        </h1>
        <p class="text-gray-400 text-base sm:text-lg mb-8 leading-relaxed">
            Search thousands of anime titles and manga chapters. Track what you've read or watched instantly.
        </p>

        <!-- Search Bar with HTMX -->
        <div class="bg-gray-900/60 border border-gray-800/80 rounded-2xl p-2 max-w-xl mx-auto flex items-center shadow-lg focus-within:border-indigo-500/50 transition-all">
            <div class="flex items-center gap-2 px-3 text-gray-550">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            
            <input 
                type="text" 
                name="query" 
                placeholder="Search anime or manga..." 
                class="bg-transparent border-0 outline-none w-full py-2 text-white placeholder-gray-500 text-sm focus:ring-0"
                hx-get="{{ route('browse') }}"
                hx-trigger="keyup changed delay:300ms, search"
                hx-target="#search-results-section"
                hx-include="[name='type']"
                hx-indicator="#search-indicator"
            />
            
            <div class="flex items-center gap-2">
                <!-- Dropdown to select search type -->
                <select name="type" class="bg-gray-800 border-0 text-xs font-bold text-gray-300 rounded-xl px-3 py-2 cursor-pointer focus:ring-0 outline-none">
                    <option value="anime">Anime</option>
                    <option value="manga">Manga</option>
                </select>
            </div>
        </div>

        <!-- Spinner loader for HTMX search -->
        <div id="search-indicator" class="htmx-indicator mt-4 text-xs font-semibold text-indigo-400 flex items-center justify-center gap-2">
            <div class="w-4 h-4 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
            Searching database...
        </div>
    </div>
</section>

<!-- Two-Column Page Layout (Content Left, Trends Sidebar Right) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Left column: Trending Content lists -->
    <div class="lg:col-span-8 space-y-12">
        <div id="search-results-section" class="space-y-12">
            <!-- Trending Anime Section -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-2.5 h-6 rounded bg-indigo-650"></span>
                        Trending Anime
                    </h2>
                    <a href="{{ route('browse', ['type' => 'anime']) }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-wider">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    @foreach($trendingAnime as $item)
                        @include('partials.item-card', ['item' => $item, 'type' => 'anime'])
                    @endforeach
                </div>
            </section>

            <!-- Trending Manga Section -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-2.5 h-6 rounded bg-purple-650"></span>
                        Trending Manga
                    </h2>
                    <a href="{{ route('browse', ['type' => 'manga']) }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-wider">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    @foreach($trendingManga as $item)
                        @include('partials.item-card', ['item' => $item, 'type' => 'manga'])
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <!-- Right column: Top 10 Trends Sidebar (always visible on side) -->
    <div class="lg:col-span-4">
        @include('partials.trends-sidebar', ['type' => 'anime', 'weeklyTrend' => $weeklyTrend, 'monthlyTrend' => $monthlyTrend, 'yearlyTrend' => $yearlyTrend])
    </div>
</div>
@endsection
