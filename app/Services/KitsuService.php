<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KitsuService
{
    protected string $baseUrl = 'https://graphql.anilist.co';

    /**
     * Data is considered fresh for this long; after that it is refreshed in the
     * background on the next visit while visitors keep seeing the cached copy.
     */
    protected const FRESH_SECONDS = 600;

    /**
     * How long the last good copy is kept as a fallback if AniList is down.
     */
    protected const STALE_SECONDS = 604800;

    /**
     * Helper to query AniList GraphQL endpoint.
     */
    protected function queryAniList(string $query, array $variables = []): array
    {
        $cacheKey = 'anilist:' . md5($query . json_encode($variables));

        try {
            // Failed fetches throw, so they are never cached and the previous
            // good copy keeps being served until AniList recovers.
            return Cache::flexible(
                $cacheKey,
                [self::FRESH_SECONDS, self::STALE_SECONDS],
                fn () => $this->fetchAniList($query, $variables)
            );
        } catch (\Throwable $e) {
            Log::error('AniList API unavailable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @throws \RuntimeException when AniList cannot be reached or returns an error
     */
    protected function fetchAniList(string $query, array $variables = []): array
    {
        $response = Http::timeout(10)
            ->retry(2, 500, throw: false)
            ->acceptJson()
            ->post($this->baseUrl, [
                'query' => $query,
                'variables' => $variables,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('HTTP ' . $response->status() . ': ' . mb_substr($response->body(), 0, 300));
        }

        $data = $response->json('data');
        if (empty($data)) {
            throw new \RuntimeException('Empty response: ' . mb_substr($response->body(), 0, 300));
        }

        return $data;
    }

    /**
     * Map AniList GraphQL media item structure to Kitsu format for compatibility.
     */
    private function transformMedia(array $media): array
    {
        // Clean synopsis description from html tags if any
        $synopsis = strip_tags($media['description'] ?? 'No description available.');

        $posterUrl = $media['coverImage']['large'] ?? $media['coverImage']['medium'] ?? $media['coverImage']['extraLarge'] ?? '';
        $coverUrl = $media['bannerImage'] ?? $media['coverImage']['extraLarge'] ?? $media['coverImage']['large'] ?? '';

        return [
            'id' => (string) ($media['id'] ?? ''),
            'attributes' => [
                'canonicalTitle' => $media['title']['english'] ?? $media['title']['romaji'] ?? $media['title']['native'] ?? 'No Title',
                'synopsis' => $synopsis,
                'posterImage' => [
                    'medium' => $posterUrl,
                    'large' => $media['coverImage']['extraLarge'] ?? $posterUrl,
                    'original' => $media['coverImage']['extraLarge'] ?? $posterUrl,
                ],
                'coverImage' => [
                    'large' => $coverUrl,
                    'original' => $coverUrl,
                ],
                'averageRating' => $media['averageScore'] ?? null,
                'subtype' => $media['format'] ?? '',
                'countryOfOrigin' => $media['countryOfOrigin'] ?? '',
                'status' => $media['status'] ?? '',
                'startDate' => isset($media['startDate']) ? sprintf('%04d-%02d-%02d', $media['startDate']['year'] ?? 0, $media['startDate']['month'] ?? 0, $media['startDate']['day'] ?? 0) : 'Unknown',
                'episodeCount' => $media['episodes'] ?? null,
                'chapterCount' => $media['chapters'] ?? null,
                'ageRatingGuide' => ($media['isAdult'] ?? false) ? 'R18+' : 'PG-13',
                'serialization' => implode(', ', $media['genres'] ?? []),
                'popularityRank' => $media['popularity'] ?? null,
                'youtubeVideoId' => isset($media['trailer']) && ($media['trailer']['site'] ?? '') === 'youtube' ? ($media['trailer']['id'] ?? null) : null,
            ]
        ];
    }

    /**
     * Unified browse and search endpoint with filters.
     */
    public function browseItems(string $type, array $filters = []): array
    {
        $mediaType = strtoupper($type) === 'MANGA' ? 'MANGA' : 'ANIME';

        $query = '
        query ($page: Int, $perPage: Int, $search: String, $genre: [String], $status: MediaStatus, $sort: [MediaSort], $ageRating: Boolean, $startedAfter: FuzzyDateInt, $startedBefore: FuzzyDateInt, $tag: [String], $format: [MediaFormat], $country: CountryCode, $season: MediaSeason, $seasonYear: Int, $source: MediaSource) {
            Page(page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                    perPage
                }
                media(type: ' . $mediaType . ', search: $search, genre_in: $genre, status: $status, sort: $sort, isAdult: $ageRating, startDate_greater: $startedAfter, startDate_lesser: $startedBefore, tag_in: $tag, format_in: $format, countryOfOrigin: $country, season: $season, seasonYear: $seasonYear, source: $source) {
                    id
                    title {
                        romaji
                        english
                        native
                    }
                    description
                    coverImage {
                        medium
                        large
                        extraLarge
                    }
                    bannerImage
                    averageScore
                    format
                    countryOfOrigin
                    status
                    startDate {
                        year
                        month
                        day
                    }
                    episodes
                    chapters
                    isAdult
                    genres
                    popularity
                    trailer {
                        id
                        site
                    }
                }
            }
        }';

        $variables = [
            'page' => (int) ($filters['page'] ?? 1),
            'perPage' => (int) ($filters['perPage'] ?? 18),
        ];

        if (!empty($filters['search'])) {
            $variables['search'] = $filters['search'];
        }
        if (!empty($filters['genre'])) {
            $variables['genre'] = [$filters['genre']];
        }
        if (!empty($filters['status'])) {
            $variables['status'] = $filters['status'];
        }
        // Adult titles are hidden unless explicitly requested
        $variables['ageRating'] = ($filters['ageRating'] ?? '') === 'R18';
        if (!empty($filters['tag'])) {
            $variables['tag'] = [$filters['tag']];
        }
        if (!empty($filters['format'])) {
            $variables['format'] = [$filters['format']];
        }
        if (!empty($filters['country'])) {
            $variables['country'] = $filters['country'];
        }
        if (!empty($filters['source'])) {
            $variables['source'] = $filters['source'];
        }
        if (!empty($filters['year'])) {
            $year = (int) $filters['year'];
            if ($mediaType === 'ANIME' && !empty($filters['season'])) {
                $variables['season'] = $filters['season'];
                $variables['seasonYear'] = $year;
            } else {
                // Any title that started within the given year (FuzzyDateInt: YYYYMMDD)
                $variables['startedAfter'] = ($year - 1) * 10000 + 1231;
                $variables['startedBefore'] = ($year + 1) * 10000;
            }
        } elseif ($mediaType === 'ANIME' && !empty($filters['season'])) {
            $variables['season'] = $filters['season'];
        }
        if (!empty($filters['startedAfter'])) {
            // FuzzyDateInt format: YYYYMMDD
            $variables['startedAfter'] = (int) $filters['startedAfter'];
        }
        if (!empty($filters['sort'])) {
            $variables['sort'] = [$filters['sort']];
        } else {
            $variables['sort'] = ['POPULARITY_DESC'];
        }

        $data = $this->queryAniList($query, $variables);
        $mediaList = $data['Page']['media'] ?? [];
        $pageInfo = $data['Page']['pageInfo'] ?? [
            'total' => 0,
            'currentPage' => 1,
            'lastPage' => 1,
            'hasNextPage' => false,
            'perPage' => 18
        ];

        $results = array_map([$this, 'transformMedia'], $mediaList);

        return [
            'results' => $results,
            'pageInfo' => $pageInfo
        ];
    }

    /**
     * All genres known to AniList, so the filter never falls behind the source.
     */
    public function getGenres(): array
    {
        $data = $this->queryAniList('query { GenreCollection }');

        return $data['GenreCollection'] ?? [
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Mahou Shoujo', 'Mecha', 'Music',
            'Mystery', 'Psychological', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural', 'Thriller',
        ];
    }

    /**
     * Non-adult AniList tags grouped by category, e.g. ['Theme-Fantasy' => ['Isekai', ...]].
     */
    public function getTagsByCategory(): array
    {
        $data = $this->queryAniList('query { MediaTagCollection { name category isAdult } }');

        $grouped = [];
        foreach ($data['MediaTagCollection'] ?? [] as $tag) {
            if (!empty($tag['isAdult'])) {
                continue;
            }
            $grouped[$tag['category'] ?: 'Other'][] = $tag['name'];
        }
        ksort($grouped);
        foreach ($grouped as &$names) {
            sort($names);
        }

        return $grouped;
    }

    /**
     * Sidebar trend lists limited to recent releases, so they reflect what is
     * current instead of all-time classics.
     *
     * @return array{0: array, 1: array, 2: array} [weekly, monthly, yearly]
     */
    public function getTrendPeriods(string $type, int $limit = 10): array
    {
        $weekly = $this->browseItems($type, [
            'sort' => 'TRENDING_DESC',
            'perPage' => $limit,
        ])['results'];

        $monthly = $this->browseItems($type, [
            'sort' => 'TRENDING_DESC',
            'startedAfter' => now()->subMonths(6)->format('Ymd'),
            'perPage' => $limit,
        ])['results'];

        $yearly = $this->browseItems($type, [
            'sort' => 'POPULARITY_DESC',
            'startedAfter' => now()->subYear()->format('Ymd'),
            'perPage' => $limit,
        ])['results'];

        return [$weekly, $monthly, $yearly];
    }

    /**
     * Get trending anime.
     */
    public function getTrendingAnime(int $limit = 12): array
    {
        $response = $this->browseItems('anime', [
            'sort' => 'TRENDING_DESC',
            'perPage' => $limit
        ]);
        return $response['results'];
    }

    /**
     * Get trending manga.
     */
    public function getTrendingManga(int $limit = 12): array
    {
        $response = $this->browseItems('manga', [
            'sort' => 'TRENDING_DESC',
            'perPage' => $limit
        ]);
        return $response['results'];
    }

    /**
     * Search anime.
     */
    public function searchAnime(string $query, int $limit = 12): array
    {
        $response = $this->browseItems('anime', [
            'search' => $query,
            'perPage' => $limit
        ]);
        return $response['results'];
    }

    /**
     * Search manga.
     */
    public function searchManga(string $query, int $limit = 12): array
    {
        $response = $this->browseItems('manga', [
            'search' => $query,
            'perPage' => $limit
        ]);
        return $response['results'];
    }

    /**
     * Get details of a single anime.
     */
    public function getAnimeDetails(string $id): ?array
    {
        $query = '
        query ($id: Int) {
            Media(id: $id, type: ANIME) {
                id
                title {
                    romaji
                    english
                    native
                }
                description
                coverImage {
                    medium
                    large
                    extraLarge
                }
                bannerImage
                averageScore
                format
                status
                startDate {
                    year
                    month
                    day
                }
                episodes
                isAdult
                genres
                popularity
                trailer {
                    id
                    site
                }
            }
        }';

        $data = $this->queryAniList($query, ['id' => (int) $id]);
        return isset($data['Media']) ? $this->transformMedia($data['Media']) : null;
    }

    /**
     * Get details of a single manga.
     */
    public function getMangaDetails(string $id): ?array
    {
        $query = '
        query ($id: Int) {
            Media(id: $id, type: MANGA) {
                id
                title {
                    romaji
                    english
                    native
                }
                description
                coverImage {
                    medium
                    large
                    extraLarge
                }
                bannerImage
                averageScore
                format
                status
                startDate {
                    year
                    month
                    day
                }
                chapters
                isAdult
                genres
                popularity
            }
        }';

        $data = $this->queryAniList($query, ['id' => (int) $id]);
        return isset($data['Media']) ? $this->transformMedia($data['Media']) : null;
    }

    /**
     * Get episodes for an anime.
     */
    public function getAnimeEpisodes(string $id, int $limit = 20): array
    {
        $details = $this->getAnimeDetails($id);
        $count = $details['attributes']['episodeCount'] ?? 12;
        if ($count <= 0) $count = 12;

        $episodes = [];
        for ($i = 1; $i <= min($count, 100); $i++) {
            $episodes[] = [
                'id' => $i,
                'attributes' => [
                    'number' => $i,
                    'canonicalTitle' => 'Episode ' . $i,
                    'airdate' => 'Aired',
                ]
            ];
        }
        return $episodes;
    }

    /**
     * Get chapters for a manga.
     */
    public function getMangaChapters(string $id, int $limit = 20): array
    {
        $details = $this->getMangaDetails($id);
        $count = $details['attributes']['chapterCount'] ?? 30;
        if ($count <= 0) $count = 30;

        $chapters = [];
        for ($i = 1; $i <= min($count, 100); $i++) {
            $chapters[] = [
                'id' => $i,
                'attributes' => [
                    'number' => $i,
                    'canonicalTitle' => 'Chapter ' . $i,
                    'volumeNumber' => ceil($i / 10),
                ]
            ];
        }
        return $chapters;
    }
}
