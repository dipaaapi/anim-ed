@extends('layouts.app')

@section('content')
<!-- Hero -->
<section class="relative rounded-3xl overflow-hidden mb-16 py-20 sm:py-28 px-6 sm:px-12 bg-gradient-to-br from-indigo-950/50 via-purple-950/20 to-black border border-indigo-900/20 shadow-2xl">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-purple-600/15 via-transparent to-transparent pointer-events-none"></div>

    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="bg-indigo-500/15 text-indigo-400 font-extrabold text-xs px-4 py-1.5 rounded-full uppercase tracking-wider mb-5 inline-block">
                Welcome to anim-ed
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight mb-5">
                Your anime &amp; manga <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400">catalog hub</span>
            </h1>
            <p class="text-gray-400 text-base sm:text-lg mb-8 leading-relaxed max-w-xl">
                anim-ed is a clean, fast place to discover what's trending, explore thousands of anime and manga titles,
                and keep track of what you've watched and read, all powered by live data from AniList.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all active:scale-[0.98]">
                    Explore the Catalog
                </a>
                <a href="#about" class="bg-gray-900/60 hover:bg-gray-800/80 border border-gray-800/80 text-gray-200 font-bold px-6 py-3 rounded-xl transition-all">
                    Learn More
                </a>
            </div>
        </div>

        <!-- Live poster collage -->
        @if(count($featured))
            <div class="hidden lg:grid grid-cols-4 gap-3 rotate-2">
                @foreach(array_slice($featured, 0, 8) as $index => $item)
                    <div class="aspect-[2/3] rounded-xl overflow-hidden border border-gray-800/60 shadow-lg {{ $index % 2 ? 'translate-y-6' : '' }}">
                        <img src="{{ $item['attributes']['posterImage']['medium'] ?? '' }}"
                             alt="{{ $item['attributes']['canonicalTitle'] ?? '' }}"
                             class="w-full h-full object-cover" loading="lazy">
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- About / Purpose -->
<section id="about" class="mb-16 scroll-mt-24">
    <div class="text-center max-w-2xl mx-auto mb-10">
        <h2 class="text-3xl font-extrabold text-white mb-3">What is anim-ed?</h2>
        <p class="text-gray-400 leading-relaxed">
            A portfolio and educational project that shows how a modern web app can bring together a public media API,
            a fast interface, and personal tracking, without heavy infrastructure.
        </p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach([
            ['Discover', 'See what is trending this week, this season, and this year, refreshed from live data.', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['Browse & Search', 'Filter anime and manga by genre, status, rating and more, with instant search.', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            ['Watch & Read', 'Open any title for its synopsis, trailer, episodes or chapters in one place.', 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Track Progress', 'Sign in to keep a history of what you have watched and read.', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as [$heading, $text, $icon])
            <div class="bg-[#12121a]/40 border border-gray-800/50 rounded-2xl p-6 hover:border-indigo-500/30 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-2">{{ $heading }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">{{ $text }}</p>
            </div>
        @endforeach
    </div>
</section>

<!-- Call to action -->
<section class="text-center rounded-3xl border border-gray-800/50 bg-[#12121a]/40 py-14 px-6">
    <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">Ready to find your next adventure?</h2>
    <p class="text-gray-400 mb-7">Jump into the catalog and start exploring.</p>
    <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('browse', ['type' => 'anime']) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-xl transition-all">Browse Anime</a>
        <a href="{{ route('browse', ['type' => 'manga']) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-6 py-3 rounded-xl transition-all">Browse Manga</a>
    </div>
    <p class="text-gray-600 text-xs mt-8">Data provided by the public AniList API. Non-commercial, educational project.</p>
</section>
@endsection
