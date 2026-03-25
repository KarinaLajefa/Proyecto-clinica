<aside class="w-[260px] bg-[#111827] text-slate-300 flex flex-col h-full transition-all duration-300 z-20">
    <div class="h-20 flex items-center px-6 gap-3">
        <div class="w-8 h-8 rounded-lg bg-cyan-400 flex items-center justify-center text-white font-bold text-xl">
            ♥
        </div>
        <div>
            <h1 class="text-white font-bold text-lg leading-tight">FisioClínica</h1>
            <p class="text-[10px] text-slate-400">Sistema de Gestión</p>
        </div>
    </div>

    <div class="px-4 py-4">
        <div class="bg-[#1f2937] rounded-xl p-3 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-cyan-500 text-white flex items-center justify-center font-bold">
                <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
            </div>
            <div>
                <p class="text-sm font-bold text-white"><?= $_SESSION['nombre'] ?></p>
                <p class="text-xs text-slate-400 capitalize"><?= $rol ?></p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
        <p class="text-xs font-semibold text-slate-500 mb-2 mt-4 px-2 tracking-wider">MENÚ PRINCIPAL</p>
        
        <?php 
        $menuItems = [
            'dashboard' => ['icono' => '⊞', 'texto' => 'Dashboard', 'roles' => ['supervisor', 'fisio', 'recepcion']],
            'sesiones' => ['icono' => '📅', 'texto' => 'Control de Sesiones', 'roles' => ['supervisor', 'fisio', 'recepcion', 'paciente']],
            'expediente' => ['icono' => '📄', 'texto' => 'Expediente', 'roles' => ['supervisor', 'fisio']],
            'pacientes' => ['icono' => '👥', 'texto' => 'Pacientes', 'roles' => ['supervisor', 'recepcion']],
            'pagos' => ['icono' => '💳', 'texto' => 'Pagos', 'roles' => ['supervisor', 'recepcion']]
        ];

        foreach ($menuItems as $key => $item): 
            if (in_array($rol, $item['roles'])):
                $isActive = ($modulo == $key);
                $bgClass = $isActive ? 'bg-[#1f2937] text-white border-l-4 border-cyan-400' : 'hover:bg-[#1f2937] hover:text-white border-l-4 border-transparent';
        ?>
            <a href="index.php?modulo=<?= $key ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= $bgClass ?>">
                <span class="text-lg opacity-80"><?= $item['icono'] ?></span>
                <?= $item['texto'] ?>
            </a>
        <?php 
            endif;
        endforeach; 
        ?>
    </nav>

    <div class="p-4 border-t border-slate-700/50">
        <a href="index.php?logout=true" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-400 hover:text-white hover:bg-[#1f2937] rounded-lg transition-colors">
            <span class="text-lg opacity-80">↪</span> Cerrar Sesión
        </a>
    </div>
</aside>