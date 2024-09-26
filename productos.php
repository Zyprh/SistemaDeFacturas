<?php
require_once 'DB/db.php';
require_once 'clases/Producto.php';  // Asegúrate de que esta clase exista
require_once 'clases/Crud.php';       // Asegúrate de que esta clase incluya el método para listar productos

$crud = new Crud();

// Consultar todos los productos
$productos = $crud->listarProductos(); // Asegúrate de implementar este método en la clase Crud

// Manejar la eliminación de un cliente
if (isset($_POST['eliminar'])) {
    $crud->eliminarProducto($_POST['id']);
    header("Location: productos.php"); // Redirigir a la misma página
    exit;
}

// Manejar la edición de un producto
if (isset($_POST['editar'])) {
    $producto = new Producto();
    $producto->setId($_POST['productoId']);
    $producto->setNombre($_POST['nombre']);
    $producto->setDescripcion($_POST['descripcion']);
    $producto->setPrecio($_POST['precio']);
    $producto->setStock($_POST['stock']);
    $producto->setCategoria($_POST['categoria']);
    $producto->setCodigo($_POST['codigo']);
    $crud->editarProducto($producto);
    header("Location: productos.php"); // Redirigir a la misma página
    exit;
}

// Manejar el agregado de un nuevo producto
if (isset($_POST['agregar'])) {
    $producto = new Producto();
    $producto->setNombre($_POST['nombre']);
    $producto->setDescripcion($_POST['descripcion']);
    $producto->setPrecio($_POST['precio']);
    $producto->setStock($_POST['stock']);
    $producto->setCategoria($_POST['categoria']);
    $producto->setCodigo($_POST['codigo']);
    $crud->agregarProducto($producto);
    header("Location: productos.php"); // Redirigir a la misma página
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema de Facturación</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Incluir el navbar -->
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Listado de Productos</h3>
    
    <!-- Botón para agregar producto -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalProducto" onclick="prepararAgregar()">Agregar Producto</button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th>Código</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="8" class="text-center">No hay productos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <tr id="producto-<?php echo $producto['id']; ?>">
                        <td><?php echo htmlspecialchars($producto['id']); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                        <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                        <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                            <button class="btn btn-warning btn-sm" onclick="prepararEdicion(<?php echo $producto['id']; ?>, '<?php echo htmlspecialchars($producto['nombre']); ?>', '<?php echo htmlspecialchars($producto['descripcion']); ?>', '<?php echo htmlspecialchars($producto['precio']); ?>', '<?php echo htmlspecialchars($producto['stock']); ?>', '<?php echo htmlspecialchars($producto['categoria']); ?>', '<?php echo htmlspecialchars($producto['codigo']); ?>')">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar/editar producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoLabel">Agregar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formProducto" method="POST">
                    <input type="hidden" id="productoId" name="productoId"> <!-- Campo oculto para el ID -->
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" class="form-control" id="precio" name="precio" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <input type="text" class="form-control" id="categoria" name="categoria" required>
                    </div>
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" required>
                    </div>
                    <button type="submit" id="botonAccion" name="agregar" class="btn btn-primary">Agregar Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Función para preparar la edición
    function prepararEdicion(id, nombre, descripcion, precio, stock, categoria, codigo) {
        $('#productoId').val(id);
        $('#nombre').val(nombre);
        $('#descripcion').val(descripcion);
        $('#precio').val(precio);
        $('#stock').val(stock);
        $('#categoria').val(categoria);
        $('#codigo').val(codigo);
        
        $('#modalProductoLabel').text('Editar Producto');
        $('#botonAccion').text('Guardar Cambios').attr('name', 'editar');
        $('#modalProducto').modal('show');
    }

    // Función para preparar agregar producto
    function prepararAgregar() {
        $('#productoId').val('');
        $('#nombre').val('');
        $('#descripcion').val('');
        $('#precio').val('');
        $('#stock').val('');
        $('#categoria').val('');
        $('#codigo').val('');

        $('#modalProductoLabel').text('Agregar Producto');
        $('#botonAccion').text('Agregar Producto').attr('name', 'agregar');
    }
</script>
</body>
</html>
