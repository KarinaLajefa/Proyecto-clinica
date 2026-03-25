<div class="flex flex-col gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Programar Nueva Sesión</h3>
        <form id="formSesion" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ID Tratamiento</label>
                <input type="number" id="id_tratamiento" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" placeholder="Ej. 1">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ID Fisioterapeuta</label>
                <input type="number" id="id_fisio" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" placeholder="Ej. 1">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                <select id="estado" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none cursor-pointer">
                    <option value="Programada">Programada</option>
                    <option value="Completada">Completada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Fecha</label>
                <input type="date" id="fecha" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Hora</label>
                <input type="time" id="hora" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Observaciones</label>
                <input type="text" id="observaciones" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" placeholder="Opcional">
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#0891b2] hover:bg-[#0e7490] text-white text-sm font-semibold rounded-xl">Guardar Sesión</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Agenda de Sesiones</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 uppercase text-xs tracking-wider">
                        <th class="pb-3 font-semibold">Fecha y Hora</th>
                        <th class="pb-3 font-semibold">Paciente / Terapia</th>
                        <th class="pb-3 font-semibold">Estado</th>
                        <th class="pb-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaSesiones"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', cargarSesiones);

    function cargarSesiones() {
        fetch('api/sesiones.php')
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    let html = '';
                    res.data.forEach(s => {
                        let badgeClass = s.estado === 'Completada' ? 'bg-emerald-50 text-emerald-600' : (s.estado === 'Cancelada' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600');
                        html += `
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-4"><span class="font-bold text-slate-700">${s.fecha}</span> <span class="text-slate-400 ml-2">${s.hora}</span></td>
                            <td class="py-4"><p class="font-semibold text-slate-800">${s.nombre} ${s.apellido_p}</p><p class="text-xs text-slate-500">${s.tipo_terapia}</p></td>
                            <td class="py-4"><span class="px-3 py-1 text-[11px] font-bold uppercase rounded-md ${badgeClass}">${s.estado}</span></td>
                            <td class="py-4 text-right">
                                <button onclick="eliminarSesion(${s.id_sesion})" class="text-red-500 hover:text-red-700 font-medium text-sm">Eliminar</button>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('tablaSesiones').innerHTML = html;
                }
            });
    }

    document.getElementById('formSesion').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            id_tratamiento: document.getElementById('id_tratamiento').value,
            id_fisio: document.getElementById('id_fisio').value,
            estado: document.getElementById('estado').value,
            fecha: document.getElementById('fecha').value,
            hora: document.getElementById('hora').value,
            observaciones: document.getElementById('observaciones').value
        };
        fetch('api/sesiones.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) })
        .then(res => res.json()).then(res => {
            if(res.status === 'success') { document.getElementById('formSesion').reset(); cargarSesiones(); }
            else { alert('Error: ' + res.message); }
        });
    });

    function eliminarSesion(id) {
        if(confirm('¿Eliminar sesión?')) {
            fetch('api/sesiones.php', { method: 'DELETE', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id_sesion: id}) })
            .then(res => res.json()).then(() => cargarSesiones());
        }
    }
</script>