<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/ProyectoIngWeb/includes/connection.php";

// email del admin actual para excluirlo
$adminEmail = $_SESSION['email'];
?>

<!-- Título -->
<h1 class="text-3xl font-bold mb-6">Gestión de Usuarios</h1>

<!-- Barra superior (Buscar + Filtros + Crear usuario) -->
<div class="flex items-center justify-between mb-4">

    <!-- Buscar -->
    <input 
        type="text" 
        id="searchInput"
        placeholder="Buscar usuario..." 
        class="px-3 py-2 border rounded-lg w-1/3 focus:outline-none focus:ring focus:ring-sky-900"
    >

    <!-- Filtro por tipo -->
    <select 
        id="filterTipo" 
        class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-sky-900"
    >
        <option value="">Todos los tipos</option>
        <option value="Administrador">Administrador</option>
        <option value="Cliente">Cliente</option>
        <option value="Empleado">Empleado</option>
    </select>

    <!-- Botón nuevo usuario -->
    <a href="/ProyectoIngWeb/admin/pages/createUser.php"
       class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
       <i data-lucide="user-plus"></i>
       Nuevo Usuario
    </a>
</div>

<!-- Tabla -->
<div class="overflow-x-auto">
    <table id="tablaUsuarios" class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-sky-900 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Nombre</th>
                <th class="py-3 px-4 text-left">Correo</th>
                <th class="py-3 px-4 text-left">Tipo</th>
                <th class="py-3 px-4 text-center">Editar</th>
                <th class="py-3 px-4 text-center">Eliminar</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = "SELECT userWEB_nombre, userWEB_emailID, userWEB_tipo FROM usuariosWEB";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()):

                // NO!!! mostrar el usuario administrador actual!!!
                if ($row['userWEB_emailID'] === $adminEmail) continue;

                // Convertir tipo a texto legible
                $tipoLegible = [
                    "A" => "Administrador",
                    "C" => "Cliente",
                    "E" => "Empleado"
                ][$row["userWEB_tipo"]];
            ?>
                <tr class="border-b hover:bg-gray-100 transition">
                    <td class="py-2 px-4 nombre"><?php echo $row['userWEB_nombre']; ?></td>
                    <td class="py-2 px-4 correo"><?php echo $row['userWEB_emailID']; ?></td>
                    <td class="py-2 px-4 tipo"><?php echo $tipoLegible; ?></td>

                    <!-- Editar -->
                    <td class="py-2 px-4 text-center">
                        <button class="px-3 py-1 rounded-lg bg-yellow-400 hover:bg-yellow-500 transition editBtn"
                            data-email="<?php echo $row['userWEB_emailID']; ?>"
                            data-nombre="<?php echo $row['userWEB_nombre']; ?>"
                            data-tipo="<?php echo $row['userWEB_tipo']; ?>"
                        >
                            <i data-lucide="pencil"></i>
                        </button>
                    </td>

                    <!-- Eliminar -->
                    <td class="py-2 px-4 text-center">
                        <button class="px-3 py-1 rounded-lg bg-red-500 text-white hover:bg-red-600 transition deleteBtn"
                        data-email="<?php echo $row['userWEB_emailID']; ?>">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal eliminar usuario -->
<div id="modalEliminar" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-xl w-80 text-center">

        <h2 class="text-xl font-semibold mb-4">Eliminar usuario</h2>
        <p class="text-gray-700 mb-6">
            ¿Seguro que deseas eliminar este usuario?
        </p>

        <div class="flex justify-around">
            <button onclick="confirmarEliminar()" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Sí, eliminar
            </button>

            <button onclick="cerrarModal()" 
                class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                Cancelar
            </button>
        </div>

    </div>
</div>

<!-- Modal Editar Usuario -->
<div id="modalEditar" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-xl w-96 text-center">

        <h2 class="text-xl font-semibold mb-4">Editar usuario</h2>

        <!-- Formulario -->
        <div class="mb-4 text-left">
            <label class="block text-sm mb-1">Nombre:</label>
            <input type="text" id="editNombre"
                   class="w-full border px-3 py-2 rounded-lg">
        </div>

        <div class="mb-4 text-left">
            <label class="block text-sm mb-1">Tipo:</label>
            <select id="editTipo"
                    class="w-full border px-3 py-2 rounded-lg">
                <option value="A">Administrador</option>
                <option value="C">Cliente</option>
                <option value="E">Empleado</option>
            </select>
        </div>

        <p id="editMensaje" class="text-red-600 mb-4 text-sm"></p>

        <div class="flex justify-around">
            <button onclick="guardarCambiosUsuario()" 
                class="bg-sky-900 text-white px-4 py-2 rounded-lg hover:bg-sky-950">
                Guardar
            </button>

            <button onclick="cerrarModalEditar()" 
                class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Activar iconos Lucide -->
