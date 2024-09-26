<?php
require_once 'Cliente.php';
require_once 'Producto.php';

class Factura {
    private $id; // ID de la factura
    private $cliente; // Objeto Cliente
    private $productos = []; // Arreglo de productos
    private $fecha; // Fecha de la factura
    private $tipo_documento; // Tipo de documento
    private $total; // Total de la factura

    public function __construct(Cliente $cliente, $tipo_documento) {
        $this->cliente = $cliente; // Guarda el objeto Cliente
        $this->tipo_documento = $tipo_documento;
        $this->fecha = date('Y-m-d'); // Fecha actual
        $this->total = 0; // Inicializa el total
    }

    public function getClienteId() {
        return $this->cliente->getId(); // Método para obtener el ID del cliente
    }

    // Métodos set y get
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id; // Establecer el ID
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha; // Establecer la fecha
    }
    public function setProductos($productos) {
        $this->productos = $productos; // Establecer la fecha
    }

    public function setTipoDocumento($tipo_documento) {
        $this->tipo_documento = $tipo_documento; // Establecer el tipo de documento
    }

    public function setTotal($total) {
        $this->total = $total; // Establecer el total
    }

    public function getCliente() {
        return $this->cliente; // Obtener el objeto Cliente
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getTipoDocumento() {
        return $this->tipo_documento;
    }

    public function getProductos() {
        return $this->productos;
    }

    public function getTotal() {
        return $this->total; // Retornar el total almacenado
    }

        // Agregar producto a la factura
        public function agregarProducto(Producto $producto, $cantidad) {
            $this->productos[] = [
                'id' => $producto->getId(), 
                'nombre' => $producto->getNombre(), // Agregar el nombre del producto
                'cantidad' => $cantidad,
                'precio' => $producto->getPrecio(), // Agregar precio aquí
            ]; 
        }

    

    // Calcular total de la factura
    public function calcularTotal() {
        $total = 0;
        foreach ($this->productos as $item) {
            $total += $item['precio'] * $item['cantidad']; // Calcular correctamente el total
        }
        $this->total = $total; // Actualiza el total en el objeto
        return $this->total;
    }

    // Mostrar la factura completa
    public function mostrarFactura() {
        echo "Cliente ID: " . $this->cliente->getId() . "<br>"; // Usar el ID del cliente
        echo "Fecha: " . $this->fecha . "<br>";
        echo "Tipo de Documento: " . $this->tipo_documento . "<br>";
        echo "Productos: <br>";

        foreach ($this->productos as $item) {
            echo "ID: " . $item['id'] . " | Precio: $" . number_format($item['precio'], 2) . " | Cantidad: " . $item['cantidad'] . "<br>";
        }

        echo "Total: $" . number_format($this->total, 2) . "<br>"; // Usar $this->total
    }
}
?>
