<?php
require_once 'DB/db.php';
require_once 'clases/Crud.php';
require_once 'clases/Factura.php';
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require('fpdf/fpdf.php');

$crud = new Crud();

if (isset($_GET['id'])) {
    $facturaId = $_GET['id'];

    // Obtener los datos de la factura por ID
    $factura = $crud->getFacturaPorId($facturaId);
    if (!$factura) {
        die('Error: Factura no encontrada.');
    }

    // Obtener detalles del cliente
    $cliente = $crud->getClientePorId($factura['cliente_id']);
    if (!$cliente) {
        die('Error: Cliente no encontrado.');
    }

    // Decodificar los productos de la factura desde JSON
    $productos = json_decode($factura['productos'], true);
    if (empty($productos)) {
        die('Error: No se encontraron productos para esta factura.');
    }

    // Comenzar a crear el PDF
    ob_start();
    $pdf = new FPDF('L', 'mm', 'A4');  // 'L' indica el formato horizontal (landscape)
    $pdf->AddPage();
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 20);

    // Logo de la empresa ficticia en la esquina derecha (asegúrate de tener el archivo logo.png en la carpeta indicada)
    $pdf->Image('img/logo2.png', 250, 10, 30);  // X=250 ajusta el logo a la derecha, Y=10 ajusta la altura, tamaño=30

    // Título
    $pdf->SetFont('Helvetica', 'B', 24);
    $pdf->SetTextColor(33, 150, 243);  // Azul corporativo
    $pdf->Cell(0, 15, iconv('UTF-8', 'ISO-8859-1', ($factura['tipo_documento'] == 'factura' ? 'Factura de Venta' : 'Boleta de Venta')), 0, 1, 'C');
    $pdf->Ln(10);

    // Información del cliente
    $nombreCliente = isset($cliente['nombre']) ? $cliente['nombre'] : 'Nombre desconocido';
    $apellidosCliente = isset($cliente['apellidos']) && !empty($cliente['apellidos']) ? $cliente['apellidos'] : 'Sin apellidos';
    $emailCliente = isset($cliente['email']) ? $cliente['email'] : 'Email no registrado';
    $dniCliente = isset($cliente['dni']) ? $cliente['dni'] : 'DNI no registrado';

    $pdf->SetFont('Helvetica', '', 14);
    $pdf->SetTextColor(0);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "Cliente: {$nombreCliente} {$apellidosCliente}"), 0, 1, 'L');
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "Email: {$emailCliente}"), 0, 1, 'L');
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "DNI: {$dniCliente}"), 0, 1, 'L');

    // Fecha de emisión
    $fechaEmision = date('d/m/Y', strtotime($factura['fecha']));
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1','Fecha de Emisión: ') . iconv('UTF-8', 'ISO-8859-1', $fechaEmision), 0, 1, 'L');
    $pdf->Ln(5);

    // Encabezado de la tabla de productos con estilo
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetFillColor(60, 179, 113);  // Color verde claro para el encabezado
    $pdf->SetTextColor(255);  // Texto blanco

    $pdf->Cell(110, 12, 'Producto', 1, 0, 'L', true);  // Hacemos la columna de producto más ancha
    $pdf->Cell(40, 12, 'Cantidad', 1, 0, 'C', true);
    $pdf->Cell(50, 12, 'Precio Unitario (S/.)', 1, 0, 'C', true);
    $pdf->Cell(50, 12, 'Total (S/.)', 1, 1, 'C', true);

    // Detalles de productos con alternancia de colores
    $pdf->SetFont('Helvetica', '', 12);
    $totalGeneral = 0;
    $fill = false;  // Variable para alternar color de fondo

    foreach ($productos as $producto) {
        if (!isset($producto['id'], $producto['cantidad'])) {
            die('Error: El producto no tiene ID o cantidad.');
        }

        // Obtener detalles del producto usando su ID
        $productoDetalles = $crud->getProductoPorId($producto['id']);
        if (!$productoDetalles) {
            die('Error: Producto no encontrado.');
        }

        $nombreProducto = isset($productoDetalles['nombre']) ? $productoDetalles['nombre'] : 'Producto desconocido';
        $precioProducto = isset($productoDetalles['precio']) ? $productoDetalles['precio'] : 0;
        $cantidad = (int)$producto['cantidad'];

        $totalProducto = $precioProducto * $cantidad;
        $totalGeneral += $totalProducto;

        // Alternancia de color en las filas
        $pdf->SetFillColor(240, 240, 240);  // Color gris claro para alternar filas
        $pdf->SetTextColor(0);  // Texto negro

        $pdf->Cell(110, 12, iconv('UTF-8', 'ISO-8859-1', $nombreProducto), 1, 0, 'L', $fill);  // Producto
        $pdf->Cell(40, 12, $cantidad, 1, 0, 'C', $fill);  // Cantidad
        $pdf->Cell(50, 12, 'S/. ' . number_format($precioProducto, 2), 1, 0, 'C', $fill);  // Precio unitario
        $pdf->Cell(50, 12, 'S/. ' . number_format($totalProducto, 2), 1, 1, 'C', $fill);  // Total

        $fill = !$fill;  // Alterna el color de fondo para la siguiente fila
    }

    // Total general con estilo
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetFillColor(60, 179, 113);  // Verde claro para el total
    $pdf->SetTextColor(255);  // Texto blanco
    $pdf->Cell(200, 12, 'Total General', 1, 0, 'R', true);
    $pdf->Cell(50, 12, 'S/. ' . number_format($totalGeneral, 2), 1, 1, 'C', true);

    // Salida del PDF
    $pdf->Output('D', "factura_{$facturaId}.pdf");
    ob_end_flush();
} else {
    die('Error: ID de factura no proporcionado.');
}
