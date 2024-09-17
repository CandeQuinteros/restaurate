<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Menús y Pedidos</title>
    <style>
/* Estilos generales */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Contenedores de formularios y tablas */
.form-container, .tabla-pedidos, .modificar-pedido, .tabla-menus, .menu-seccion {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
    text-align: center;
    margin-bottom: 20px;
}

/* Sección de botones */
.menu-seccion {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 20px;
}

.menu-seccion button {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.menu-seccion button:hover {
    background-color: #0056b3;
}

/* Estilo del formulario */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

input, textarea, select, button {
    width: calc(100% - 20px);
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input:focus, select:focus, textarea:focus {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
}

button {
    background-color: #007bff;
    color: #ffffff;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

/* Estilo de tablas */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #007bff;
    color: #ffffff;
}

/* Diseño responsivo */
@media (max-width: 600px) {
    .form-container, .tabla-pedidos, .modificar-pedido, .tabla-menus, .menu-seccion {
        width: 90%;
    }

    input, select, textarea, button {
        width: 100%;
    }
}
/* Estilos del modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
    </style>
</head>
<body>
    <!-- Botón para regresar a eleccion.html -->
<a href="eleccion.html" class="btn btn-secondary position-fixed top-0 end-0 m-3">
    Atrás
</a>

<!-- Sección de Botones -->
<section class="menu-seccion">
    <button onclick="mostrarSeccion('form-container')">Agregar Menú</button>
    <button onclick="mostrarSeccion('tabla-pedidos')">Pedidos en Cocina</button>
</section>

<div id="form-container" class="form-container" style="display: none;">
    <h2>Agregar Menú</h2>
    <form method="POST" action="">
        <input type="text" name="nombre" placeholder="Nombre del Menú" required>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required>
        <button type="submit" name="agregar_menu">Agregar Menú</button>
    </form>
</div>

<?php
ob_start(); // Inicia el buffering de salida

$host = 'localhost';
$dbname = 'restaurante';
$username = 'root';
$password = '';

try {
    // Conectar a la base de datos
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si se ha enviado el formulario para agregar un menú
    if (isset($_POST['agregar_menu'])) {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];

        // Insertar el nuevo menú en la base de datos
        $stmt = $conn->prepare("INSERT INTO menus (nombre, precio) VALUES (?, ?)");
        $stmt->execute([$nombre, $precio]);

        // Redirigir para evitar reenvíos múltiples del formulario al recargar la página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Detener la ejecución después de redirigir
    }

    // Verificar si se ha enviado la solicitud para eliminar un menú
    if (isset($_POST['eliminar_menu'])) {
        $menu_id = $_POST['menu_id'];
        // Aquí deberías tener la lógica para eliminar el menú
        // ...

        // Redirigir para evitar reenvíos múltiples del formulario al recargar la página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Detener la ejecución después de redirigir
    }

    // Verificar si se ha enviado la solicitud para actualizar un pedido
    if (isset($_POST['actualizar_pedido'])) {
        $pedido_id = $_POST['pedido_id'];
        $nombre = $_POST['nombre'];
        $cantidad = $_POST['cantidad'];

        // Actualizar el pedido en la base de datos
        $stmt = $conn->prepare("UPDATE pedidos p
                                INNER JOIN menus m ON p.id_menu = m.id
                                SET m.nombre = ?, p.cantidad = ?
                                WHERE p.id = ?");
        $stmt->execute([$nombre, $cantidad, $pedido_id]);

        // Redirigir para evitar reenvíos múltiples del formulario al recargar la página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Detener la ejecución después de redirigir
    }

    // Obtener todos los pedidos para mostrarlos con el precio total
    $stmt = $conn->prepare("SELECT p.id, m.nombre, p.cantidad, 
                            (m.precio * p.cantidad) AS precio_total 
                            FROM pedidos p 
                            INNER JOIN menus m ON p.id_menu = m.id 
                            WHERE p.estado = 'pendiente'");
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<p>Error al conectar a la base de datos: " . $e->getMessage() . "</p>";
}

// Manejar la solicitud de eliminación de todos los pedidos
if (isset($_POST['eliminar_todos_pedidos'])) {
    $conn = new PDO("mysql:host=localhost;dbname=restaurante", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Eliminar todos los pedidos
    $stmt = $conn->prepare("DELETE FROM pedidos WHERE estado = 'pendiente'");
    $stmt->execute();
    // Redirigir para evitar reenvíos múltiples del formulario al recargar la página
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Detener la ejecución después de redirigir
}

ob_end_flush(); // Envía todo el contenido del buffer al navegador
?>


<!-- Tabla de Pedidos Pendientes -->
<div id="tabla-pedidos" class="tabla-pedidos" style="">
    <h2>Pedidos en Cocina</h2>
    <?php if (!empty($pedidos)): ?>
        <form method="POST" action="" style="margin-bottom: 20px;">
            <button type="submit" name="eliminar_todos_pedidos" style="background-color: red; color: white;">Eliminar Todos los Pedidos</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Nombre del Menú</th>
                    <th>Cantidad</th>
                    <th>Precio Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido['id']) ?></td>
                        <td><?= htmlspecialchars($pedido['nombre']) ?></td>
                        <td><?= htmlspecialchars($pedido['cantidad']) ?></td>
                        <td>$<?= number_format($pedido['precio_total'], 2) ?></td>
                        <td>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                <button type="button" onclick="abrirModal(<?= $pedido['id'] ?>, '<?= htmlspecialchars($pedido['nombre']) ?>', <?= htmlspecialchars($pedido['cantidad']) ?>)">Modificar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay pedidos pendientes.</p>
    <?php endif; ?>
</div>

<!-- Modal para Modificar Pedido -->
<div id="modal-modificar" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Modificar Pedido</h2>
        <form id="form-modificar" method="POST" action="">
            <input type="hidden" name="pedido_id" id="pedido_id">
            <input type="text" name="nombre" id="nombre" placeholder="Nombre del Menú" required>
            <input type="number" name="cantidad" id="cantidad" placeholder="Cantidad" required>
            <button type="submit" name="actualizar_pedido">Actualizar Pedido</button>
        </form>
    </div>
</div>


<script>
function mostrarSeccion(seccionId) {
    document.querySelectorAll('.form-container, .tabla-pedidos, .tabla-menus').forEach(function(seccion) {
        seccion.style.display = 'none';
    });
    
    document.getElementById(seccionId).style.display = 'block';
}

function abrirModal(pedidoId, nombre, cantidad) {
    document.getElementById('pedido_id').value = pedidoId;
    document.getElementById('nombre').value = nombre;
    document.getElementById('cantidad').value = cantidad;
    document.getElementById('modal-modificar').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modal-modificar').style.display = 'none';
}

// Añadir evento para cerrar el modal al hacer clic fuera del modal
window.onclick = function(event) {
    if (event.target == document.getElementById('modal-modificar')) {
        cerrarModal();
    }
}
</script>

</body>
</html>

