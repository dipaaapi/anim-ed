@extends('layouts.app')

@section('content')
@php
    $attributes = $anime['attributes'] ?? [];
    $title = $attributes['canonicalTitle'] ?? 'No Title';
    $synopsis = $attributes['synopsis'] ?? 'No synopsis available.';
    $posterUrl = $attributes['posterImage']['large'] ?? $attributes['posterImage']['original'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=300&q=80';
    $coverUrl = $attributes['coverImage']['large'] ?? $attributes['coverImage']['original'] ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=1200&q=80';
    $rating = $attributes['averageRating'] ?? null;
    $ytId = $attributes['youtubeVideoId'] ?? null;
    $activeEp = request()->input('episode', 'Episode 1');
@endphp

<!-- Banner Header -->
<div class="relative h-[250px] sm:h-[350px] w-full rounded-3xl overflow-hidden mb-8 border border-gray-800/40 shadow-2xl">
    <div class="absolute inset-0 bg-cover bg-center filter blur-[1px] brightness-[0.4]" style="background-image: url('{{ $coverUrl }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0f] via-[#0b0b0f]/50 to-transparent"></div>
    
    <div class="absolute bottom-6 left-6 right-6 flex flex-col sm:flex-row items-end gap-6 z-10">
        <!-- Thumbnail -->
        <img src="{{ $posterUrl }}" alt="{{ $title }}" class="w-32 h-44 rounded-xl object-cover border-2 border-indigo-500/30 shadow-2xl hidden sm:block">
        
        <div class="flex-grow">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="bg-indigo-600 text-white font-extrabold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">Anime</span>
                <span class="bg-gray-800 text-gray-300 font-extrabold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $attributes['subtype'] ?? 'TV' }}</span>
                <span class="bg-gray-800 text-gray-300 font-extrabold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $attributes['status'] ?? 'Finished' }}</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">{{ $title }}</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Released: {{ $attributes['startDate'] ?? 'Unknown' }} • {{ $attributes['episodeCount'] ?? '?' }} Episodes</p>
        </div>
    </div>
</div>

<!-- Grid Layout (Player + Side Sidebar) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Player Column -->
    <div class="lg:col-span-8 space-y-6">
        
        <!-- Video Player Wrapper -->
        <div id="video-player-container" class="bg-black border border-gray-800/60 rounded-3xl overflow-hidden aspect-video relative group shadow-2xl flex items-center justify-center">
            <!-- Simulated Video / Cover Image -->
            <img src="{{ $coverUrl }}" alt="Video Stream" class="absolute inset-0 w-full h-full object-cover brightness-[0.3]">
            
            <div class="relative z-10 text-center px-6">
                <!-- Play Button Icon -->
                <button 
                    onclick="startVideoPlayer()" 
                    class="w-20 h-20 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/30 hover:scale-105 active:scale-95 transition-all mx-auto mb-4"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                </button>
                <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-550/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 inline-block">
                    {{ $activeEp }}
                </span>
                <h3 class="text-lg font-bold text-white mt-1">Ready to Stream</h3>
                <p class="text-xs text-gray-400 max-w-sm mx-auto mt-1">Click play to watch the official preview / episode stream.</p>
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
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Age Rating</span>
                    <span class="text-white font-bold">{{ $attributes['ageRatingGuide'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Genres</span>
                    <span class="text-white font-bold truncate block" title="{{ $attributes['serialization'] ?? 'N/A' }}">{{ $attributes['serialization'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs uppercase font-semibold">Popularity Rank</span>
                    <span class="text-white font-bold">#{{ $attributes['popularityRank'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column (Ads + Episodes + Top 10 Trends) -->
    <div class="lg:col-span-4 space-y-8">
        
        <!-- SIDE ADVERTISMENT -->
        @if($ad)
            <div class="bg-indigo-950/15 border border-indigo-500/20 rounded-3xl p-5 relative overflow-hidden shadow-xl shadow-indigo-950/10">
                <div class="absolute top-3 right-3 bg-indigo-500/20 text-indigo-400 border border-indigo-400/20 font-black text-[9px] px-2 py-0.5 rounded tracking-wider uppercase">
                    Sponsored Ad
                </div>
                <h3 class="text-xs font-semibold text-gray-400 mb-3 uppercase tracking-wider">Promoted Links</h3>
                
                <a href="{{ route('ad.click', ['ad' => $ad->id]) }}" target="_blank" class="block group">
                    <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-full h-44 object-cover rounded-2xl border border-gray-800 group-hover:border-indigo-500/40 transition-colors mb-3">
                    <h4 class="text-sm font-extrabold text-white leading-snug group-hover:text-indigo-400 transition-colors">{{ $ad->title }}</h4>
                    <span class="text-xs text-indigo-500 font-bold flex items-center gap-1 mt-2">
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

        <!-- Episodes List -->
        <div class="bg-[#12121a]/30 border border-gray-800/40 rounded-3xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Episodes</h2>
            
            @if(count($episodes) > 0)
                <div class="space-y-2.5 max-h-[350px] overflow-y-auto pr-1">
                    @foreach($episodes as $index => $episode)
                        @php
                            $epAttr = $episode['attributes'] ?? [];
                            $epTitle = $epAttr['canonicalTitle'] ?? 'Episode ' . ($epAttr['number'] ?? ($index + 1));
                            $epNumber = $epAttr['number'] ?? ($index + 1);
                            $isCurrent = $activeEp === 'Episode ' . $epNumber;
                        @endphp
                        <a 
                            href="{{ route('watch', ['id' => $anime['id'], 'episode' => 'Episode ' . $epNumber]) }}" 
                            class="flex items-center gap-3 p-2.5 rounded-xl border transition-all group {{ $isCurrent ? 'border-indigo-500 bg-indigo-600/10 text-white' : 'border-gray-800/40 hover:border-indigo-500/25 bg-gray-900/10 hover:bg-indigo-600/5' }}"
                        >
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs transition-colors {{ $isCurrent ? 'bg-indigo-600 text-white' : 'bg-gray-800/80 group-hover:bg-indigo-600 text-gray-400 group-hover:text-white' }}">
                                {{ $epNumber }}
                            </div>
                            <div class="overflow-hidden flex-grow">
                                <h4 class="text-xs font-bold truncate transition-colors {{ $isCurrent ? 'text-indigo-400' : 'text-gray-300 group-hover:text-white' }}">{{ $epTitle }}</h4>
                                <p class="text-[10px] text-gray-500">Air Date: {{ $epAttr['airdate'] ?? 'N/A' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-500">No episodes list available for this anime.</p>
            @endif
        </div>

        <!-- Sidebar Top 10 Trends (Always on Side) -->
        @include('partials.trends-sidebar', ['type' => 'anime', 'weeklyTrend' => $weeklyTrend, 'monthlyTrend' => $monthlyTrend, 'yearlyTrend' => $yearlyTrend])
    </div>
</div>

<script>
    function startVideoPlayer() {
        const container = document.getElementById('video-player-container');
        const ytId = @json($ytId);
        
        if (ytId) {
            container.innerHTML = `
                <iframe 
                    src="https://www.youtube.com/embed/${ytId}?autoplay=1&rel=0" 
                    class="w-full h-full absolute inset-0 border-0" 
                    allow="autoplay; encrypted-media; picture-in-picture" 
                    allowfullscreen>
                </iframe>`;
        } else {
            container.innerHTML = `
                <video class="w-full h-full object-cover absolute inset-0" controls autoplay>
                    <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>`;
        }
    }
</script>
@endsection
