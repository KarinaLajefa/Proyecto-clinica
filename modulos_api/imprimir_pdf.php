<?php
require 'db.php';
$id = $_GET['id'] ?? 0;

$sql = "SELECT p.*, u.nombre, u.apellido_p, u.apellido_m 
        FROM pacientes p 
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario 
        WHERE p.id_paciente = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) die("Paciente no encontrado");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Expediente_<?= $p['nombre'] ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-shadow { shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50 p-6 md:p-12">

    <div class="no-print fixed bottom-10 right-10">
        <button onclick="window.print()" class="flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-full shadow-2xl transition-all transform hover:scale-105 font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Imprimir Expediente
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl overflow-hidden shadow-2xl print-shadow">
        <div class="bg-gradient-to-r from-cyan-600 to-blue-700 p-8 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight uppercase">FisioClínica</h1>
                    <p class="text-cyan-100 opacity-80 text-sm font-medium">Sistema de Gestión de Salud v2.0</p>
                </div>
                <div class="text-right">
                    <span class="bg-white/20 backdrop-blur-md px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                        Expediente Digital
                    </span>
                    <p class="mt-2 text-xs opacity-70">ID Paciente: #<?= str_pad($p['id_paciente'], 5, "0", STR_PAD_LEFT) ?></p>
                </div>
            </div>
        </div>

        <div class="p-10">
            <div class="flex items-center gap-6 mb-10 pb-8 border-b border-slate-100">
                <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center text-3xl font-bold text-cyan-600">
                    <?= strtoupper($p['nombre'][0]) ?>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800"><?= $p['nombre'] . " " . $p['apellido_p'] . " " . $p['apellido_m'] ?></h2>
                    <p class="text-slate-500 font-medium italic">Paciente Registrado</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-1">Información de Contacto</h3>
                        <p class="text-slate-700 font-semibold border-l-4 border-cyan-100 pl-3">
                            <?= $p['telefono'] ?: 'No disponible' ?>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-1">Fecha de Nacimiento</h3>
                        <p class="text-slate-700 font-semibold border-l-4 border-cyan-100 pl-3">
                            <?= $p['fecha_nacimiento'] ?: 'N/A' ?>
                        </p>
                    </div>
                </div>

                    <div>
                        <h3 class="text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-1">Género</h3>
                        <p class="text-slate-700 font-semibold border-l-4 border-cyan-100 pl-3">
                            <?= $p['genero'] ?: 'No especificado' ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-10 bg-slate-50 p-6 rounded-2xl">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Dirección Registrada</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    <?= $p['direccion'] ?: 'Sin domicilio registrado en el sistema.' ?>
                </p>
            </div>

            <div class="mt-16 pt-8 border-t border-slate-100 flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                <p>Generado automáticamente el <?= date('d/m/Y H:i') ?></p>
                <p>FisioClínica - Documento de carácter confidencial</p>
            </div>
        </div>
    </div>
</body>
</html>