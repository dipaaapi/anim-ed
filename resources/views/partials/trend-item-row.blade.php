@php
    $id = $item['id'];
    $attributes = $item['attributes'] ?? [];
    $title = $attributes['canonicalTitle'] ?? 'No Title';
    $posterUrl = $attributes['posterImage']['medium'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=150&q=80';
    $rating = $attributes['averageRating'] ?? null;
    $route = $type === 'manga' ? route('read', ['id' => $id]) : route('watch', ['id' => $id]);
    
    // Rank styling classes
    $rankBadgeClass = 'bg-gray-800 text-gray-400';
    if ($rank === 1) {
        $rankBadgeClass = 'bg-amber-500/20 text-amber-400 border border-amber-500/35';
    } elseif ($rank === 2) {
        $rankBadgeClass = 'bg-slate-300/20 text-slate-300 border border-slate-300/35';
    } elseif ($rank === 3) {
        $rankBadgeClass = 'bg-amber-700/20 text-amber-600 border border-amber-700/35';
    }
@endphp

<a href="{{ $route }}" class="flex items-center gap-3 group p-1.5 rounded-xl hover:bg-gray-900/40 border border-transparent hover:border-gray-800/40 transition-all">
    <!-- Rank Number Badge -->
    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black shrink-0 {{ $rankBadgeClass }}">
        {{ $rank }}
    </div>

    <!-- Mini Thumbnail -->
    <div class="w-10 h-14 rounded-lg overflow-hidden shrink-0 border border-gray-800/80 bg-gray-950">
        <img src="{{ $posterUrl }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
    </div>

    <!-- Content details -->
    <div class="overflow-hidden flex-grow">
        <h4 class="text-xs font-bold text-gray-200 truncate group-hover:text-indigo-400 transition-colors" title="{{ $title }}">
            {{ $title }}
        </h4>
        <div class="flex items-center gap-1.5 mt-0.5">
            <span class="text-[9px] font-semibold text-gray-500 capitalize">
                {{ $attributes['subtype'] ?? $type }}
            </span>
            @if($rating)
                <span class="text-[9px] text-gray-400 font-bold">•</span>
                <span class="text-[9px] text-amber-400 font-extrabold flex items-center gap-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    {{ $rating }}%
                </span>
            @endif
        </div>
    </div>
</a>
