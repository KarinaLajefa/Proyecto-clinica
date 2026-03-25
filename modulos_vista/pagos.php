<div class="flex flex-col gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Registrar Nuevo Pago</h3>
        <form id="formPago" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ID Paciente</label>
                <input type="number" id="id_paciente" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ID Tratamiento</label>
                <input type="number" id="id_tratamiento" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ID Método de Pago</label>
                <input type="number" id="id_metodo" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" placeholder="Ej. 1 para Efectivo">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Monto ($)</label>
                <input type="number" step="0.01" id="monto" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Pago</label>
                <input type="date" id="fecha_pago" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                <select id="estado" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none cursor-pointer">
                    <option value="Completado">Completado</option>
                    <option value="Pendiente">Pendiente</option>
                </select>
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#0891b2] hover:bg-[#0e7490] text-white text-sm font-semibold rounded-xl">Registrar Pago</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Historial de Pagos</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 uppercase text-xs tracking-wider">
                        <th class="pb-3 font-semibold">Fecha</th>
                        <th class="pb-3 font-semibold">Paciente</th>
                        <th class="pb-3 font-semibold">Monto / Método</th>
                        <th class="pb-3 font-semibold">Estado</th>
                        <th class="pb-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPagos"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', cargarPagos);

    function cargarPagos() {
        fetch('api/pagos.php')
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    let html = '';
                    res.data.forEach(p => {
                        let badgeClass = p.estado === 'Completado' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600';
                        html += `
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-4 text-slate-600 font-medium">${p.fecha_pago}</td>
                            <td class="py-4 font-semibold text-slate-800">${p.nombre} ${p.apellido_p}</td>
                            <td class="py-4"><span class="font-bold text-slate-800">$${p.monto}</span> <span class="text-xs text-slate-400 ml-1">(${p.nombre_metodo})</span></td>
                            <td class="py-4"><span class="px-3 py-1 text-[11px] font-bold uppercase rounded-md ${badgeClass}">${p.estado}</span></td>
                            <td class="py-4 text-right">
                                <button onclick="eliminarPago(${p.id_pago})" class="text-red-500 hover:text-red-700 font-medium text-sm">Eliminar</button>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('tablaPagos').innerHTML = html;
                }
            });
    }

    document.getElementById('formPago').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            id_paciente: document.getElementById('id_paciente').value,
            id_tratamiento: document.getElementById('id_tratamiento').value,
            id_metodo: document.getElementById('id_metodo').value,
            monto: document.getElementById('monto').value,
            fecha_pago: document.getElementById('fecha_pago').value,
            estado: document.getElementById('estado').value
        };
        fetch('api/pagos.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) })
        .then(res => res.json()).then(res => {
            if(res.status === 'success') { document.getElementById('formPago').reset(); cargarPagos(); }
            else { alert('Error: ' + res.message); }
        });
    });

    function eliminarPago(id) {
        if(confirm('¿Eliminar este registro de pago?')) {
            fetch('api/pagos.php', { method: 'DELETE', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id_pago: id}) })
            .then(res => res.json()).then(() => cargarPagos());
        }
    }
</script>