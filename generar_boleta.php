<?php
session_start(); // Iniciar sesión

require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Crud.php';
require_once 'clases/Factura.php'; // Incluir la clase Factura

$crud = new Crud();

$clienteNombre = $_POST['cliente'];
$productosSeleccionados = isset($_POST['productos']) ? $_POST['productos'] : [];

// Obtener los datos del cliente
$cliente = $crud->getClientePorNombre($clienteNombre);

if (!$cliente) {
    die("Cliente no encontrado.");
}

// Crear una nueva boleta (usando la clase Factura)
$boleta = new Factura($cliente);

// Agregar productos a la boleta
foreach ($productosSeleccionados as $productoNombre) {
    $producto = $crud->getProductoPorNombre($productoNombre);
    if ($producto) {
        $boleta->agregarProducto($producto);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .boleta {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px 5px; /* Ajustamos el margen para que los botones estén bien espaciados */
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #3498db;
        }
        .btn.regresar {
            background-color: gray;
        }
        .btn.regresar:hover {
            background-color: darkgray;
        }
        .btn-container {
            text-align: center; /* Centramos los botones */
            margin-top: 20px; /* Espacio extra por encima de los botones */
        }
    </style>
</head>
<body>

<div class="boleta">
    <h1>Boleta de Venta</h1>
    <h2>Cliente: <?php echo $cliente->getNombre(); ?></h2>
    
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($boleta->getProductos() as $producto) { // Usar getProductos()
                echo "<tr>
                        <td>{$producto->getNombre()}</td>
                        <td>\${$producto->getPrecio()}</td>
                      </tr>";
                $total += $producto->getPrecio();
            }
            ?>
        </tbody>
    </table>
    <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
    
        <div class="btn-container">
            <form action="descargar.php" method="POST" style="display:inline;">
            <input type="hidden" name="cliente" value="<?php echo $cliente->getNombre(); ?>">
            <input type="hidden" name="productos" value="<?php echo implode(',', $productosSeleccionados); ?>">
            <input type="hidden" name="tipo_documento" value="boleta"> <!-- Campo agregado -->
            <button type="submit" class="btn">Descargar Boleta</button>
            </form>
        <a href="Boleta.php" class="btn regresar">Regresar</a>
    </div>
</div>

</body>
</html>
