<?php
session_start(); // Iniciar sesión

require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Crud.php';
require_once 'clases/Factura.php'; // Incluir la clase Factura

$crud = new Crud();

$clienteNombre = $_POST['cliente'];
$productosSeleccionados = isset($_POST['productos']) ? $_POST['productos'] : [];

// Validar que se haya seleccionado al menos un producto
if (empty($productosSeleccionados)) {
    die("Debe seleccionar al menos un producto.");
}
//
// Obtener los datos del cliente
$cliente = $crud->getClientePorNombre($clienteNombre);

if (!$cliente) {
    die("Cliente no encontrado.");
}

// Crear una nueva factura
$factura = new Factura($cliente);

// Agregar productos a la factura
foreach ($productosSeleccionados as $productoNombre) {
    $producto = $crud->getProductoPorNombre($productoNombre);
    if ($producto) {
        $factura->agregarProducto($producto);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-download {
            background-color: #218838;
            color: white;
        }
        .btn-download:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Factura de Venta</h1>
    <h2>Cliente: <?php echo $cliente->getNombre(); ?></h2>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Producto</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($factura->getProductos() as $producto) { // Usar getProductos()
                echo "<tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'>{$producto->getNombre()}</td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>\${$producto->getPrecio()}</td>
                      </tr>";
                $total += $producto->getPrecio();
            }
            ?>
        </tbody>
    </table>
    <p class="total">Total: $<?php echo number_format($total, 2); ?></p>

    <div class="buttons">
        <form action="descargar.php" method="POST" style="display:inline;">
            <input type="hidden" name="cliente" value="<?php echo $cliente->getNombre(); ?>">
            <input type="hidden" name="productos" value="<?php echo implode(',', $productosSeleccionados); ?>">
            <input type="hidden" name="tipo_documento" value="factura"> <!-- Campo Oculto -->
            <button type="submit" class="btn btn-download">Descargar Factura</button>
        </form>
        <a href="Boleta.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Regresar</a>
    </div>
</div>

</body>
</html>
