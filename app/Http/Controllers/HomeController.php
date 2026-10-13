<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\WatchRead;
use App\Services\KitsuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected KitsuService $kitsu;

    public function __construct(KitsuService $kitsu)
    {
        $this->kitsu = $kitsu;
    }

    /**
     * Show the landing page that introduces the project.
     */
    public function welcome()
    {
        $featured = $this->kitsu->getTrendingAnime(8);

        return view('landing', compact('featured'));
    }

    /**
     * Show the catalog home page with trending content.
     */
    public function index()
    {
        $trendingAnime = $this->kitsu->getTrendingAnime(6);
        $trendingManga = $this->kitsu->getTrendingManga(6);

        // Sidebar trends (default to anime for homepage)
        [$weeklyTrend, $monthlyTrend, $yearlyTrend] = $this->kitsu->getTrendPeriods('anime');

        return view('home', compact('trendingAnime', 'trendingManga', 'weeklyTrend', 'monthlyTrend', 'yearlyTrend'));
    }

    /**
     * Browse / Search anime and manga.
     */
    public function browse(Request $request)
    {
        $type = $request->input('type', 'anime') === 'manga' ? 'manga' : 'anime';
        $filters = [
            'query' => $request->input('query', ''),
            'genre' => $request->input('genre', ''),
            'tag' => $request->input('tag', ''),
            'format' => $request->input('format', ''),
            'country' => $request->input('country', ''),
            'status' => $request->input('status', ''),
            'year' => $request->input('year', ''),
            'season' => $type === 'anime' ? $request->input('season', '') : '',
            'source' => $request->input('source', ''),
            'ageRating' => $request->input('age_rating', ''),
            'sort' => $request->input('sort', 'TRENDING_DESC'),
        ];
        $query = $filters['query'];
        $view = $request->input('view', 'grid'); // grid or list
        $page = max(1, (int) $request->input('page', 1));

        $browseData = $this->kitsu->browseItems($type, array_merge($filters, [
            'search' => $filters['query'],
            'page' => $page,
            'perPage' => 24,
        ]));

        $results = $browseData['results'];
        $pageInfo = $browseData['pageInfo'];

        // Sidebar trends for the active type (anime or manga)
        [$weeklyTrend, $monthlyTrend, $yearlyTrend] = $this->kitsu->getTrendPeriods($type);

        // Handle HTMX dynamic search request
        if ($request->header('HX-Request')) {
            return view('partials.items-grid', compact('results', 'type', 'query', 'pageInfo', 'view'));
        }

        $genres = $this->kitsu->getGenres();
        $tagsByCategory = $this->kitsu->getTagsByCategory();

        return view('browse', compact('results', 'type', 'query', 'filters', 'view', 'pageInfo', 'genres', 'tagsByCategory', 'weeklyTrend', 'monthlyTrend', 'yearlyTrend'));
    }

    /**
     * Watch anime page.
     */
    public function watch(string $id, Request $request)
    {
        $anime = $this->kitsu->getAnimeDetails($id);
        if (!$anime) {
            abort(404, 'Anime not found');
        }

        $episodes = $this->kitsu->getAnimeEpisodes($id, 12);

        // Fetch a side ad for watching
        $ad = Ad::where('is_active', true)
            ->whereIn('type', ['watch', 'both'])
            ->inRandomOrder()
            ->first();

        if ($ad) {
            $ad->increment('views');
        }

        // Record watch history if user is logged in
        if (Auth::check()) {
            WatchRead::create([
                'user_id' => Auth::id(),
                'item_id' => $id,
                'item_type' => 'anime',
                'title' => $anime['attributes']['canonicalTitle'] ?? 'Unknown Anime',
                'episode_or_chapter' => $request->input('episode', 'Episode 1'),
            ]);
        }

        // Sidebar trends
        [$weeklyTrend, $monthlyTrend, $yearlyTrend] = $this->kitsu->getTrendPeriods('anime');

        return view('watch', compact('anime', 'episodes', 'ad', 'weeklyTrend', 'monthlyTrend', 'yearlyTrend'));
    }

    /**
     * Read manga page.
     */
    public function read(string $id, Request $request)
    {
        $manga = $this->kitsu->getMangaDetails($id);
        if (!$manga) {
            abort(404, 'Manga not found');
        }

        $chapters = $this->kitsu->getMangaChapters($id, 12);

        // Fetch a side ad for reading
        $ad = Ad::where('is_active', true)
            ->whereIn('type', ['read', 'both'])
            ->inRandomOrder()
            ->first();

        if ($ad) {
            $ad->increment('views');
        }

        // Record read history if user is logged in
        if (Auth::check()) {
            WatchRead::create([
                'user_id' => Auth::id(),
                'item_id' => $id,
                'item_type' => 'manga',
                'title' => $manga['attributes']['canonicalTitle'] ?? 'Unknown Manga',
                'episode_or_chapter' => $request->input('chapter', 'Chapter 1'),
            ]);
        }

        // Sidebar trends
        [$weeklyTrend, $monthlyTrend, $yearlyTrend] = $this->kitsu->getTrendPeriods($type);

        return view('read', compact('manga', 'chapters', 'ad', 'weeklyTrend', 'monthlyTrend', 'yearlyTrend'));
    }

    /**
     * Record click on an advertisement.
     */
    public function clickAd(Ad $ad)
    {
        $ad->increment('clicks');
        return redirect()->away($ad->target_url);
    }
}
