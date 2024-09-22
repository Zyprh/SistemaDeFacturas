<?php
session_start(); // Iniciar sesión
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Crud.php';

$crud = new Crud();

// Simulación de datos (en lugar de usar una base de datos)
if (!isset($_SESSION['clientes'])) {
    $_SESSION['clientes'] = [
        new Cliente('Cliente 1', 'email'),
        new Cliente('Cliente 2', 'email'),
    ];
}

if (!isset($_SESSION['productos'])) {
    $_SESSION['productos'] = [
        new Producto('Producto 1', 10.00), // Asegúrate de pasar ambos argumentos
        new Producto('Producto 2', 20.00), // Asegúrate de pasar ambos argumentos
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Comprobante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        select, .btn-generar, .radio-group {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .radio-group {
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }
        .btn-generar {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-generar:hover {
            background-color: #0056b3;
        }
        .producto {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .producto input {
            margin-right: 10px;
        }
        .producto label {
            flex: 1;
            color: #555;
        }
        .btn.regresar {
            background-color: gray;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px;
        }
    </style>
    <script>
        function redirigirComprobante(event) {
            event.preventDefault();
            const comprobanteSeleccionado = document.querySelector('input[name="comprobante"]:checked').value;
            const formulario = document.getElementById('form-comprobante');

            // Redirigir según el comprobante seleccionado
            if (comprobanteSeleccionado === 'boleta') {
                formulario.action = 'generar_boleta.php';
            } else if (comprobanteSeleccionado === 'factura') {
                formulario.action = 'generar_fac.php';
            }

            formulario.submit(); // Enviar el formulario
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Generar Comprobante</h1>
    <form id="form-comprobante" method="POST" onsubmit="redirigirComprobante(event)">
        <h2>Seleccionar Cliente:</h2>
        <select name="cliente" required>
            <option value="">Seleccione un cliente</option>
            <?php foreach ($crud->listarClientes() as $cliente): ?>
                <option value="<?php echo htmlspecialchars($cliente->getNombre()); ?>">
                    <?php echo htmlspecialchars($cliente->getNombre()); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <h2>Seleccionar Productos:</h2>
        <?php foreach ($crud->listarProductos() as $producto): ?>
            <div class="producto">
                <input type="checkbox" id="<?php echo htmlspecialchars($producto->getNombre()); ?>" name="productos[]" value="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                <label for="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                    <?php echo htmlspecialchars($producto->getNombre()); ?> - $<?php echo number_format($producto->getPrecio(), 2); ?>
                </label>
            </div>
        <?php endforeach; ?>

        <h2>Seleccionar Tipo de Comprobante:</h2>
        <div class="radio-group">
            <label>
                <input type="radio" name="comprobante" value="boleta" required> Boleta
            </label>
            <label>
                <input type="radio" name="comprobante" value="factura" required> Factura
            </label>
        </div>

        <button type="submit" class="btn-generar"><i class="fas fa-file-invoice"></i> Generar Comprobante</button>
        <a href="index.php" class="btn regresar">Regresar</a>
    </form>
</div>

</body>
</html>
