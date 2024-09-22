<?php
// Autoloader para cargar las clases automáticamente
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Crud.php';

session_start();

// Limpia la sesión (opcional, solo para pruebas)
// session_unset();
// session_destroy();

$crud = new Crud();

// Inicializar los arrays de clientes y productos en la sesión si no existen
if (!isset($_SESSION['clientes'])) {
    $_SESSION['clientes'] = [];
}

if (!isset($_SESSION['productos'])) {
    $_SESSION['productos'] = [];
}

// Cargar clientes desde la sesión
foreach ($_SESSION['clientes'] as $clienteData) {
    if (is_string($clienteData)) {
        $cliente = unserialize($clienteData);
        if ($cliente instanceof Cliente) {
            $crud->agregarCliente($cliente);
        }
    }
}

// Cargar productos desde la sesión
foreach ($_SESSION['productos'] as $productoData) {
    if (is_string($productoData)) {
        $producto = unserialize($productoData);
        if ($producto instanceof Producto) {
            $crud->agregarProducto($producto);
        }
    }
}

// Agregar Clientes si no existen
if (empty($crud->listarClientes())) {
    $cliente1 = new Cliente("Juan Pérez", "juan@example.com");
    $cliente2 = new Cliente("María López", "maria@example.com");
    $crud->agregarCliente($cliente1);
    $crud->agregarCliente($cliente2);
}

// Agregar Productos si no existen
if (empty($crud->listarProductos())) {
    $producto1 = new Producto("Laptop", 1500);
    $producto2 = new Producto("Mouse", 25);
    $crud->agregarProducto($producto1);
    $crud->agregarProducto($producto2);
}

// Eliminar cliente
if (isset($_POST['eliminar_cliente'])) {
    $crud->eliminarCliente($_POST['cliente_a_eliminar']);
}

// Agregar nuevo cliente
if (isset($_POST['agregar_cliente'])) {
    $nombre = $_POST['nuevo_cliente_nombre'];
    $email = $_POST['nuevo_cliente_email'];
    $nuevoCliente = new Cliente($nombre, $email);
    $crud->agregarCliente($nuevoCliente);
}

// Agregar nuevo producto
if (isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nuevo_producto_nombre'];
    $precio = $_POST['nuevo_producto_precio'];
    $nuevoProducto = new Producto($nombre, $precio);
    $crud->agregarProducto($nuevoProducto);
}
// Eliminar producto
if (isset($_POST['eliminar_producto'])) {
    $crud->eliminarProducto($_POST['producto_a_eliminar']);
}


// Guardar los cambios en la sesión
$_SESSION['clientes'] = array_map('serialize', $crud->listarClientes());
$_SESSION['productos'] = array_map('serialize', $crud->listarProductos());
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Facturación Electrónica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            display: flex;
            justify-content: space-between;
        }

        .section {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            margin: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        h2 {
            color: #444;
        }

        .agregar-form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .agregar-form input {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-agregar, .btn-eliminar, .btn-generar {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-eliminar {
            background-color: #dc3545;
        }

        .btn-agregar:hover, .btn-eliminar:hover, .btn-generar:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .generar-boleta {
            margin-top: 20px;
        }

        .generar-boleta button {
            background-color: #007bff;
        }
    </style>
</head>
<body>

<h1>Sistema de Facturación Electrónica</h1>

<div class="container">
    <div class="section">
        <h2>Agregar Cliente:</h2>
        <form method="POST" class="agregar-form">
            <input type="text" name="nuevo_cliente_nombre" placeholder="Nombre" required>
            <input type="email" name="nuevo_cliente_email" placeholder="Email" required>
            <button type="submit" name="agregar_cliente" class="btn-agregar"><i class="fas fa-user-plus"></i> Agregar Cliente</button>
        </form>

        <h2>Clientes:</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($crud->listarClientes() as $cliente): ?>
                <tr>
                    <td><?php echo $cliente->getNombre(); ?></td>
                    <td><?php echo $cliente->getEmail(); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="cliente_a_eliminar" value="<?php echo $cliente->getNombre(); ?>">
                            <button type="submit" name="eliminar_cliente" class="btn-eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>Agregar Producto:</h2>
        <form method="POST" class="agregar-form">
            <input type="text" name="nuevo_producto_nombre" placeholder="Nombre del Producto" required>
            <input type="number" name="nuevo_producto_precio" placeholder="Precio" required min="0">
            <button type="submit" name="agregar_producto" class="btn-agregar"><i class="fas fa-box"></i> Agregar Producto</button>
        </form>

        <h2>Productos:</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($crud->listarProductos() as $producto): ?>
                <tr>
                    <td><?php echo $producto->getNombre(); ?></td>
                    <td>$<?php echo $producto->getPrecio(); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="producto_a_eliminar" value="<?php echo $producto->getNombre(); ?>">
                            <button type="submit" name="eliminar_producto" class="btn-eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="section generar-boleta" style="text-align: center;">
    <h2>Generar Vaucher</h2>
    <form action="Boleta.php" method="GET">
        <button type="submit" class="btn-generar">
            <i class="fas fa-file-invoice"></i> Imprimir vaucher 
        </button>
    </form>
</div>

</body>

</html>
