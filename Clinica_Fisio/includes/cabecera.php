<header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-10 sticky top-0">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 capitalize"><?= str_replace('_', ' ', $modulo) ?></h2>
        <p class="text-sm text-slate-500">Resumen general del sistema</p>
    </div>

    <div class="flex items-center gap-6">
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
            <input type="text" placeholder="Buscar..." class="w-64 bg-slate-100 text-sm border-none rounded-full py-2 pl-10 pr-4 focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
        </div>
        
        <button class="relative text-slate-400 hover:text-cyan-600 transition-colors">
            <span class="text-xl">🔔</span>
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">3</span>
        </button>
    </div>
</header>