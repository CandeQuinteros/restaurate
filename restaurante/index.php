<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'restaurante';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Procesar nuevo pedido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_menu = $_POST['id_menu'];
        $cantidad = $_POST['cantidad'];

        // Insertar el nuevo pedido en la base de datos
        $stmt = $conn->prepare("INSERT INTO pedidos (id_menu, cantidad) VALUES (?, ?)");
        $stmt->execute([$id_menu, $cantidad]);

        // Redirigir a la misma página para evitar el reenvío del formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Obtener todos los menús para el selector
    $menus = $conn->query("SELECT * FROM menus")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los pedidos pendientes
    $pedidos = $conn->query("SELECT pedidos.id, menus.nombre, pedidos.cantidad, menus.precio
                             FROM pedidos 
                             JOIN menus ON pedidos.id_menu = menus.id 
                             WHERE pedidos.estado = 'pendiente'")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Restaurante</title>
    <style>
        /* Estilos generales */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Encabezado */
h1, h2 {
    color: #333;
    margin-top: 30px;
    text-align: center;
}

/* Estilo del formulario */
form {
    margin: 20px;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 300px;
}

input, select, button {
    margin: 10px 0;
    padding: 12px;
    font-size: 16px;
    width: 90%;
    border: 1px solid #ddd;
    border-radius: 4px;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input:focus, select:focus {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
}

button {
    background-color: #007bff;
    color: #ffffff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

/* Estilo de la lista de pedidos */
#lista-pedidos {
    width: 80%;
    max-width: 600px;
    margin-top: 20px;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#lista-pedidos div {
    padding: 10px;
    margin: 5px 0;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Diseño responsivo */
@media (max-width: 600px) {
    form, #lista-pedidos {
        width: 90%;
    }

    input, select, button {
        width: 100%;
    }
}

    </style>
</head>
<body>
<a href="eleccion.html" class="btn btn-secondary position-fixed top-0 end-0 m-3">
    Atrás
</a>
    <h1>Pedidos del Restaurante</h1>

    <!-- Formulario para agregar pedidos -->
    <form id="form-pedido" method="POST" action="">
        <select name="id_menu" required>
            <?php foreach ($menus as $menu): ?>
                <option value="<?= $menu['id'] ?>"><?= htmlspecialchars($menu['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="cantidad" placeholder="Cantidad" min="1" required>
        <button type="submit">Agregar Pedido</button>
    </form>

    <!-- Listado de pedidos pendientes -->
    <h2>Pedidos en Cocina</h2>
    <div id="lista-pedidos">
        <?php foreach ($pedidos as $pedido): ?>
            <div>Pedido #<?= $pedido['id'] ?> - <?= htmlspecialchars($pedido['nombre']) ?> x <?= $pedido['cantidad'] ?> total: $<?= $pedido['cantidad'] * $pedido['precio'] ?></div>
        <?php endforeach; ?>
    </div>
</body>
</html>


