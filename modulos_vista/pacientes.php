<div class="flex flex-col gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 id="tituloForm" class="text-lg font-bold text-slate-800 mb-6">Registro de Nuevo Paciente</h3>
        
        <form id="formPaciente" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre(s)</label>
                <input type="text" id="nombre" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Apellido Paterno</label>
                <input type="text" id="apellido_p" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Apellido Materno</label>
                <input type="text" id="apellido_m" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Correo Electrónico</label>
                <input type="email" id="correo" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono</label>
                <input type="tel" id="telefono" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nac" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alergias o Lesiones Previas (Expediente)</label>
                <textarea id="alergias" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></textarea>
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#0891b2] hover:bg-[#0e7490] text-white text-sm font-semibold rounded-xl">
                    Guardar Paciente
                </button>
            </div>
        </form>
    </div>

    <div class="mb-4 flex items-center gap-4">
    <div class="relative flex-1">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>
        <input type="text" id="buscadorPaciente" onkeyup="filtrarPacientes()" placeholder="Buscar por nombre o teléfono..." 
               class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#0891b2]">
    </div>
</div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Directorio de Pacientes</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 uppercase text-xs tracking-wider">
                        <th class="pb-3 font-semibold">Paciente</th>
                        <th class="pb-3 font-semibold">Contacto</th>
                        <th class="pb-3 font-semibold">Alergias</th>
                        <th class="pb-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPacientes">
                    </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let listaPacientes = [];
    let editando = false;
    let idPacienteEdicion = null;

    const formPaciente = document.getElementById('formPaciente');
    const tablaPacientes = document.getElementById('tablaPacientes');

    // La ruta correcta segun tu estructura en XAMPP
    const API_URL = '/Clinica_Fisio/modulos_api/pacientes.php';

    document.addEventListener('DOMContentLoaded', cargarPacientes);

    function cargarPacientes() {
        fetch(API_URL) // 
            .then(res => res.json())
            .then(respuesta => {
                if(respuesta.status === 'success') {
                     listaPacientes = respuesta.data;
                    let html = '';
                    respuesta.data.forEach(paciente => {
                        let inicial = paciente.nombre.charAt(0).toUpperCase();
                        let nombreCompleto = `${paciente.nombre} ${paciente.apellido_p} ${paciente.apellido_m}`;
                        
                        html += `
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">${inicial}</div>
                                <span class="font-semibold text-slate-800">${nombreCompleto}</span>
                            </td>
                            <td class="py-4 text-slate-600">${paciente.telefono || 'N/A'}</td>
                            <td class="py-4 text-slate-600">${paciente.alergias || 'Ninguna'}</td>
                           <td class="py-4 text-right">
                               <button onclick='prepararEdicion(${JSON.stringify(paciente)})' 
                                        class="text-cyan-600 hover:text-cyan-800 font-medium text-sm">
                                    Editar
                                </button>
                                
                                <button onclick="eliminarPaciente(${paciente.id_paciente})" 
                                        class="text-red-500 hover:text-red-700 font-medium text-sm ml-3">
                                    Eliminar
                                </button>
                            </td>
                        </tr>`;
                    });
                    tablaPacientes.innerHTML = html;
                }
            })
            .catch(err => console.error("Error al cargar:", err));
    }

    formPaciente.addEventListener('submit', function(e) {
    e.preventDefault(); 

    const data = {
        nombre: document.getElementById('nombre').value,
        apellido_p: document.getElementById('apellido_p').value,
        apellido_m: document.getElementById('apellido_m').value,
        correo: document.getElementById('correo').value,
        telefono: document.getElementById('telefono').value,
        fecha_nac: document.getElementById('fecha_nac').value,
        alergias: document.getElementById('alergias').value
    };

    // Si estamos editando, agregamos el ID y cambiamos el método a PUT
    let metodo = 'POST';
    if (editando) {
        metodo = 'PUT';
        data.id_paciente = idPacienteEdicion;
    }

    fetch(API_URL, {
        method: metodo,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(respuesta => {
        if(respuesta.status === 'success') {
            alert(editando ? 'Paciente actualizado' : 'Paciente registrado');
            resetearFormulario();
            cargarPacientes(); 
        } else {
            alert('Error: ' + respuesta.message);
        }
    });
});

function resetearFormulario() {
    editando = false;
    idPacienteEdicion = null;
    
    // 1. Esto limpia los cuadros de texto (siempre funciona)
    if (formPaciente) {
        formPaciente.reset(); 
    }
    
    // 2. Intentar cambiar textos solo si los IDs existen en tu HTML
    const titulo = document.getElementById('tituloForm');
    const btn = document.getElementById('btnGuardar');
    
    if (titulo) titulo.textContent = "Registro de Nuevo Paciente";
    if (btn) btn.textContent = "Guardar Paciente";
}

    function eliminarPaciente(id_paciente) {
        if(confirm('¿Seguro que deseas eliminar este paciente? Esto borrará su usuario y expediente.')) {
            fetch(API_URL, { // <--- Corregido
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_paciente: id_paciente })
            })
            .then(res => res.json())
            .then(respuesta => {
                if(respuesta.status === 'success') {
                    cargarPacientes(); 
                } else {
                    alert('Error: ' + respuesta.message);
                }
            })
            .catch(err => console.error("Error al eliminar:", err));
        }
    }



function prepararEdicion(paciente) {
    console.log("¡Botón presionado! Datos del paciente:", paciente);
    
    editando = true;
    idPacienteEdicion = paciente.id_paciente;
    
    // Llenar campos con los nombres exactos que vienen de la base de datos
    document.getElementById('nombre').value = paciente.nombre || '';
    document.getElementById('apellido_p').value = paciente.apellido_p || '';
    document.getElementById('apellido_m').value = paciente.apellido_m || '';
    document.getElementById('correo').value = paciente.correo || '';
    document.getElementById('telefono').value = paciente.telefono || '';
    document.getElementById('fecha_nac').value = paciente.fecha_nacimiento || '';
    document.getElementById('alergias').value = paciente.alergias || '';
    
    // Cambiar textos con seguridad
    const titulo = document.getElementById('tituloForm');
    const btn = document.getElementById('btnGuardar');
    
    if (titulo) {
        titulo.textContent = "Editando Paciente";
    } else {
        console.warn("No se encontró el elemento #tituloForm");
    }

    if (btn) {
        btn.textContent = "Actualizar Datos";
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Función para filtrar pacientes en la tabla sin recargar la página
function filtrarPacientes() {
    const input = document.getElementById("buscadorPaciente");
    const filtro = input.value.toLowerCase();
    const tabla = document.getElementById("tablaPacientes"); // ID de tu tbody
    const filas = tabla.getElementsByTagName("tr");

    for (let i = 0; i < filas.length; i++) {
        const textoFila = filas[i].textContent.toLowerCase();
        filas[i].style.display = textoFila.includes(filtro) ? "" : "none";
    }
}

// Función para redirigir al expediente del paciente
function verExpediente(idPaciente) {
    // Redirige al módulo de expedientes pasando el ID del paciente por la URL
    window.location.href = `index.php?modulo=expedientes&id=${idPaciente}`;
}


</script>