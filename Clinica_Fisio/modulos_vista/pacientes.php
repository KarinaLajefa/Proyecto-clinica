<div class="flex flex-col gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Registro de Nuevo Paciente</h3>
        
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
    const formPaciente = document.getElementById('formPaciente');
    const tablaPacientes = document.getElementById('tablaPacientes');

    document.addEventListener('DOMContentLoaded', cargarPacientes);

    function cargarPacientes() {
        fetch('/modulos_api/pacientes.php')
            .then(res => res.json())
            .then(respuesta => {
                if(respuesta.status === 'success') {
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
                                <button onclick="eliminarPaciente(${paciente.id_paciente})" class="text-red-500 hover:text-red-700 font-medium text-sm ml-3">Eliminar</button>
                            </td>
                        </tr>`;
                    });
                    tablaPacientes.innerHTML = html;
                }
            });
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
            alergias: document.getElementById('alergias').value,
            lesiones: document.getElementById('alergias').value // Usando el mismo campo para simplificar el alta rápida
        };

        fetch('/modulos_api/pacientes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(respuesta => {
            if(respuesta.status === 'success') {
                formPaciente.reset(); 
                cargarPacientes(); 
            } else {
                alert('Error: ' + respuesta.message);
            }
        });
    });

    function eliminarPaciente(id_paciente) {
        if(confirm('¿Seguro que deseas eliminar este paciente? Esto borrará su usuario y expediente.')) {
            fetch('api/pacientes.php', {
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
            });
        }
    }
</script>