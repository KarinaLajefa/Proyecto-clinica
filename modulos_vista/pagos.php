<div class="flex flex-col gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 id="tituloForm" class="text-lg font-bold text-slate-800 mb-6">Registrar Nuevo Pago</h3>
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
                <button id="btnPago" type="submit"
                    class="px-6 py-2.5 bg-[#0891b2] hover:bg-[#0e7490] text-white text-sm font-semibold rounded-xl">
                        Registrar Pago
                    </button>
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

const API_PAGOS = '/Clinica_Fisio/modulos_api/pagos.php';

let listaPagos = [];
let editandoPago = false;
let idPagoEdit = null;

document.addEventListener('DOMContentLoaded', cargarPagos);

function cargarPagos() {
    fetch(API_PAGOS)
    .then(res => res.json())
    .then(res => {

        listaPagos = res.data;

        let html = '';

        res.data.forEach(p => {

           html += `
            <tr>
                <td>${p.fecha_pago}</td>
                <td>${p.nombre} ${p.apellido_p}</td>
                <td>$${p.monto} - ${p.nombre_metodo}</td>
                <td>${p.estado}</td>
                <td>
                    <button onclick="editarPago(${p.id_pago})">Editar</button>
                    <button onclick="eliminarPago(${p.id_pago})">Eliminar</button>
                </td>
            </tr>`;
        });

        document.getElementById('tablaPagos').innerHTML = html;
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

    let metodo = 'POST';

    if(editandoPago){
        metodo = 'PUT';
        data.id_pago = idPagoEdit;
    }

    fetch(API_PAGOS, {
        method: metodo,
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
.then(res => {
    if(res.status === 'success') {
        alert(editandoPago ? "Pago actualizado" : "Pago registrado");
        resetPago(); // Esto ahora funcionará sin errores
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

    // Llenar campos
    document.getElementById('id_paciente').value = p.id_paciente;
    document.getElementById('id_tratamiento').value = p.id_tratamiento;
    document.getElementById('id_metodo').value = p.id_metodo;
    document.getElementById('monto').value = p.monto;
    document.getElementById('fecha_pago').value = p.fecha_pago;
    document.getElementById('estado').value = p.estado;

    // Cambiar textos
    document.getElementById('btnPago').textContent = "Actualizar Pago";
    document.getElementById('tituloForm').textContent = "Editando Pago #" + id;

    // Scroll suave hacia arriba para ver el formulario
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

    // Reinicia los campos del formulario
    const form = document.getElementById('formPago');
    if(form) form.reset();

    // Actualiza los textos con los IDs correctos del HTML
    const btn = document.getElementById('btnPago');
    const titulo = document.getElementById('tituloForm'); // Cambiado de tituloPago a tituloForm

    if(btn) btn.textContent = "Registrar Pago";
    if(titulo) titulo.textContent = "Registrar Nuevo Pago";
}
</script>