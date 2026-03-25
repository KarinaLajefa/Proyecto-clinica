<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Configuración</h1>
        <p class="text-slate-500 text-sm">Ajustes del sistema</p>
    </div>

    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-1">Configuración del Sistema</h2>
            <p class="text-sm text-slate-400 mb-6">Administra los ajustes generales de la clínica</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nombre de la clínica</label>
                    <input type="text" value="FisioClínica" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:ring-2 focus:ring-cyan-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Teléfono</label>
                    <input type="text" value="555 - 100 - 2000" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:ring-2 focus:ring-cyan-500 outline-none transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Dirección</label>
                    <input type="text" value="Av. Principal #123, Col. Centro" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:ring-2 focus:ring-cyan-500 outline-none transition">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-800">Usuarios del Sistema</h2>
                <button class="text-cyan-600 text-sm font-medium hover:underline">+ Agregar usuario</button>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-50 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-cyan-500 flex items-center justify-center text-white font-bold text-xs">RS</div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Dr. Roberto Sánchez</p>
                            <p class="text-xs text-slate-400">Fisioterapeuta</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cyan-50 text-cyan-600 text-[10px] font-bold uppercase">Fisio</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-50 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold text-xs">LM</div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Dra. Laura Martínez</p>
                            <p class="text-xs text-slate-400">Fisioterapeuta</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cyan-50 text-cyan-600 text-[10px] font-bold uppercase">Fisio</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-cyan-200 transition">
                Guardar Cambios
            </button>
        </div>
    </div>
</div>