<?php
require_once 'DB/db.php';
require_once 'clases/Crud.php';
require_once 'clases/Factura.php';
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require('fpdf/fpdf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Asegúrate de incluir autoload.php de Composer

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$crud = new Crud();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['factura_id'], $_POST['email_cliente'])) {
    $facturaId = $_POST['factura_id'];
    $emailCliente = $_POST['email_cliente'];

    // Obtener los datos de la factura por ID
    $factura = $crud->getFacturaPorId($facturaId);
    if (!$factura) {
        echo json_encode(['success' => false, 'message' => 'Factura no encontrada.']);
        exit();
    }

    // Obtener detalles del cliente
    $cliente = $crud->getClientePorId($factura['cliente_id']);
    if (!$cliente) {
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
        exit();
    }

    // Verificar que el email del cliente esté definido
    if (empty($cliente['email'])) {
        echo json_encode(['success' => false, 'message' => 'El correo del cliente no está definido.']);
        exit();
    }

    // Asegúrate de que $emailCliente contenga un valor válido
    $emailCliente = filter_var($cliente['email'], FILTER_VALIDATE_EMAIL);
    if (!$emailCliente) {
        echo json_encode(['success' => false, 'message' => 'El correo del cliente es inválido.']);
        exit();
    }

    // Decodificar los productos de la factura desde JSON
    $productos = json_decode($factura['productos'], true);
    if (empty($productos)) {
        echo json_encode(['success' => false, 'message' => 'No se encontraron productos para esta factura.']);
        exit();
    }

    // Comenzar a crear el PDF
    ob_start();
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();

    // Verificar si el logo existe
    if (!file_exists('img/logo2.png')) {
        echo json_encode(['success' => false, 'message' => 'El logo no se encuentra en la ruta especificada.']);
        exit();
    }

    try {
        $pdf->Image('img/logo2.png', 250, 10, 30);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al cargar la imagen del logo: ' . $e->getMessage()]);
        exit();
    }

    // Título
    $pdf->SetFont('Helvetica', 'B', 24);
    $pdf->SetTextColor(33, 150, 243);
    $pdf->Cell(0, 15, iconv('UTF-8', 'ISO-8859-1', ($factura['tipo_documento'] == 'factura' ? 'Factura de Venta' : 'Boleta de Venta')), 0, 1, 'C');
    $pdf->Ln(10);

    // Información del cliente
    $pdf->SetFont('Helvetica', '', 14);
    $pdf->SetTextColor(0);
    $nombreCliente = $cliente['nombre'] ?? 'Nombre desconocido';
    $apellidosCliente = $cliente['apellidos'] ?? 'Sin apellidos';
    $dniCliente = $cliente['dni'] ?? 'DNI no registrado';

    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "Cliente: {$nombreCliente} {$apellidosCliente}"), 0, 1, 'L');
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "DNI: {$dniCliente}"), 0, 1, 'L');
    $pdf->Ln(5);

    // Tabla de productos
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->Cell(40, 10, 'Producto', 1);
    $pdf->Cell(30, 10, 'Cantidad', 1);
    $pdf->Cell(40, 10, 'Precio Unitario', 1);
    $pdf->Cell(40, 10, 'Total', 1);
    $pdf->Ln();

    $pdf->SetFont('Helvetica', '', 12);
    $totalFactura = 0;

    foreach ($productos as $producto) {
        $nombreProducto = $producto['nombre'] ?? 'Producto desconocido';
        $cantidad = isset($producto['cantidad']) && is_numeric($producto['cantidad']) ? (int)$producto['cantidad'] : 0;
        $precioUnitario = isset($producto['precio']) && is_numeric($producto['precio']) ? (float)$producto['precio'] : 0;

        $total = $cantidad * $precioUnitario;
        $totalFactura += $total;

        $pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', $nombreProducto), 1);
        $pdf->Cell(30, 10, $cantidad, 1);
        $pdf->Cell(40, 10, number_format($precioUnitario, 2), 1);
        $pdf->Cell(40, 10, number_format($total, 2), 1);
        $pdf->Ln();
    }

    // Total de la factura
    $pdf->Cell(110, 10, 'Total Factura', 1);
    $pdf->Cell(40, 10, number_format($totalFactura, 2), 1);
    $pdf->Ln(10);

    // Guardar el PDF en un archivo temporal
    $pdfFileName = "factura_{$facturaId}.pdf";
    try {
        $pdf->Output('F', $pdfFileName);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear el archivo PDF: ' . $e->getMessage()]);
        exit();
    }

    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Configuraciones del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'Rodoil.22kellyd@gmail.com';
        $mail->Password = 'lvnm zhpx vbvc lxvd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Destinatarios
        $mail->setFrom('Rodoil.22kellyd@gmail.com', 'SISTEMA DE FACTURACION');
        $mail->addAddress($emailCliente);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Factura de Compra';
        $mail->Body    = '
        <html>
        <head>
           <title>Factura de Compra</title>
        </head>
        <body>
        <h1>Gracias por tu compra!</h1>
        <p>Adjunto encontrarás tu factura en formato PDF. Si tienes alguna pregunta, no dudes en contactarnos.</p>
        </body>
        </html>';

        // Adjuntar el PDF
        $mail->addAttachment($pdfFileName);

        // Enviar el correo
        $mail->send();
        echo json_encode(['success' => true, 'message' => "Factura enviada por correo a {$emailCliente}."]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al enviar la factura: ' . $mail->ErrorInfo]);
    }

    // Limpiar el archivo temporal
    unlink($pdfFileName);  
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ID de factura no proporcionado.']);
}
?>
