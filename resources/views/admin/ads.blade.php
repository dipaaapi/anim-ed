@extends('layouts.admin')

@section('page_title', 'Manage Advertisements')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Ads List Table -->
    <div class="lg:col-span-2 bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md">
        <h2 class="text-base font-bold text-white mb-4">Ad Campaigns</h2>

        @if(count($ads) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-800 text-gray-500 font-semibold">
                            <th class="pb-3 pr-4">Ad details</th>
                            <th class="pb-3 pr-4">Placement</th>
                            <th class="pb-3 pr-4">Stats</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @foreach($ads as $ad)
                            <tr class="text-gray-300">
                                <td class="py-4 pr-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $ad->image_url }}" alt="Ad Banner" class="w-12 h-12 object-cover rounded-lg border border-gray-800">
                                        <div class="overflow-hidden max-w-[150px]">
                                            <h4 class="text-xs font-bold text-white truncate" title="{{ $ad->title }}">{{ $ad->title }}</h4>
                                            <a href="{{ $ad->target_url }}" target="_blank" class="text-[10px] text-gray-500 hover:text-indigo-400 truncate block">{{ $ad->target_url }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider bg-gray-800 text-gray-300 border border-gray-700">
                                        {{ $ad->type }}
                                    </span>
                                </td>
                                <td class="py-4 pr-4 text-xs">
                                    <span class="block text-gray-300">{{ $ad->views }} views</span>
                                    <span class="block text-gray-500 font-medium">{{ $ad->clicks }} clicks</span>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ad->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-800 text-gray-500 border border-gray-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $ad->is_active ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>
                                        {{ $ad->is_active ? 'Active' : 'Paused' }}
                                    </span>
                                </td>
                                <td class="py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Toggle Status Form -->
                                        <form action="{{ route('admin.ads.toggle', ['ad' => $ad->id]) }}" method="POST" class="inline m-0">
                                            @csrf
                                            <button type="submit" class="text-xs bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 px-2.5 py-1 rounded-lg transition-colors font-semibold">
                                                {{ $ad->is_active ? 'Pause' : 'Activate' }}
                                            </button>
                                        </form>
                                        
                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.ads.delete', ['ad' => $ad->id]) }}" method="POST" class="inline m-0" onsubmit="return confirm('Are you sure you want to delete this ad campaign?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 text-rose-400 px-2.5 py-1 rounded-lg transition-colors font-semibold">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-gray-500 py-6 text-center">No campaigns found. Create one using the form on the right.</p>
        @endif
    </div>

    <!-- Create Campaign Form -->
    <div class="bg-[#12121a] border border-gray-800/80 rounded-2xl p-6 shadow-md h-fit">
        <h2 class="text-base font-bold text-white mb-4">Create Campaign</h2>

        <form action="{{ route('admin.ads.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Campaign Title</label>
                <input 
                    type="text" 
                    name="title" 
                    placeholder="e.g. Special Discount on Figures!"
                    required
                    class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Banner Image URL</label>
                <input 
                    type="url" 
                    name="image_url" 
                    placeholder="https://images.unsplash.com/photo-..."
                    required
                    class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Target Link URL</label>
                <input 
                    type="url" 
                    name="target_url" 
                    placeholder="https://sponsor-website.com/product"
                    required
                    class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Placement Target</label>
                <select name="type" class="bg-gray-900 border border-gray-800 text-sm text-gray-300 rounded-xl px-4 py-2.5 w-full cursor-pointer focus:border-indigo-500/50 focus:ring-0 outline-none">
                    <option value="both">Both (Watch & Read)</option>
                    <option value="watch">Watch Page only</option>
                    <option value="read">Read Page only</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-indigo-600/20 active:scale-[0.98] mt-6">
                Publish Campaign
            </button>
        </form>
    </div>

</div>
@endsection
