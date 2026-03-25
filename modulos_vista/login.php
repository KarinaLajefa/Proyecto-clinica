<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FisioClínica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-50 h-screen flex">

    <div class="hidden lg:flex w-1/2 bg-[#0f172a] text-white flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#06b6d4] flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-cyan-500/30">♥</div>
            <div>
                <h1 class="font-bold text-2xl leading-tight">FisioClínica</h1>
                <p class="text-xs text-slate-400 uppercase tracking-widest">Sistema de Gestión</p>
            </div>
        </div>
        <div class="relative z-10">
            <h2 class="text-4xl font-bold mb-4 leading-tight">Gestiona tu clínica<br>de manera <span class="text-cyan-400">inteligente.</span></h2>
        </div>
        <div class="relative z-10 text-sm text-slate-500">&copy; 2026 FisioClínica.</div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 relative">
        <div class="w-full max-w-md bg-white p-10 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-2">¡Hola de nuevo! 👋</h2>
                <p class="text-slate-500">Ingresa tus credenciales para acceder a tu cuenta.</p>
                <div id="alertaError" class="hidden mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 font-semibold"></div>
            </div>

            <form id="formLogin" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Correo Electrónico</label>
                    <input type="email" id="correo" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Contraseña</label>
                    <input type="password" id="password" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 outline-none" required>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#06b6d4] hover:bg-[#0891b2] text-white font-bold rounded-xl transition-colors shadow-lg shadow-cyan-500/30 mt-4">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <script>
        const formLogin = document.getElementById('formLogin');
        const alertaError = document.getElementById('alertaError');

        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const data = {
                correo: document.getElementById('correo').value,
                password: document.getElementById('password').value
            };

            fetch('modulos_api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(respuesta => {
                if(respuesta.status === 'success') {
                    // Si el login es exitoso, recargamos la página (index.php ahora detectará la sesión)
                    window.location.href = 'index.php';
                } else {
                    alertaError.classList.remove('hidden');
                    alertaError.textContent = respuesta.message;
                }
            });
        });
    </script>
</body>
</html>