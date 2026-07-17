@extends('layouts.admin')

@section('page_title', 'System Overview')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Stat 1 -->
    <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md relative overflow-hidden">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total Ads Served</h3>
        <p class="text-3xl font-extrabold text-white">{{ number_format($totalViews) }}</p>
        <span class="text-xs text-gray-600 block mt-1">Impressions counted on details pages</span>
        <div class="absolute -right-4 -bottom-4 text-gray-800/30 font-black text-6xl select-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md relative overflow-hidden">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ad Clicks</h3>
        <p class="text-3xl font-extrabold text-indigo-400">{{ number_format($totalClicks) }}</p>
        <span class="text-xs text-gray-600 block mt-1">
            CTR: {{ $totalViews > 0 ? number_format(($totalClicks / $totalViews) * 100, 2) : 0 }}%
        </span>
        <div class="absolute -right-4 -bottom-4 text-gray-800/30 font-black text-6xl select-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
            </svg>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md relative overflow-hidden">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Active Campaigns</h3>
        <p class="text-3xl font-extrabold text-white">{{ $totalAds }}</p>
        <span class="text-xs text-gray-600 block mt-1">Available ad banners</span>
        <div class="absolute -right-4 -bottom-4 text-gray-800/30 font-black text-6xl select-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md relative overflow-hidden">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Member Activities</h3>
        <p class="text-3xl font-extrabold text-white">{{ $totalActivities }}</p>
        <span class="text-xs text-gray-600 block mt-1">Total page-triggered watch/read actions</span>
        <div class="absolute -right-4 -bottom-4 text-gray-800/30 font-black text-6xl select-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
    </div>

</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- User Activity Log -->
    <div class="lg:col-span-2 bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md">
        <h2 class="text-base font-bold text-white mb-4">Recent Watch & Read Activities</h2>
        
        @if(count($recentActivities) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-800 text-gray-500 font-semibold">
                            <th class="pb-3 pr-4">User</th>
                            <th class="pb-3 pr-4">Type</th>
                            <th class="pb-3 pr-4">Title</th>
                            <th class="pb-3 pr-4">Episode/Chapter</th>
                            <th class="pb-3 text-right">Logged At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @foreach($recentActivities as $activity)
                            <tr class="text-gray-300">
                                <td class="py-3 pr-4 font-semibold text-white">
                                    {{ $activity->user->name ?? 'Deleted User' }}
                                    <span class="block text-[10px] text-gray-500 font-normal">{{ $activity->user->email ?? '' }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $activity->item_type === 'manga' ? 'bg-purple-500/10 text-purple-400 border border-purple-400/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-400/20' }}">
                                        {{ $activity->item_type }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 truncate max-w-[180px]" title="{{ $activity->title }}">
                                    {{ $activity->title }}
                                </td>
                                <td class="py-3 pr-4 font-mono text-xs text-gray-400">
                                    {{ $activity->episode_or_chapter ?? 'N/A' }}
                                </td>
                                <td class="py-3 text-right text-xs text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-gray-500 py-6 text-center">No user watch/read activities recorded yet.</p>
        @endif
    </div>

    <!-- Ad Performance Breakdown -->
    <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md">
        <h2 class="text-base font-bold text-white mb-4">Ad Campaigns Performance</h2>
        
        @if(count($ads) > 0)
            <div class="space-y-4">
                @foreach($ads as $ad)
                    <div class="p-3 bg-gray-900/30 border border-gray-800/60 rounded-xl flex items-center justify-between gap-3">
                        <div class="overflow-hidden flex-grow">
                            <h4 class="text-xs font-bold text-white truncate" title="{{ $ad->title }}">{{ $ad->title }}</h4>
                            <p class="text-[10px] text-gray-500 capitalize">Type: {{ $ad->type }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs font-bold text-gray-300 block">{{ $ad->views }} views</span>
                            <span class="text-[10px] text-indigo-400 font-semibold">{{ $ad->clicks }} clicks</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-500 py-6 text-center">No advertisement campaigns configured.</p>
        @endif
    </div>

</div>
@endsection
