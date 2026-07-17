@extends('layouts.app')

@section('content')
@php
    $attributes = $manga['attributes'] ?? [];
    $title = $attributes['canonicalTitle'] ?? 'No Title';
    $synopsis = $attributes['synopsis'] ?? 'No synopsis available.';
    $posterUrl = $attributes['posterImage']['large'] ?? $attributes['posterImage']['original'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=300&q=80';
    $coverUrl = $attributes['coverImage']['large'] ?? $attributes['coverImage']['original'] ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=1200&q=80';
    $rating = $attributes['averageRating'] ?? null;
    $activeCh = request()->input('chapter', 'Chapter 1');
@endphp

<!-- Banner Header -->
<div class="relative h-[250px] sm:h-[350px] w-full rounded-3xl overflow-hidden mb-8 border border-gray-800/40 shadow-2xl">
    <div class="absolute inset-0 bg-cover bg-center filter blur-[1px] brightness-[0.4]" style="background-image: url('{{ $coverUrl }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0f] via-[#0b0b0f]/50 to-transparent"></div>
    
    <div class="absolute bottom-6 left-6 right-6 flex flex-col sm:flex-row items-end gap-6 z-10">
        <!-- Thumbnail -->
        <img src="{{ $posterUrl }}" alt="{{ $title }}" class="w-32 h-44 rounded-xl object-cover border-2 border-purple-500/30 shadow-2xl hidden sm:block">
        
        <div class="flex-grow">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="bg-purple-600 text-white font-extrabold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">Manga</span>
                <span class="bg-gray-800 text-gray-300 font-extrabold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $attributes['subtype'] ?? 'Manga' }}</span>
                <span class="bg-gray-800 text-gray-300 font-extrabold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $attributes['status'] ?? 'Publishing' }}</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">{{ $title }}</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Published: {{ $attributes['startDate'] ?? 'Unknown' }} • {{ $attributes['chapterCount'] ?? '?' }} Chapters</p>
        </div>
    </div>
</div>

<!-- Grid Layout (Reader + Side Sidebar) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Reader Column -->
    <div class="lg:col-span-8 space-y-6">
        
        <!-- Reader Container -->
        <div class="bg-[#12121a] border border-gray-800/60 rounded-3xl overflow-hidden p-6 sm:p-8 shadow-2xl relative">
            <div class="max-w-xl mx-auto text-center space-y-6">
                <!-- Chapter Badge Indicator -->
                <div class="flex items-center justify-between border-b border-gray-800/60 pb-4">
                    <span class="bg-purple-500/10 text-purple-400 border border-purple-555/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ $activeCh }}
                    </span>
                    <span id="manga-page-label" class="text-xs text-gray-400 font-semibold">
                        Page 1 of 15
                    </span>
                </div>
                
                <!-- Interactive Manga Page Viewer -->
                <div class="relative bg-gray-950/80 rounded-2xl border border-gray-800/80 overflow-hidden shadow-inner flex items-center justify-center p-2 min-h-[400px]">
                    <div id="manga-loading-overlay" class="absolute inset-0 bg-gray-950/90 z-20 flex flex-col items-center justify-center gap-3 transition-opacity duration-300 hidden">
                        <div class="w-8 h-8 rounded-full border-2 border-purple-500/20 border-t-purple-500 animate-spin"></div>
                        <span class="text-xs font-semibold text-purple-400">Loading page...</span>
                    </div>

                    <img 
                        id="manga-page-img" 
                        src="https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80" 
                        alt="Manga Panel" 
                        class="max-h-[600px] object-contain rounded-lg transition-transform duration-300"
                    />
                </div>
                
                <!-- Reader Nav Buttons -->
                <div class="flex items-center justify-center gap-4 pt-2">
                    <button 
                        type="button"
                        id="manga-prev-btn"
                        onclick="changeMangaPage(-1)" 
                        class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold py-2.5 px-5 rounded-xl text-xs transition-colors"
                    >
                        Previous Page
                    </button>
                    <button 
                        type="button"
                        id="manga-next-btn"
                        onclick="changeMangaPage(1)" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition-all shadow-lg shadow-purple-650/10 active:scale-95"
                    >
                        Next Page
                    </button>
                </div>
            </div>
            
            <!-- Floating Reader Control Bar -->
            <div class="border-t border-gray-800/80 pt-6 mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
                <span>Reading Mode: Left to Right</span>
                <span class="text-purple-400 font-semibold">Press Next/Prev to flip panels</span>
                <span>Zoom Level: Fit Height</span>
            </div>
        </div>

        <!-- Description Details -->
        <div class="bg-[#12121a]/30 border border-gray-800/40 rounded-3xl p-6">
            <h2 class="text-xl font-bold text-white mb-3">Synopsis</h2>
            <p class="text-sm text-gray-400 leading-relaxed mb-6">{{ $synopsis }}</p>

            <hr class="border-gray-800/60 my-6">

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Average Score</span>
                    <span class="text-white font-bold">{{ $rating ? $rating . '%' : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Serialization</span>
                    <span class="text-white font-bold truncate block" title="{{ $attributes['serialization'] ?? 'N/A' }}">{{ $attributes['serialization'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Total Chapters</span>
                    <span class="text-white font-bold">{{ $attributes['chapterCount'] ?? 'Unknown' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Popularity Rank</span>
                    <span class="text-white font-bold">#{{ $attributes['popularityRank'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column (Ads + Chapters + Top 10 Trends) -->
    <div class="lg:col-span-4 space-y-8">
        
        <!-- SIDE ADVERTISMENT -->
        @if($ad)
            <div class="bg-purple-950/15 border border-purple-500/20 rounded-3xl p-5 relative overflow-hidden shadow-xl shadow-purple-950/10">
                <div class="absolute top-3 right-3 bg-purple-500/20 text-purple-400 border border-purple-400/20 font-black text-[9px] px-2 py-0.5 rounded tracking-wider uppercase">
                    Sponsored Ad
                </div>
                <h3 class="text-xs font-semibold text-gray-400 mb-3 uppercase tracking-wider">Promoted Links</h3>
                
                <a href="{{ route('ad.click', ['ad' => $ad->id]) }}" target="_blank" class="block group">
                    <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-full h-44 object-cover rounded-2xl border border-gray-800 group-hover:border-purple-500/40 transition-colors mb-3">
                    <h4 class="text-sm font-extrabold text-white leading-snug group-hover:text-purple-400 transition-colors">{{ $ad->title }}</h4>
                    <span class="text-xs text-purple-500 font-bold flex items-center gap-1 mt-2">
                        Visit Sponsor
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </a>
            </div>
        @else
            <!-- Fallback Mock Ad if no seeded ads -->
            <div class="bg-gray-900/30 border border-gray-800/60 rounded-3xl p-5 text-center">
                <div class="w-full h-32 bg-gray-950/60 border border-gray-800 rounded-2xl flex items-center justify-center mb-3">
                    <span class="text-xs text-gray-600 font-medium">Advertisement Space</span>
                </div>
                <p class="text-xs text-gray-500">Upgrade to Premium to remove ads.</p>
            </div>
        @endif

        <!-- Chapters List -->
        <div class="bg-[#12121a]/30 border border-gray-800/40 rounded-3xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Chapters</h2>
            
            @if(count($chapters) > 0)
                <div class="space-y-2.5 max-h-[350px] overflow-y-auto pr-1">
                    @foreach($chapters as $index => $chapter)
                        @php
                            $chAttr = $chapter['attributes'] ?? [];
                            $chTitle = $chAttr['canonicalTitle'] ?? 'Chapter ' . ($chAttr['number'] ?? ($index + 1));
                            $chNumber = $chAttr['number'] ?? ($index + 1);
                            $isCurrent = $activeCh === 'Chapter ' . $chNumber;
                        @endphp
                        <a 
                            href="{{ route('read', ['id' => $manga['id'], 'chapter' => 'Chapter ' . $chNumber]) }}" 
                            class="flex items-center gap-3 p-2.5 rounded-xl border transition-all group {{ $isCurrent ? 'border-purple-500 bg-purple-600/10 text-white' : 'border-gray-800/40 hover:border-purple-500/25 bg-gray-900/10 hover:bg-purple-650/5' }}"
                        >
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs transition-colors {{ $isCurrent ? 'bg-purple-600 text-white' : 'bg-gray-800/80 group-hover:bg-purple-600 text-gray-400 group-hover:text-white' }}">
                                {{ $chNumber }}
                            </div>
                            <div class="overflow-hidden flex-grow">
                                <h4 class="text-xs font-bold truncate transition-colors {{ $isCurrent ? 'text-purple-400' : 'text-gray-300 group-hover:text-white' }}">{{ $chTitle }}</h4>
                                <p class="text-[10px] text-gray-500">Volume: {{ $chAttr['volumeNumber'] ?? '1' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-500">No chapters list available for this manga.</p>
            @endif
        </div>

        <!-- Sidebar Top 10 Trends (Always on Side) -->
        @include('partials.trends-sidebar', ['type' => 'manga', 'weeklyTrend' => $weeklyTrend, 'monthlyTrend' => $monthlyTrend, 'yearlyTrend' => $yearlyTrend])
    </div>
</div>

<script>
    // List of beautiful manga/anime illustrations from unsplash to simulate panels
    const panelUrls = [
        "https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=600&q=80",
        "https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80",
        "https://images.unsplash.com/photo-1580477667995-2b94f01c9516?w=600&q=80",
        "https://images.unsplash.com/photo-1560942485-b2a11cc13456?w=600&q=80",
        "https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=600&q=80",
        "https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?w=600&q=80",
        "https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80",
        "https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80",
        "https://images.unsplash.com/photo-1541701494587-cb58502866ab?w=600&q=80",
        "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80"
    ];

    let currentPage = 1;
    const totalPages = 15;

    function changeMangaPage(direction) {
        const nextPage = currentPage + direction;
        if (nextPage < 1 || nextPage > totalPages) return;

        currentPage = nextPage;

        // Show loading state
        const loader = document.getElementById('manga-loading-overlay');
        loader.classList.remove('hidden');

        // Choose random image based on page index
        const imgIndex = (currentPage - 1) % panelUrls.length;
        const newImgUrl = panelUrls[imgIndex];

        const imgElement = document.getElementById('manga-page-img');
        
        // Preload image
        const tempImg = new Image();
        tempImg.src = newImgUrl;
        tempImg.onload = function() {
            imgElement.src = newImgUrl;
            loader.classList.add('hidden');
            
            // Update labels
            document.getElementById('manga-page-label').textContent = `Page ${currentPage} of ${totalPages}`;
            
            // Button disable checks
            document.getElementById('manga-prev-btn').disabled = (currentPage === 1);
            if (currentPage === 1) {
                document.getElementById('manga-prev-btn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                document.getElementById('manga-prev-btn').classList.remove('opacity-50', 'cursor-not-allowed');
            }

            document.getElementById('manga-next-btn').disabled = (currentPage === totalPages);
            if (currentPage === totalPages) {
                document.getElementById('manga-next-btn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                document.getElementById('manga-next-btn').classList.remove('opacity-50', 'cursor-not-allowed');
            }
        };
    }

    // Initialize buttons state on page load
    changeMangaPage(0);
</script>
@endsection
