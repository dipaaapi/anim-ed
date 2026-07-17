<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KitsuService
{
    protected string $baseUrl = 'https://graphql.anilist.co';

    /**
     * Helper to query AniList GraphQL endpoint.
     */
    protected function queryAniList(string $query, array $variables = []): array
    {
        try {
            $response = Http::post($this->baseUrl, [
                'query' => $query,
                'variables' => $variables,
            ]);

            if ($response->failed()) {
                Log::error('AniList API error: ' . $response->body());
                return [];
            }

            return $response->json()['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('AniList API exception: ' . $e->getMessage());
            return [];
        }
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
        query ($page: Int, $perPage: Int, $search: String, $genre: [String], $status: MediaStatus, $sort: [MediaSort], $ageRating: Boolean) {
            Page(page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                    perPage
                }
                media(type: ' . $mediaType . ', search: $search, genre_in: $genre, status: $status, sort: $sort, isAdult: $ageRating) {
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
        if (!empty($filters['ageRating'])) {
            $variables['ageRating'] = $filters['ageRating'] === 'R18';
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
