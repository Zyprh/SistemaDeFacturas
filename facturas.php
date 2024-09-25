<?php
require_once 'DB/db.php';
require_once 'clases/Crud.php';
require_once 'clases/Factura.php';
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';

$crud = new Crud();

// Consultar facturas, clientes y productos
$facturas = $crud->listarFacturas();
$clientes = $crud->obtenerClientes();
$productosDisponibles = $crud->obtenerProductos();

// Lógica para agregar la factura
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        $cliente_id = $_POST['cliente_id'];
        $tipo_documento = $_POST['tipo_documento'];
        $productos = [];
        $cantidad_total = 0;
        $precio_total = 0;

        if (empty($_POST['productos'])) {
            die("Error: No se han seleccionado productos.");
        }

        foreach ($_POST['productos'] as $producto_id => $data) {
            if (isset($data['selected']) && !empty($data['cantidad']) && $data['cantidad'] > 0) {
                if (!empty($producto_id)) {
                    $producto_data = $crud->getProductoPorId($producto_id);
                    if ($producto_data) {
                        // Verifica si hay suficiente stock
                        if ($producto_data['stock'] >= $data['cantidad']) {
                            $producto = new Producto($producto_data['id'], $producto_data['nombre'], $producto_data['precio'], $producto_data['stock']);
                            $productos[] = ['producto' => $producto, 'cantidad' => $data['cantidad']];
                            $cantidad_total += $data['cantidad'];
                            $precio_total += $data['cantidad'] * $producto->getPrecio();

                            // Actualizar el stock
                            $nuevo_stock = $producto_data['stock'] - $data['cantidad'];
                            $crud->actualizarStockProducto($producto_id, $nuevo_stock);
                        } else {
                            echo "<script>alert('No hay suficiente stock para el producto: {$producto_data['nombre']}');</script>";
                        }
                    } else {
                        throw new Exception("Error: No se encontró el producto con ID $producto_id");
                    }
                } else {
                    throw new Exception("Error: Producto ID es nulo.");
                }
            }
        }

        if (empty($productos)) {
            die("Error: No se han agregado productos válidos.");
        }

        // Crear la factura y establecer los valores
        $factura = new Factura(new Cliente($cliente_id), $tipo_documento);
        $factura->setFecha(date('Y-m-d H:i:s')); // Usar la fecha actual
        $factura->setProductos($productos);
        $factura->setTotal($precio_total);

        // Convertir productos a formato JSON para almacenarlos
        $productos_json = json_encode($productos);

        // Intentar agregar la factura
        $factura_id = $crud->agregarFactura($factura, $productos_json, $cantidad_total, $precio_total);
        if ($factura_id) {
            echo "<script>alert('Factura agregada con éxito.');</script>";
            echo "<script>window.location.href = 'facturas.php';</script>";
        } else {
            echo "Error: No se pudo agregar la factura.";
        }
    }
}

// Para eliminar una factura
if (isset($_POST['eliminar'])) {
    $factura_id = $_POST['id'];
    $eliminado = $crud->eliminarFactura($factura_id);
    
    if ($eliminado) {
        echo "<script>alert('Factura eliminada con éxito.');</script>";
        echo "<script>window.location.href = 'facturas.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar la factura.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas - Sistema de Facturación</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .producto-label {
            margin-right: 40px; 
            color: #000; 
        }
    </style>
</head>
<body>

<!-- Incluir el navbar -->
<?php include 'index.php'; ?>

<div class="container mt-4">
    <h3 class="text-center">Listado de Facturas</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalFactura">Agregar Factura</button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Tipo de Documento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($facturas)): ?>
                <tr>
                    <td colspan="6" class="text-center">No hay facturas registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($facturas as $factura): ?>
                    <tr>
                        <td><?php echo $factura['id']; ?></td>
                        <td><?php echo htmlspecialchars($factura['cliente_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($factura['fecha']); ?></td>
                        <td><?php echo number_format($factura['total'], 2); ?></td>
                        <td><?php echo htmlspecialchars($factura['tipo_documento']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="prepararEdicion(<?php echo $factura['id']; ?>)">Editar</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $factura['id']; ?>">
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar factura -->
<div class="modal fade" id="modalFactura" tabindex="-1" role="dialog" aria-labelledby="modalFacturaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFacturaLabel">Agregar Factura</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formFactura" method="POST">
                    <div class="form-group">
                        <label for="cliente_id">Seleccionar Cliente</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_documento" value="factura" id="tipo_factura" required>
                            <label class="form-check-label" for="tipo_factura">Factura</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_documento" value="boleta" id="tipo_boleta">
                            <label class="form-check-label" for="tipo_boleta">Boleta</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Productos</label><br>
                        <?php foreach ($productosDisponibles as $producto): ?>
                            <div class="form-check d-flex align-items-center mb-2">
                                <input class="form-check-input" type="checkbox" name="productos[<?php echo $producto['id']; ?>][selected]" value="1" id="producto-<?php echo $producto['id']; ?>" onchange="toggleQuantityInput(<?php echo $producto['id']; ?>)">
                                <label class="form-check-label producto-label" for="producto-<?php echo $producto['id']; ?>">
                                    <?php echo htmlspecialchars($producto['nombre']); ?> - Precio: S/. <?php echo number_format($producto['precio'], 2); ?> (Stock: <?php echo $producto['stock']; ?>)
                                </label>
                                <input type="number" name="productos[<?php echo $producto['id']; ?>][cantidad]" id="cantidad-<?php echo $producto['id']; ?>" value="0" min="0" style="width: 80px; margin-left: 10px;" disabled>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="agregar" class="btn btn-primary btn-block">Agregar Factura</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function toggleQuantityInput(productId) {
    const checkbox = document.getElementById(`producto-${productId}`);
    const quantityInput = document.getElementById(`cantidad-${productId}`);

    if (checkbox.checked) {
        quantityInput.disabled = false; // Habilitar el campo de cantidad
        quantityInput.value = 1; // Iniciar con 1
    } else {
        quantityInput.disabled = true; // Deshabilitar el campo de cantidad
        quantityInput.value = 0; // Reiniciar a 0
    }
}
</script>

</body>
</html>
