<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h2 class="text-lg font-bold text-slate-800 mb-6">Configuración del Sistema</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="text" id="conf_nombre" placeholder="Nombre de la clínica" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50">
                <input type="text" id="conf_telefono" placeholder="Teléfono" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-800">Usuarios del Sistema</h2>
                <button onclick="abrirModal()" class="text-cyan-600 text-sm font-bold hover:underline">+ Agregar usuario</button>
            </div>
            <div id="contenedor-usuarios" class="space-y-3"></div>
        </div>

        <div class="flex justify-end">
            <button class="bg-cyan-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg">Guardar Cambios</button>
        </div>
    </div>
</div>

<div id="modalUsuario" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8">
        <h3 class="text-xl font-bold mb-6">Nuevo Usuario</h3>
        <form id="formUsuario" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" id="u_nombre" placeholder="Nombre" required class="p-3 border rounded-xl w-full">
                <input type="text" id="u_apellido" placeholder="Apellido" required class="p-3 border rounded-xl w-full">
            </div>
            <input type="email" id="u_correo" placeholder="Email" required class="w-full p-3 border rounded-xl">
            <input type="password" id="u_pass" placeholder="Contraseña" required class="w-full p-3 border rounded-xl">
            <select id="u_rol" class="w-full p-3 border rounded-xl">
                <option value="2">Fisioterapeuta</option>
                <option value="3">Recepcionista</option>
                <option value="1">Supervisor</option>
            </select>
            <div class="flex gap-2">
                <button type="button" onclick="cerrarModal()" class="flex-1 py-3 text-slate-500">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-cyan-500 text-white font-bold rounded-xl">Crear</button>
            </div>
        </form>
    </div>
</div>

<script>
function cargarUsuarios() {
    // Se usa la ruta correcta detectada en consola: modulos_api/
    fetch('modulos_api/configuracion.php')
        .then(res => res.json())
        .then(data => {
            const cont = document.getElementById('contenedor-usuarios');
            cont.innerHTML = '';
            data.forEach(u => {
                const iniciales = (u.nombre[0] + u.apellido_p[0]).toUpperCase();
                cont.innerHTML += `
                <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-cyan-500 flex items-center justify-center text-white font-bold text-xs">${iniciales}</div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">${u.nombre} ${u.apellido_p}</p>
                            <p class="text-[10px] text-slate-400 uppercase">${u.nombre_rol}</p>
                        </div>
                    </div>
                </div>`;
            });
        });
}

function abrirModal() { document.getElementById('modalUsuario').classList.remove('hidden'); }
function cerrarModal() { document.getElementById('modalUsuario').classList.add('hidden'); }

document.getElementById('formUsuario').onsubmit = function(e) {
    e.preventDefault();
    const payload = {
        nombre: document.getElementById('u_nombre').value,
        apellido: document.getElementById('u_apellido').value,
        correo: document.getElementById('u_correo').value,
        password: document.getElementById('u_pass').value,
        id_rol: document.getElementById('u_rol').value
    };

    fetch('modulos_api/configuracion.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    }).then(() => { cerrarModal(); cargarUsuarios(); });
};

document.addEventListener('DOMContentLoaded', cargarUsuarios);
</script>