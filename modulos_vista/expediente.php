<div class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8 text-white">
    <h2 class="text-xl font-bold mb-4 text-cyan-400">Nuevo Expediente Clínico</h2>
    <form id="formExpediente" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="text-xs text-gray-400">ID del Paciente</label>
            <input type="number" id="id_paciente" placeholder="Ej. 1" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600" required>
        </div>
        
        <div>
            <label class="text-xs text-gray-400">Antecedentes</label>
            <textarea id="antecedentes" placeholder="Historia médica..." class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600"></textarea>
        </div>

        <div>
            <label class="text-xs text-gray-400">Alergias</label>
            <textarea id="alergias" placeholder="Medicamentos, alimentos..." class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600"></textarea>
        </div>

        <div>
            <label class="text-xs text-gray-400">Lesiones Previas</label>
            <textarea id="lesiones_previas" placeholder="Fracturas, esguinces..." class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600"></textarea>
        </div>

        <div>
            <label class="text-xs text-gray-400">Notas Generales</label>
            <textarea id="notas_generales" placeholder="Observaciones adicionales..." class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600"></textarea>
        </div>

        <div class="md:col-span-2">
            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded transition-colors">
                Guardar Expediente
            </button>
        </div>
    </form>
    <div id="mensajeAlerta" class="mt-3 text-sm hidden"></div>
</div>

<div class="bg-gray-800 p-6 rounded-lg shadow-lg text-white">
    <h2 class="text-xl font-bold mb-4 text-cyan-400">Historial de Expedientes</h2>
    <div class="overflow-x-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-lg font-bold text-slate-800">Buscar Expediente Clínico</h3>
        
        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" id="buscarExpediente" onkeyup="filtrarExpedientes()" 
                   placeholder="Escriba nombre o apellido del paciente..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#0891b2] transition-all">
        </div>
    </div>
</div>
    <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-700 text-gray-400 text-xs uppercase">
                    <th class="py-2">Paciente</th>
                    <th class="py-2">Antecedentes</th>
                    <th class="py-2">Alergias</th>
                    <th class="py-2">Lesiones</th>
                    <th class="py-2">Notas</th>
                </tr>
            </thead>
        <tbody id="tablaExpedientes" class="text-sm">
            </tbody>
    </table>
</div>

<script>
    // 1. Función para CARGAR los expedientes al abrir la página
    function cargarExpedientes() {
        fetch('modulos_api/expediente.php')
        .then(res => res.json())
        .then(respuesta => {
            if(respuesta.status === 'success') {
                const tbody = document.getElementById('tablaExpedientes');
                tbody.innerHTML = ''; // Limpiamos la tabla
                
                respuesta.data.forEach(exp => {
                    tbody.innerHTML += `
                        <tr class="border-b border-gray-700 hover:bg-gray-750 transition-colors">
                            <td class="py-3">
                                <div class="font-bold text-cyan-300">${exp.nombre} ${exp.apellido_p}</div>
                                <div class="text-xs text-gray-500">${exp.telefono}</div>
                            </td>
                            <td class="py-3 text-gray-300">${exp.antecedentes || 'Sin datos'}</td>
                            <td class="py-3">
                                <span class="${exp.alergias && exp.alergias !== 'Ninguna' ? 'text-red-400' : 'text-gray-400'}">
                                    ${exp.alergias || 'Ninguna'}
                                </span>
                            </td>
                            <td class="py-3 text-gray-300">${exp.lesiones_previas || 'N/A'}</td>
                            <td class="py-3 text-xs italic text-gray-500">${exp.notas_generales || 'NA'}</td>
                        </tr>
                    `;
                });
            }
        })
        .catch(error => console.error("Error al cargar:", error));
    }

    // 2. Función para GUARDAR un nuevo expediente
    document.getElementById('formExpediente').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = {
            id_paciente: document.getElementById('id_paciente').value,
            antecedentes: document.getElementById('antecedentes').value,
            alergias: document.getElementById('alergias').value,
            lesiones_previas: document.getElementById('lesiones_previas').value,
            notas_generales: document.getElementById('notas_generales').value
        };

        fetch('modulos_api/expediente.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(respuesta => {
            const alerta = document.getElementById('mensajeAlerta');
            alerta.classList.remove('hidden');
            alerta.textContent = respuesta.message;
            
            if(respuesta.status === 'success') {
                alerta.className = "mt-3 text-sm text-green-400"; // Letras verdes
                document.getElementById('formExpediente').reset(); // Limpiar formulario
                cargarExpedientes(); // Recargar la tabla automáticamente
            } else {
                alerta.className = "mt-3 text-sm text-red-400"; // Letras rojas
            }
        });
    });

    // Cargar los expedientes apenas cargue la página
    cargarExpedientes();


    function filtrarExpedientes() {
    // 1. Obtener el valor del buscador en minúsculas
    const input = document.getElementById("buscarExpediente");
    const filtro = input.value.toLowerCase();
    
    // 2. Apuntar al cuerpo de la tabla de expedientes
    const tabla = document.getElementById("tablaExpedientes"); // Asegúrate que tu tbody tenga este ID
    const filas = tabla.getElementsByTagName("tr");

    // 3. Recorrer y filtrar
    for (let i = 0; i < filas.length; i++) {
        const textoFila = filas[i].textContent.toLowerCase();
        
        // Si el nombre o apellido coinciden, se muestra; si no, se oculta
        if (textoFila.includes(filtro)) {
            filas[i].style.display = ""; 
        } else {
            filas[i].style.display = "none";
        }
    }
}
</script>