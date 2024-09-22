<?php
session_start();
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Crud.php';
require_once 'clases/Factura.php';
require('fpdf/fpdf.php');

$crud = new Crud();

// Recuperar datos del formulario
$clienteNombre = $_POST['cliente'];
$productosSeleccionados = explode(',', $_POST['productos']); 
$tipoDocumento = $_POST['tipo_documento']; 

$cliente = $crud->getClientePorNombre($clienteNombre);

// Crear una nueva factura
$factura = new Factura($cliente);

// Agregar productos a la factura
foreach ($productosSeleccionados as $productoNombre) {
    $producto = $crud->getProductoPorNombre(trim($productoNombre));
    if ($producto) {
        $factura->agregarProducto($producto);
    }
}

// Crear un nuevo PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(33, 150, 243);

// Título del documento
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ($tipoDocumento == 'factura' ? 'Factura de Venta' : 'Boleta de Venta')), 0, 1, 'C');
$pdf->Ln(10);

// Información del cliente
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Cliente: $clienteNombre"), 0, 1, 'L');
$pdf->Cell(0, 10, 'Fecha de Emision: ' . date('d/m/Y'), 0, 1, 'L');
$pdf->Ln(5);

// Encabezado de tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(100, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Producto'), 1, 0, 'L', true);
$pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Precio Unitario'), 1, 0, 'C', true);
$pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Total'), 1, 1, 'C', true);

// Productos
$pdf->SetFont('Arial', '', 12);
$total = 0;
foreach ($factura->getProductos() as $producto) {
    $precio = $producto->getPrecio();
    $pdf->Cell(100, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $producto->getNombre()), 1, 0);
    $pdf->Cell(40, 10, '$' . number_format($precio, 2), 1, 0, 'C');
    $pdf->Cell(40, 10, '$' . number_format($precio, 2), 1, 1, 'C');
    $total += $precio;
}

// Total
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Total a Pagar: '), 1, 0, 'R');
$pdf->Cell(40, 10, '$' . number_format($total, 2), 1, 1, 'C');

// Pie de página
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Gracias por su compra!'), 0, 1, 'C');

// Guardar el archivo PDF
$pdfFileName = ($tipoDocumento == 'factura' ? "factura_" : "boleta_") . time() . ".pdf";
$pdf->Output('D', $pdfFileName); // Descarga el archivo
exit();
?>
