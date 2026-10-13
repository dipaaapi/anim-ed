@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-white capitalize mb-2">Browse {{ $type }}</h1>
    <p class="text-gray-400 text-sm">Discover trending content or use the advanced filters below to search.</p>
</div>

<!-- Search & Filters Container -->
<section class="bg-[#12121a]/30 border border-gray-800/40 rounded-3xl p-6 mb-8 shadow-xl">
    <form hx-get="{{ route('browse') }}" hx-target="#search-results-section" hx-trigger="submit, change" hx-indicator="#browse-indicator" class="space-y-4">
        <!-- Main Search Input & Type -->
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <!-- Search Input -->
            <div class="relative w-full flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="query" 
                    value="{{ $query }}"
                    placeholder="Search titles..." 
                    class="bg-gray-900/60 border border-gray-800/80 rounded-xl pl-10 pr-4 py-2.5 w-full text-white placeholder-gray-500 text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
                />
            </div>

            <!-- Type Selector -->
            <div class="w-full md:w-44">
                <select name="type" onchange="window.location = '{{ route('browse') }}?type=' + this.value" class="bg-gray-900/60 border border-gray-800/80 text-sm text-gray-300 rounded-xl px-4 py-2.5 w-full cursor-pointer focus:border-indigo-500/50 focus:ring-0 outline-none">
                    <option value="anime" {{ $type === 'anime' ? 'selected' : '' }}>Anime</option>
                    <option value="manga" {{ $type === 'manga' ? 'selected' : '' }}>Manga</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-indigo-600/10 active:scale-95">
                Apply
            </button>
        </div>

        @include('partials.browse-filters')

        <!-- Pagination hidden field to maintain state -->
        <input type="hidden" name="page" id="browse-page-input" value="{{ $pageInfo['currentPage'] ?? 1 }}">
    </form>

    <!-- Search status indicator -->
    <div id="browse-indicator" class="htmx-indicator mt-3 text-xs font-semibold text-indigo-400 flex items-center gap-2">
        <div class="w-4 h-4 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
        Applying filters...
    </div>
</section>

<!-- Two-Column Page Layout (Results Left, Top 10 Trends Right) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Results column -->
    <div class="lg:col-span-8">
        <div id="search-results-section">
            @include('partials.items-grid', ['results' => $results, 'type' => $type, 'query' => $query, 'pageInfo' => $pageInfo, 'view' => $view ?? 'grid'])
        </div>
    </div>

    <!-- Sidebar Trends Column -->
    <div class="lg:col-span-4">
        @include('partials.trends-sidebar', ['type' => $type, 'weeklyTrend' => $weeklyTrend, 'monthlyTrend' => $monthlyTrend, 'yearlyTrend' => $yearlyTrend])
    </div>
</div>

<script>
    // Any filter change starts again from page 1
    document.addEventListener('change', function (e) {
        const pageInput = document.getElementById('browse-page-input');
        if (pageInput && e.target.form === pageInput.form) {
            pageInput.value = 1;
        }
    }, true);

    // Handle pagination page change without full reload
    function goToBrowsePage(pageNum) {
        const pageInput = document.getElementById('browse-page-input');
        if (pageInput) {
            pageInput.value = pageNum;
            // Trigger htmx form submission
            const form = pageInput.closest('form');
            if (form) {
                htmx.trigger(form, 'submit');
            }
        }
    }
</script>
@endsection
