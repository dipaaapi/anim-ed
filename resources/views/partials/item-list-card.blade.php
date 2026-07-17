@php
    $id = $item['id'];
    $attributes = $item['attributes'] ?? [];
    $title = $attributes['canonicalTitle'] ?? 'No Title';
    $posterUrl = $attributes['posterImage']['medium'] ?? $attributes['posterImage']['original'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=300&q=80';
    $rating = $attributes['averageRating'] ?? null;
    $route = $type === 'manga' ? route('read', ['id' => $id]) : route('watch', ['id' => $id]);
@endphp

<div class="group flex flex-col sm:flex-row bg-[#12121a]/30 border border-gray-800/40 rounded-2xl overflow-hidden hover:border-indigo-500/30 hover:shadow-xl hover:shadow-indigo-950/10 transition-all duration-300">
    <!-- Thumbnail image -->
    <div class="w-full sm:w-40 aspect-[2/3] sm:aspect-auto shrink-0 relative overflow-hidden bg-gray-950">
        <img 
            src="{{ $posterUrl }}" 
            alt="{{ $title }}" 
            class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
            loading="lazy"
        />
        <!-- Rating badge -->
        @if($rating)
            <div class="absolute top-3 left-3 bg-black/75 backdrop-blur-md text-amber-400 font-extrabold text-[10px] px-2 py-0.5 rounded-lg border border-amber-400/20 flex items-center gap-1 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ $rating }}%
            </div>
        @endif
    </div>

    <!-- Metadata Details -->
    <div class="p-6 flex-grow flex flex-col justify-between overflow-hidden">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wider {{ $type === 'manga' ? 'bg-purple-650/20 text-purple-400 border border-purple-500/20' : 'bg-indigo-650/20 text-indigo-400 border border-indigo-500/20' }}">
                    {{ $attributes['subtype'] ?? $type }}
                </span>
                <span class="text-[10px] font-extrabold bg-gray-900/60 text-gray-400 border border-gray-800/60 px-2 py-0.5 rounded-md uppercase tracking-wider">
                    {{ $attributes['status'] ?? 'Unknown' }}
                </span>
            </div>
            
            <h3 class="text-lg font-bold text-gray-200 group-hover:text-white transition-colors truncate">
                <a href="{{ $route }}">{{ $title }}</a>
            </h3>

            <!-- Synopsis snippet -->
            <p class="text-xs text-gray-450 line-clamp-3 mt-2 mb-4 leading-relaxed">
                {{ $attributes['synopsis'] ?? 'No description available.' }}
            </p>

            <!-- Genres List -->
            @if(!empty($attributes['serialization']))
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @foreach(explode(', ', $attributes['serialization']) as $genre)
                        <span class="text-[10px] bg-[#12121a]/60 text-gray-400 px-2 py-0.5 rounded border border-gray-800/40">
                            {{ $genre }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-gray-800/30">
            <span class="text-xs text-gray-500 font-semibold">
                {{ $attributes['episodeCount'] ?? $attributes['chapterCount'] ?? '?' }} {{ $type === 'manga' ? 'Chapters' : 'Episodes' }}
            </span>

            <a href="{{ $route }}" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-5 rounded-xl shadow-lg transition-all active:scale-[0.97]">
                {{ $type === 'manga' ? 'Read Now' : 'Watch Now' }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</div>
