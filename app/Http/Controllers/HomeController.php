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
     * Show home page with trending content.
     */
    public function index()
    {
        $trendingAnime = $this->kitsu->getTrendingAnime(6);
        $trendingManga = $this->kitsu->getTrendingManga(6);

        // Sidebar trends (default to anime for homepage)
        $weeklyTrend = $this->kitsu->browseItems('anime', ['sort' => 'TRENDING_DESC', 'perPage' => 10])['results'];
        $monthlyTrend = $this->kitsu->browseItems('anime', ['sort' => 'POPULARITY_DESC', 'perPage' => 10])['results'];
        $yearlyTrend = $this->kitsu->browseItems('anime', ['sort' => 'SCORE_DESC', 'perPage' => 10])['results'];

        return view('home', compact('trendingAnime', 'trendingManga', 'weeklyTrend', 'monthlyTrend', 'yearlyTrend'));
    }

    /**
     * Browse / Search anime and manga.
     */
    public function browse(Request $request)
    {
        $query = $request->input('query', '');
        $type = $request->input('type', 'anime'); // anime or manga
        $genre = $request->input('genre', '');
        $status = $request->input('status', '');
        $ageRating = $request->input('age_rating', '');
        $sort = $request->input('sort', 'POPULARITY_DESC');
        $view = $request->input('view', 'grid'); // grid or list
        $page = (int) $request->input('page', 1);

        $perPage = 18;

        // Fetch paginated results with filters from AniList
        $browseData = $this->kitsu->browseItems($type, [
            'search' => $query,
            'genre' => $genre,
            'status' => $status,
            'ageRating' => $ageRating,
            'sort' => $sort,
            'page' => $page,
            'perPage' => $perPage
        ]);

        $results = $browseData['results'];
        $pageInfo = $browseData['pageInfo'];

        // Sidebar trends for the active type (anime or manga)
        $weeklyTrend = $this->kitsu->browseItems($type, ['sort' => 'TRENDING_DESC', 'perPage' => 10])['results'];
        $monthlyTrend = $this->kitsu->browseItems($type, ['sort' => 'POPULARITY_DESC', 'perPage' => 10])['results'];
        $yearlyTrend = $this->kitsu->browseItems($type, ['sort' => 'SCORE_DESC', 'perPage' => 10])['results'];

        // Handle HTMX dynamic search request
        if ($request->header('HX-Request')) {
            return view('partials.items-grid', compact('results', 'type', 'query', 'pageInfo', 'view'));
        }

        return view('browse', compact('results', 'type', 'query', 'genre', 'status', 'ageRating', 'sort', 'view', 'pageInfo', 'weeklyTrend', 'monthlyTrend', 'yearlyTrend'));
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
        $weeklyTrend = $this->kitsu->browseItems('anime', ['sort' => 'TRENDING_DESC', 'perPage' => 10])['results'];
        $monthlyTrend = $this->kitsu->browseItems('anime', ['sort' => 'POPULARITY_DESC', 'perPage' => 10])['results'];
        $yearlyTrend = $this->kitsu->browseItems('anime', ['sort' => 'SCORE_DESC', 'perPage' => 10])['results'];

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
        $weeklyTrend = $this->kitsu->browseItems('manga', ['sort' => 'TRENDING_DESC', 'perPage' => 10])['results'];
        $monthlyTrend = $this->kitsu->browseItems('manga', ['sort' => 'POPULARITY_DESC', 'perPage' => 10])['results'];
        $yearlyTrend = $this->kitsu->browseItems('manga', ['sort' => 'SCORE_DESC', 'perPage' => 10])['results'];

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