<script src="https://unpkg.com/lucide-icons/dist/umd/lucide.js"></script>
<script> lucide.createIcons(); </script>

<!-- JavaScript para búsqueda y filtros -->
<script>
const searchInput = document.getElementById("searchInput");
const filterTipo = document.getElementById("filterTipo");
const tabla = document.getElementById("tablaUsuarios").getElementsByTagName("tbody")[0];

function actualizarTabla() {
    const busqueda = searchInput.value.toLowerCase();
    const filtro = filterTipo.value;

    for (let row of tabla.rows) {
        const nombre = row.querySelector(".nombre").textContent.toLowerCase();
        const correo = row.querySelector(".correo").textContent.toLowerCase();
        const tipo = row.querySelector(".tipo").textContent;

        const coincideBusqueda =
            nombre.includes(busqueda) || correo.includes(busqueda);

        const coincideFiltro =
            !filtro || tipo === filtro;

        row.style.display = (coincideBusqueda && coincideFiltro) ? "" : "none";
    }
}

searchInput.addEventListener("input", actualizarTabla);
filterTipo.addEventListener("change", actualizarTabla);
</script>

<script>
let emailAEliminar = null;

function cerrarModal() {
    document.getElementById("modalEliminar").classList.add("hidden");
}

function abrirModal(email) {
    emailAEliminar = email;
    document.getElementById("modalEliminar").classList.remove("hidden");
}

async function confirmarEliminar() {
    try {
        const response = await fetch("/ProyectoIngWeb/includes/deleteUser.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(emailAEliminar)}`
        });

        const data = await response.json();

        if (data.status === 0) {
            // Recargar tabla
            location.reload();
        }
        else if (data.status === 1) {
            alert("El usuario no existe.");
        }
        else if (data.status === 2) {
            alert("No puedes eliminar tu propio usuario.");
        }
        else {
            alert("Error inesperado en el servidor.");
        }

    } catch (error) {
        console.error(error);
        alert("Error de conexión con el servidor.");
    }

    cerrarModal();
}

// Asignar evento a todos los botones de eliminar
document.querySelectorAll(".deleteBtn").forEach(btn => {
    btn.addEventListener("click", () => {
        abrirModal(btn.dataset.email);
    });
});
</script>

<script>
let usuarioActual = {
    email: "",
    nombre: "",
    tipo: ""
};

function cerrarModalEditar() {
    document.getElementById("modalEditar").classList.add("hidden");
}

function abrirModalEditar(btn) {
    usuarioActual.email  = btn.dataset.email;
    usuarioActual.nombre = btn.dataset.nombre;
    usuarioActual.tipo   = btn.dataset.tipo;

    document.getElementById("editNombre").value = usuarioActual.nombre;
    document.getElementById("editTipo").value   = usuarioActual.tipo;

    document.getElementById("modalEditar").classList.remove("hidden");
}

document.querySelectorAll(".editBtn").forEach(btn => {
    btn.addEventListener("click", () => abrirModalEditar(btn));
});

async function guardarCambiosUsuario() {
    const nuevoNombre = document.getElementById("editNombre").value;
    const nuevoTipo   = document.getElementById("editTipo").value;

    const mensaje = document.getElementById("editMensaje");
    mensaje.innerHTML = "";

    if (nuevoNombre.trim() === "") {
        mensaje.innerHTML = "El nombre no puede estar vacío.";
        return;
    }

    try {
        const response = await fetch("/ProyectoIngWeb/includes/changeTypeProcess.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: 
                `email=${encodeURIComponent(usuarioActual.email)}` +
                `&nombre=${encodeURIComponent(nuevoNombre)}` +
                `&tipo=${encodeURIComponent(nuevoTipo)}`
        });

        const data = await response.json();

        if (data.status === 0) {
            location.reload(); // recargar tabla
        } else {
            mensaje.innerHTML = "Error: " + data.error;
        }

    } catch (error) {
        mensaje.innerHTML = "Error de conexión con el servidor.";
        console.error(error);
    }
}
</script>