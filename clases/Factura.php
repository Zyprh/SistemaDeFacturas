<?php
require_once 'cliente.php';
require_once 'producto.php';

class Factura {
    private $cliente;
    private $productos = [];

    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
    }
     
    public function getProductos() {
        return $this->productos;
    }    

    public function agregarProducto(Producto $producto) {
        $this->productos[] = $producto;
    }

    public function calcularTotal() {
        $total = 0;
        foreach ($this->productos as $producto) {
            $total += $producto->getPrecio();
        }
        return $total;
    }

    public function mostrarFactura() {
        echo $this->cliente->mostrarDatos(); // Mostrar los datos del cliente
        if (empty($this->productos)) {
            echo "No se han seleccionado productos.<br>";
            return;
        }
        
        echo "Productos: <br>";
        foreach ($this->productos as $producto) {
            echo "Nombre: " . $producto->getNombre() . " Precio: $" . number_format($producto->getPrecio(), 2) . "<br>";
        }
        echo "Total: $" . number_format($this->calcularTotal(), 2) . "<br>";
    }
}
