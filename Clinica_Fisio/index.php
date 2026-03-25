<?php
session_start();

// 1. PROTECCIÓN DE RUTA: Si no existe la sesión, mostramos la vista de login y detenemos todo.
if (!isset($_SESSION['id_usuario'])) {
    require 'modulos_vista/login.php';
    exit;
}

// 2. ENRUTADOR: Detectamos qué módulo quiere ver el usuario (por defecto 'dashboard')
$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FisioClínica - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#0f172a] text-white flex flex-col h-full hidden md:flex">
        <div class="h-20 flex items-center px-6 gap-3 border-b border-slate-800">
            <div class="w-8 h-8 rounded-lg bg-[#06b6d4] flex items-center justify-center font-bold text-xl">♥</div>
            <span class="font-bold text-xl tracking-wide">FisioClínica</span>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="index.php?modulo=dashboard" class="flex items-center gap-3 px-4 py-3 <?= $modulo == 'dashboard' ? 'bg-[#06b6d4] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?> rounded-xl transition-colors">
                🏠 <span class="font-medium text-sm">Dashboard</span>
            </a>
            <a href="index.php?modulo=pacientes" class="flex items-center gap-3 px-4 py-3 <?= $modulo == 'pacientes' ? 'bg-[#06b6d4] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?> rounded-xl transition-colors">
                👥 <span class="font-medium text-sm">Pacientes</span>
            </a>
            <a href="index.php?modulo=sesiones" class="flex items-center gap-3 px-4 py-3 <?= $modulo == 'sesiones' ? 'bg-[#06b6d4] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?> rounded-xl transition-colors">
                📅 <span class="font-medium text-sm">Sesiones</span>
            </a>
            <a href="index.php?modulo=pagos" class="flex items-center gap-3 px-4 py-3 <?= $modulo == 'pagos' ? 'bg-[#06b6d4] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?> rounded-xl transition-colors">
                💳 <span class="font-medium text-sm">Pagos</span>
            </a>
             <a href="index.php?modulo=expediente" class="flex items-center gap-3 px-4 py-3 <?= $modulo == 'expedientes' ? 'bg-[#06b6d4] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?> rounded-xl transition-colors">
                 👥<span class="font-medium text-sm">Expedientes</span>
            </a>
             <a href="index.php?modulo=configuracion" class="flex items-center gap-3 px-4 py-3 <?= $modulo == 'configuracion' ? 'bg-[#06b6d4] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?> rounded-xl transition-colors">
                 <span class="font-medium text-sm">Configuracion</span>
            </a>
        </nav>

        <div class="p-4">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition-colors">
                🚪 <span class="font-medium text-sm">Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 z-10">
            <h2 class="text-2xl font-bold text-slate-800 capitalize"><?= $modulo ?></h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-800"><?= $_SESSION['nombre_completo'] ?></p>
                    <p class="text-xs text-slate-500 font-medium"><?= $_SESSION['rol'] ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center text-cyan-600 font-bold border border-cyan-200">
                    <?= substr($_SESSION['nombre_completo'], 0, 1) ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-8">
            <?php 
                // Carga el archivo según el menú que hayas clickeado
                $ruta_vista = "modulos_vista/" . $modulo . ".php";
                if (file_exists($ruta_vista)) {
                    require $ruta_vista;
                } else {
                    echo "<div class='p-4 bg-yellow-50 text-yellow-700 rounded-xl font-bold'>El módulo '$modulo' aún no ha sido creado.</div>";
                }
            ?>
        </main>
    </div>

</body>
</html>