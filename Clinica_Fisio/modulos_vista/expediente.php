<div class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8 text-white">
    <h2 class="text-xl font-bold mb-4 text-cyan-400">Nuevo Expediente</h2>
    <form id="formExpediente" class="flex flex-col gap-4">
        <input type="text" id="nombre" placeholder="Nombre completo" class="p-2 rounded bg-gray-700 text-white" required>
        <input type="text" id="telefono" placeholder="Teléfono" class="p-2 rounded bg-gray-700 text-white" required>
        <textarea id="motivo" placeholder="Motivo de consulta" class="p-2 rounded bg-gray-700 text-white"></textarea>
        <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">
            Guardar Paciente
        </button>
    </form>
    <div id="mensajeAlerta" class="mt-3 text-sm hidden"></div>
</div>

<div class="bg-gray-800 p-6 rounded-lg shadow-lg text-white">
    <h2 class="text-xl font-bold mb-4 text-cyan-400">Lista de Expedientes</h2>
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-gray-700 text-gray-400">
                <th class="py-2">Nombre</th>
                <th class="py-2">Teléfono</th>
                <th class="py-2">Motivo</th>
            </tr>
        </thead>
        <tbody id="tablaExpedientes">
            </tbody>
    </table>
</div>

<script>
    // 1. Función para CARGAR los expedientes al abrir la página
    function cargarExpedientes() {
        fetch('modulos_api/expedientes.php')
        .then(res => res.json())
        .then(respuesta => {
            if(respuesta.status === 'success') {
                const tbody = document.getElementById('tablaExpedientes');
                tbody.innerHTML = ''; // Limpiamos la tabla
                
                respuesta.data.forEach(paciente => {
                    tbody.innerHTML += `
                        <tr class="border-b border-gray-700">
                            <td class="py-3">${paciente.nombre_completo}</td>
                            <td class="py-3">${paciente.telefono}</td>
                            <td class="py-3">${paciente.motivo || 'N/A'}</td>
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
            nombre_completo: document.getElementById('nombre').value,
            telefono: document.getElementById('telefono').value,
            motivo: document.getElementById('motivo').value
        };

        fetch('modulos_api/expedientes.php', {
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
</script>