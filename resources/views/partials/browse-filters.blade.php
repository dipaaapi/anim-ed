@php
    $f = $filters ?? [];
    $sel = fn ($key, $value) => ($f[$key] ?? '') === (string) $value ? 'selected' : '';
    $selectClass = 'bg-gray-900/60 border border-gray-800/80 text-xs text-gray-300 rounded-lg px-3 py-2 w-full cursor-pointer focus:border-indigo-500/50 focus:ring-0 outline-none';
    $labelClass = 'block text-[10px] uppercase font-bold text-gray-550 mb-1 tracking-wider';
    $formats = $type === 'manga'
        ? ['MANGA' => 'Manga / Manhwa / Manhua', 'ONE_SHOT' => 'One-shot', 'NOVEL' => 'Light Novel']
        : ['TV' => 'TV Series', 'TV_SHORT' => 'TV Short', 'MOVIE' => 'Movie', 'OVA' => 'OVA', 'ONA' => 'ONA', 'SPECIAL' => 'Special', 'MUSIC' => 'Music Video'];
    $countries = $type === 'manga'
        ? ['JP' => 'Japan (Manga)', 'KR' => 'Korea (Manhwa)', 'CN' => 'China (Manhua)', 'TW' => 'Taiwan']
        : ['JP' => 'Japan', 'CN' => 'China (Donghua)', 'KR' => 'Korea', 'TW' => 'Taiwan'];
    $sources = ['ORIGINAL' => 'Original', 'MANGA' => 'Manga', 'LIGHT_NOVEL' => 'Light Novel', 'WEB_NOVEL' => 'Web Novel', 'NOVEL' => 'Novel', 'VIDEO_GAME' => 'Video Game', 'VISUAL_NOVEL' => 'Visual Novel', 'WEBTOON' => 'Webtoon', 'OTHER' => 'Other'];
    $statuses = ['RELEASING' => 'Ongoing', 'FINISHED' => 'Finished', 'NOT_YET_RELEASED' => 'Upcoming', 'HIATUS' => 'Hiatus', 'CANCELLED' => 'Cancelled'];
    $seasons = ['WINTER' => 'Winter', 'SPRING' => 'Spring', 'SUMMER' => 'Summer', 'FALL' => 'Fall'];
    $sorts = ['TRENDING_DESC' => 'Trending', 'POPULARITY_DESC' => 'Popularity', 'SCORE_DESC' => 'Top Rated', 'START_DATE_DESC' => 'Newest', 'START_DATE' => 'Oldest', 'FAVOURITES_DESC' => 'Most Favorited', 'UPDATED_AT_DESC' => 'Recently Updated', 'TITLE_ROMAJI' => 'Title (A-Z)'];
@endphp

@if($type === 'manga')
    <!-- Quick origin tabs (manga / manhwa / manhua) -->
    <div class="flex flex-wrap gap-2 pt-2">
        @foreach(['' => 'All', 'JP' => 'Manga', 'KR' => 'Manhwa', 'CN' => 'Manhua'] as $code => $label)
            <a href="{{ route('browse', array_filter(['type' => 'manga', 'country' => $code])) }}"
               class="text-xs font-bold px-4 py-1.5 rounded-full border transition-colors {{ ($f['country'] ?? '') === (string) $code ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-800/80 text-gray-400 hover:text-white hover:border-indigo-500/50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
@endif

<!-- Advanced Filter Rows -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-2 border-t border-gray-800/40">
    <div>
        <label class="{{ $labelClass }}">Genre</label>
        <select name="genre" class="{{ $selectClass }}">
            <option value="">Any Genre</option>
            @foreach($genres as $g)
                @continue($g === 'Hentai')
                <option value="{{ $g }}" {{ $sel('genre', $g) }}>{{ $g }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Tag / Theme</label>
        <select name="tag" class="{{ $selectClass }}">
            <option value="">Any Tag</option>
            @foreach($tagsByCategory as $category => $tags)
                <optgroup label="{{ $category }}">
                    @foreach($tags as $t)
                        <option value="{{ $t }}" {{ $sel('tag', $t) }}>{{ $t }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Format</label>
        <select name="format" class="{{ $selectClass }}">
            <option value="">Any Format</option>
            @foreach($formats as $value => $label)
                <option value="{{ $value }}" {{ $sel('format', $value) }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Country</label>
        <select name="country" class="{{ $selectClass }}">
            <option value="">Any Country</option>
            @foreach($countries as $value => $label)
                <option value="{{ $value }}" {{ $sel('country', $value) }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Status</label>
        <select name="status" class="{{ $selectClass }}">
            <option value="">Any Status</option>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ $sel('status', $value) }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Year</label>
        <select name="year" class="{{ $selectClass }}">
            <option value="">Any Year</option>
            @for($y = now()->year + 1; $y >= 1940; $y--)
                <option value="{{ $y }}" {{ $sel('year', $y) }}>{{ $y }}</option>
            @endfor
        </select>
    </div>

    @if($type === 'anime')
        <div>
            <label class="{{ $labelClass }}">Season</label>
            <select name="season" class="{{ $selectClass }}">
                <option value="">Any Season</option>
                @foreach($seasons as $value => $label)
                    <option value="{{ $value }}" {{ $sel('season', $value) }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div>
        <label class="{{ $labelClass }}">Source</label>
        <select name="source" class="{{ $selectClass }}">
            <option value="">Any Source</option>
            @foreach($sources as $value => $label)
                <option value="{{ $value }}" {{ $sel('source', $value) }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Age Rating</label>
        <select name="age_rating" class="{{ $selectClass }}">
            <option value="">Standard</option>
            <option value="R18" {{ $sel('ageRating', 'R18') }}>Adult (18+)</option>
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Sort By</label>
        <select name="sort" class="{{ $selectClass }}">
            @foreach($sorts as $value => $label)
                <option value="{{ $value }}" {{ $sel('sort', $value) }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="{{ $labelClass }}">Layout</label>
        <select name="view" class="{{ $selectClass }}">
            <option value="grid" {{ ($view ?? '') === 'grid' ? 'selected' : '' }}>Grid View</option>
            <option value="list" {{ ($view ?? '') === 'list' ? 'selected' : '' }}>List View</option>
        </select>
    </div>

    <div class="flex items-end">
        <a href="{{ route('browse', ['type' => $type]) }}" class="w-full text-center text-xs font-bold text-gray-400 hover:text-white border border-gray-800/80 hover:border-indigo-500/50 rounded-lg px-3 py-2 transition-colors">
            Reset Filters
        </a>
    </div>
</div>
