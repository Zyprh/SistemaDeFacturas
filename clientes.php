<?php
require_once 'DB/db.php';
require_once 'clases/Cliente.php';
require_once 'clases/Crud.php';

$crud = new Crud();

// Consultar todos los clientes
$clientes = $crud->listarClientes();

// Manejar la eliminación de un cliente
if (isset($_POST['eliminar'])) {
    $crud->eliminarCliente($_POST['id']);
    header("Location: clientes.php"); // Redirigir a la misma página
    exit;
}

// Manejar la edición de un cliente
if (isset($_POST['editar'])) {
    $cliente = new Cliente();
    $cliente->setId($_POST['clienteId']);
    $cliente->setNombre($_POST['nombre']);
    $cliente->setApellidos($_POST['apellidos']);
    $cliente->setEmail($_POST['email']);
    $cliente->setDireccion($_POST['direccion']);
    $cliente->setTelefono($_POST['telefono']);
    $cliente->setDni($_POST['dni']);
    $crud->editarCliente($cliente);
    header("Location: clientes.php"); // Redirigir a la misma página
    exit;
}

// Manejar el agregado de un nuevo cliente
if (isset($_POST['agregar'])) {
    $cliente = new Cliente();
    $cliente->setNombre($_POST['nombre']);
    $cliente->setApellidos($_POST['apellidos']);
    $cliente->setEmail($_POST['email']);
    $cliente->setDireccion($_POST['direccion']);
    $cliente->setTelefono($_POST['telefono']);
    $cliente->setDni($_POST['dni']);
    $crud->agregarCliente($cliente);
    header("Location: clientes.php"); // Redirigir a la misma página
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema de Facturación</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Incluir el navbar -->
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Listado de Clientes</h3>
    
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalCliente" onclick="prepararAgregar()">Agregar Cliente</button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>DNI</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="8" class="text-center">No hay clientes registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr id="cliente-<?php echo $cliente['id']; ?>">
                        <td><?php echo $cliente['id']; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $cliente['apellidos']; ?></td>
                        <td><?php echo $cliente['email']; ?></td>
                        <td><?php echo $cliente['direccion']; ?></td>
                        <td><?php echo $cliente['telefono']; ?></td>
                        <td><?php echo $cliente['dni']; ?></td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <form method="POST" style="margin-right: 5px;">
                                    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                                <button class="btn btn-warning btn-sm" onclick="prepararEdicion(<?php echo $cliente['id']; ?>, '<?php echo $cliente['nombre']; ?>', '<?php echo $cliente['apellidos']; ?>', '<?php echo $cliente['email']; ?>', '<?php echo $cliente['direccion']; ?>', '<?php echo $cliente['telefono']; ?>', '<?php echo $cliente['dni']; ?>')">Editar</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar/editar cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Agregar Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCliente" method="POST">
                    <input type="hidden" id="clienteId" name="clienteId">
                    <input type="hidden" id="accion" name="accion" value="agregar">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="form-group">
                        <label for="dni">DNI</label>
                        <input type="text" class="form-control" id="dni" name="dni" required>
                    </div>
                    <button type="submit" id="botonAccion" name="agregar" class="btn btn-primary">Agregar Cliente</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Función para preparar la edición
    function prepararEdicion(id, nombre, apellidos, email, direccion, telefono, dni) {
        $('#clienteId').val(id);
        $('#nombre').val(nombre);
        $('#apellidos').val(apellidos);
        $('#email').val(email);
        $('#direccion').val(direccion);
        $('#telefono').val(telefono);
        $('#dni').val(dni);
        
        // Cambiar los campos a solo lectura
        $('#email').prop('readonly', true);
        $('#dni').prop('readonly', true);
        
        $('#modalClienteLabel').text('Editar Cliente');
        $('#botonAccion').text('Guardar Cambios').attr('name', 'editar');
        $('#modalCliente').modal('show');
    }

    // Función para preparar agregar cliente
    function prepararAgregar() {
        $('#clienteId').val('');
        $('#nombre').val('');
        $('#apellidos').val('');
        $('#email').val('');
        $('#direccion').val('');
        $('#telefono').val('');
        $('#dni').val('');

        // Habilitar los campos
        $('#email').prop('readonly', false);
        $('#dni').prop('readonly', false);
        
        $('#modalClienteLabel').text('Agregar Cliente');
        $('#botonAccion').text('Agregar Cliente').attr('name', 'agregar');
    }
</script>
</body>
</html>
