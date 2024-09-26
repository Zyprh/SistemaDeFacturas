<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .welcome-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Full height of the viewport */
        }
    </style>
</head>
<body>
    <!-- Incluir el navbar -->
    <?php include 'navbar.php'; ?>


    <!-- Contenido de bienvenida -->
    <div class="welcome-container">
        <i class="fas fa-file-invoice fa-5x mb-4"></i> <!-- Icono representando un sistema -->
        <h1>¡Bienvenido a mi Sistema de Facturación!</h1>
        <p class="lead">Aquí podrás gestionar tus clientes, productos y facturas de manera eficiente.</p>
        <p>Comienza navegando por las opciones en el menú.</p>
    </div>

    <!-- Bootstrap JS y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
