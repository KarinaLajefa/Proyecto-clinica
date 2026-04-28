<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Pagos</h1>
        <p class="text-slate-500 text-sm">Registro y control de ingresos</p>
    </div>

    <div class="max-w-6xl mx-auto space-y-8">
        <form id="formPago" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h2 id="tituloForm" class="text-lg font-semibold text-slate-800 mb-6">Registrar Nuevo Pago</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Paciente</label>
                    <select id="id_paciente" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-cyan-500" required>
                        <option value="">Cargando pacientes...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tratamiento</label>
                    <select id="id_tratamiento" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-cyan-500" required>
                        <option value="">Cargando tratamientos...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Método de Pago</label>
                    <select id="id_metodo" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-cyan-500" required>
                        <option value="">Cargando métodos...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Monto ($)</label>
                    <input type="number" step="0.01" id="monto" placeholder="0.00" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-cyan-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Fecha de Pago</label>
                    <input type="date" id="fecha_pago" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-cyan-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Estado</label>
                    <select id="estado" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-cyan-500">
                        <option value="Completado">Completado</option>
                        <option value="Pendiente">Pendiente</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" id="btnPago" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition">
                    Registrar Pago
                </button>
            </div>
        </form>

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
</div>

<script>
const API_PAGOS = '/Clinica_Fisio/modulos_api/pagos.php';

let listaPagos = [];
let editandoPago = false;
let idPagoEdit = null;

document.addEventListener('DOMContentLoaded', cargarDatosIniciales);

function cargarDatosIniciales() {
    fetch(API_PAGOS)
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            // 1. Guardar y mostrar historial
            listaPagos = res.data.historial;
            renderizarTabla(listaPagos);

            // 2. Llenar Select de Pacientes
            const selectPac = document.getElementById('id_paciente');
            selectPac.innerHTML = '<option value="">Seleccione Paciente</option>';
            res.data.catalogos.pacientes.forEach(p => {
                selectPac.innerHTML += `<option value="${p.id_paciente}">${p.nombre} ${p.apellido_p}</option>`;
            });

            // 3. Llenar Select de Tratamientos
            const selectTrat = document.getElementById('id_tratamiento');
            selectTrat.innerHTML = '<option value="">Seleccione Tratamiento</option>';
            res.data.catalogos.tratamientos.forEach(t => {
                selectTrat.innerHTML += `<option value="${t.id_tratamiento}">${t.tipo_terapia}</option>`;
            });

            // 4. Llenar Select de Métodos
            const selectMet = document.getElementById('id_metodo');
            selectMet.innerHTML = '<option value="">Seleccione Método</option>';
            res.data.catalogos.metodos.forEach(m => {
                selectMet.innerHTML += `<option value="${m.id_metodo}">${m.nombre_metodo}</option>`;
            });
        }
    });
}

// Separamos la renderización para poder llamarla al cargar o editar
function renderizarTabla(datos) {
    let html = '';
    datos.forEach(p => {
        html += `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
            <td class="py-4">${p.fecha_pago}</td>
            <td class="py-4 font-medium text-slate-700">${p.nombre} ${p.apellido_p}</td>
            <td class="py-4">$${p.monto} - <span class="text-slate-400">${p.nombre_metodo}</span></td>
            <td class="py-4">
                <span class="px-2 py-1 rounded-lg text-xs font-bold ${p.estado === 'Completado' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'}">
                    ${p.estado}
                </span>
            </td>
            <td class="py-4 text-right space-x-2">
                <button onclick="editarPago(${p.id_pago})" class="text-cyan-600 hover:underline font-semibold">Editar</button>
                <button onclick="eliminarPago(${p.id_pago})" class="text-red-400 hover:underline font-semibold">Eliminar</button>
            </td>
        </tr>`;
    });
    document.getElementById('tablaPagos').innerHTML = html;
}

// Función cargarPagos (solo para refrescar la tabla)
function cargarPagos() {
    fetch(API_PAGOS)
    .then(res => res.json())
    .then(res => {
        listaPagos = res.data.historial;
        renderizarTabla(listaPagos);
    });
}

document.getElementById('formPago').addEventListener('submit', function(e) {
    e.preventDefault();

    let data = {
        id_paciente: document.getElementById('id_paciente').value,
        id_tratamiento: document.getElementById('id_tratamiento').value,
        id_metodo: document.getElementById('id_metodo').value,
        monto: document.getElementById('monto').value,
        fecha_pago: document.getElementById('fecha_pago').value,
        estado: document.getElementById('estado').value
    };

    let metodo = editandoPago ? 'PUT' : 'POST';
    if(editandoPago) data.id_pago = idPagoEdit;

    fetch(API_PAGOS, {
        method: metodo,
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            alert(editandoPago ? "Pago actualizado" : "Pago registrado");
            resetPago();
            cargarPagos();
        } else {
            alert("Error: " + res.message);
        }
    });
});

function editarPago(id){
    let p = listaPagos.find(x => x.id_pago == id);
    if(!p) return;

    editandoPago = true;
    idPagoEdit = id;

    document.getElementById('id_paciente').value = p.id_paciente;
    document.getElementById('id_tratamiento').value = p.id_tratamiento;
    document.getElementById('id_metodo').value = p.id_metodo;
    document.getElementById('monto').value = p.monto;
    document.getElementById('fecha_pago').value = p.fecha_pago;
    document.getElementById('estado').value = p.estado;

    document.getElementById('btnPago').textContent = "Actualizar Pago";
    document.getElementById('tituloForm').textContent = "Editando Pago #" + id;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function eliminarPago(id){
    if(confirm("¿Eliminar pago?")){
        fetch(API_PAGOS,{
            method:'DELETE',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({id_pago:id})
        })
        .then(()=> cargarPagos());
    }
}

function resetPago(){
    editandoPago = false;
    idPagoEdit = null;
    document.getElementById('formPago').reset();
    document.getElementById('btnPago').textContent = "Registrar Pago";
    document.getElementById('tituloForm').textContent = "Registrar Nuevo Pago";
}
</script>