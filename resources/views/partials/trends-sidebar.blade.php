<div class="bg-[#12121a]/60 border border-gray-800/60 rounded-3xl p-6 shadow-2xl backdrop-blur-md relative overflow-hidden">
    <!-- Glow effect -->
    <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex items-center justify-between mb-6 relative z-10">
        <h2 class="text-xl font-black text-white flex items-center gap-2">
            <span class="w-2.5 h-6 rounded bg-gradient-to-b from-indigo-500 to-purple-600"></span>
            Top 10 Trends
        </h2>
        <span class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/25 px-2.5 py-0.5 rounded-full capitalize">
            {{ $type ?? 'anime' }}
        </span>
    </div>

    <!-- Tab Buttons -->
    <div class="flex bg-gray-950/60 p-1 rounded-xl gap-1 mb-6 relative z-10 border border-gray-800/40">
        <button 
            type="button" 
            onclick="switchTrendTab('weekly')" 
            id="tab-btn-weekly" 
            class="flex-1 text-center py-2 px-1 text-xs font-bold rounded-lg transition-all text-white bg-indigo-600 shadow-md shadow-indigo-600/10"
        >
            Weekly
        </button>
        <button 
            type="button" 
            onclick="switchTrendTab('monthly')" 
            id="tab-btn-monthly" 
            class="flex-1 text-center py-2 px-1 text-xs font-bold rounded-lg transition-all text-gray-400 hover:text-white"
        >
            Monthly
        </button>
        <button 
            type="button" 
            onclick="switchTrendTab('yearly')" 
            id="tab-btn-yearly" 
            class="flex-1 text-center py-2 px-1 text-xs font-bold rounded-lg transition-all text-gray-400 hover:text-white"
        >
            Yearly
        </button>
    </div>

    <!-- Tab Panels -->
    <div class="space-y-4 relative z-10">
        <!-- Weekly Tab -->
        <div id="panel-weekly" class="trend-panel space-y-3.5">
            @foreach($weeklyTrend as $index => $item)
                @include('partials.trend-item-row', ['item' => $item, 'rank' => $index + 1, 'type' => $type ?? 'anime'])
            @endforeach
        </div>

        <!-- Monthly Tab -->
        <div id="panel-monthly" class="trend-panel hidden space-y-3.5">
            @foreach($monthlyTrend as $index => $item)
                @include('partials.trend-item-row', ['item' => $item, 'rank' => $index + 1, 'type' => $type ?? 'anime'])
            @endforeach
        </div>

        <!-- Yearly Tab -->
        <div id="panel-yearly" class="trend-panel hidden space-y-3.5">
            @foreach($yearlyTrend as $index => $item)
                @include('partials.trend-item-row', ['item' => $item, 'rank' => $index + 1, 'type' => $type ?? 'anime'])
            @endforeach
        </div>
    </div>
</div>

<script>
    function switchTrendTab(tab) {
        // Hide all panels
        document.querySelectorAll('.trend-panel').forEach(p => p.classList.add('hidden'));
        
        // Remove active class from buttons
        const btns = ['weekly', 'monthly', 'yearly'];
        btns.forEach(b => {
            const btn = document.getElementById('tab-btn-' + b);
            if (btn) {
                btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-600/10');
                btn.classList.add('text-gray-400', 'hover:text-white');
            }
        });

        // Show active panel
        const activePanel = document.getElementById('panel-' + tab);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        // Set active button style
        const activeBtn = document.getElementById('tab-btn-' + tab);
        if (activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-600/10');
            activeBtn.classList.remove('text-gray-400', 'hover:text-white');
        }
    }
</script>
