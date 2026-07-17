@php
    $id = $item['id'];
    $attributes = $item['attributes'] ?? [];
    $title = $attributes['canonicalTitle'] ?? 'No Title';
    $posterUrl = $attributes['posterImage']['medium'] ?? $attributes['posterImage']['original'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=300&q=80';
    $rating = $attributes['averageRating'] ?? null;
    $route = $type === 'manga' ? route('read', ['id' => $id]) : route('watch', ['id' => $id]);
@endphp

<div class="group relative bg-[#12121a]/30 border border-gray-800/40 rounded-2xl overflow-hidden hover:border-indigo-500/30 hover:shadow-xl hover:shadow-indigo-950/20 hover:-translate-y-1 transition-all duration-300">
    <!-- Image Wrapper -->
    <div class="aspect-[2/3] w-full overflow-hidden relative">
        <img 
            src="{{ $posterUrl }}" 
            alt="{{ $title }}" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        
        <!-- Score Badge -->
        @if($rating)
            <div class="absolute top-3 left-3 bg-black/75 backdrop-blur-md text-amber-400 font-extrabold text-[10px] px-2 py-0.5 rounded-lg border border-amber-400/20 flex items-center gap-1 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ $rating }}%
            </div>
        @endif

        <!-- Action Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0f] via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
            <a href="{{ $route }}" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-3 rounded-xl shadow-lg transition-all active:scale-[0.97]">
                {{ $type === 'manga' ? 'Read Now' : 'Watch Now' }}
            </a>
        </div>
    </div>

    <!-- Info -->
    <div class="p-3">
        <h3 class="text-sm font-bold text-gray-200 truncate group-hover:text-white transition-colors" title="{{ $title }}">
            <a href="{{ $route }}">{{ $title }}</a>
        </h3>
        <p class="text-[10px] text-gray-500 font-medium capitalize mt-0.5">
            {{ $attributes['subtype'] ?? $type }} • {{ $attributes['episodeCount'] ?? $attributes['chapterCount'] ?? '?' }} {{ $type === 'manga' ? 'Chs' : 'Eps' }}
        </p>
    </div>
</div>
