@php
    $view = $view ?? 'grid';
    $currentPage = $pageInfo['currentPage'] ?? 1;
    $lastPage = $pageInfo['lastPage'] ?? 1;
    $hasNextPage = $pageInfo['hasNextPage'] ?? false;
@endphp

<div>
    <!-- Results Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-extrabold text-white flex items-center gap-2">
            <span class="w-2.5 h-6 rounded bg-indigo-600"></span>
            @if(!empty($query))
                Search Results
            @else
                Explore {{ ucfirst($type) }}
            @endif
        </h2>
        <span class="text-xs font-bold text-gray-500 bg-gray-900/60 border border-gray-800/60 px-3 py-1 rounded-full uppercase tracking-wider">
            Page {{ $currentPage }} of {{ $lastPage }}
        </span>
    </div>

    @if(count($results) > 0)
        @if($view === 'list')
            <!-- List View Layout -->
            <div class="space-y-4">
                @foreach($results as $item)
                    @include('partials.item-list-card', ['item' => $item, 'type' => $type])
                @endforeach
            </div>
        @else
            <!-- Grid View Layout (changed to 3 cols on mobile, 4 on sm, 6 on md/lg for rich dense presentation) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($results as $item)
                    @include('partials.item-card', ['item' => $item, 'type' => $type])
                @endforeach
            </div>
        @endif

        <!-- PAGINATION CONTROLS -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-12 pt-6 border-t border-gray-900">
            <p class="text-xs text-gray-550 font-semibold">
                Showing page {{ $currentPage }} of {{ $lastPage }} pages.
            </p>
            
            <div class="flex items-center gap-2">
                <!-- First Page -->
                @if($currentPage > 1)
                    <button 
                        type="button" 
                        onclick="goToBrowsePage(1)"
                        class="bg-[#12121a]/60 hover:bg-indigo-600 border border-gray-800 hover:border-indigo-500/30 text-gray-400 hover:text-white font-bold p-2.5 rounded-xl text-xs transition-all"
                        title="First Page"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                @endif

                <!-- Previous Page -->
                <button 
                    type="button" 
                    onclick="goToBrowsePage({{ max(1, $currentPage - 1) }})"
                    class="bg-[#12121a]/60 hover:bg-indigo-600 border border-gray-800 hover:border-indigo-500/30 text-gray-300 hover:text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all active:scale-[0.98] {{ $currentPage <= 1 ? 'opacity-40 cursor-not-allowed' : '' }}"
                    {{ $currentPage <= 1 ? 'disabled' : '' }}
                >
                    Prev
                </button>

                <!-- Page numbers surrounding current page -->
                @php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp
                @for($p = $startPage; $p <= $endPage; $p++)
                    <button 
                        type="button" 
                        onclick="goToBrowsePage({{ $p }})"
                        class="w-10 h-10 rounded-xl text-xs font-bold transition-all {{ $currentPage == $p ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-[#12121a]/60 hover:bg-gray-800 text-gray-400 hover:text-white border border-gray-800/80' }}"
                    >
                        {{ $p }}
                    </button>
                @endfor

                <!-- Next Page -->
                <button 
                    type="button" 
                    onclick="goToBrowsePage({{ min($lastPage, $currentPage + 1) }})"
                    class="bg-[#12121a]/60 hover:bg-indigo-600 border border-gray-800 hover:border-indigo-500/30 text-gray-300 hover:text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all active:scale-[0.98] {{ !$hasNextPage ? 'opacity-40 cursor-not-allowed' : '' }}"
                    {{ !$hasNextPage ? 'disabled' : '' }}
                >
                    Next
                </button>

                <!-- Last Page -->
                @if($hasNextPage && $currentPage < $lastPage)
                    <button 
                        type="button" 
                        onclick="goToBrowsePage({{ $lastPage }})"
                        class="bg-[#12121a]/60 hover:bg-indigo-600 border border-gray-800 hover:border-indigo-500/30 text-gray-400 hover:text-white font-bold p-2.5 rounded-xl text-xs transition-all"
                        title="Last Page"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @else
        <div class="text-center py-20 bg-[#12121a]/30 border border-gray-800/40 rounded-3xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-300">No results found</h3>
            <p class="text-gray-550 text-sm mt-1 max-w-xs mx-auto">Try refining your filter criteria, checking your query spelling, or changing the sorting type.</p>
        </div>
    @endif
</div>
