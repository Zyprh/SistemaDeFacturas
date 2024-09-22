<?php

require_once 'Cliente.php';
require_once 'Producto.php';

class Crud {
    private $clientes = [];
    private $productos = [];

    public function __construct() {
        // Asegúrate de que la sesión esté iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Cargar clientes y productos de la sesión si existen
        if (isset($_SESSION['clientes'])) {
            foreach ($_SESSION['clientes'] as $clienteData) {
                if (is_string($clienteData)) {
                    $this->clientes[] = unserialize($clienteData); // Deserializa correctamente
                }
            }
        }
        
        if (isset($_SESSION['productos'])) {
            foreach ($_SESSION['productos'] as $productoData) {
                if (is_string($productoData)) { // Asegúrate de que sea una cadena
                    $this->productos[] = unserialize($productoData);
                }
            }
        }
    }
    
    public function agregarCliente(Cliente $cliente) {
        if ($this->getClientePorNombre($cliente->getNombre()) === null) {
            $this->clientes[] = $cliente;
            $_SESSION['clientes'][] = serialize($cliente); // Asegúrate de serializar
        }
    }

    public function listarClientes() {
        return $this->clientes;
    }

    public function eliminarCliente($nombre) {
        foreach ($this->clientes as $key => $cliente) {
            if ($cliente->getNombre() === $nombre) {
                unset($this->clientes[$key]);
                $this->clientes = array_values($this->clientes); // Reindexar el array
                $_SESSION['clientes'] = array_map('serialize', $this->clientes); // Actualizar la sesión
                return true; // Retorna true cuando se elimina correctamente
            }
        }
        return false; // Si no se encuentra el cliente, retorna false
    }

    public function getClientePorNombre($nombre) {
        foreach ($this->clientes as $cliente) {
            if ($cliente->getNombre() === $nombre) {
                return $cliente;
            }
        }
        return null; // Si no se encuentra el cliente
    }

    public function agregarProducto(Producto $producto) {
        if ($this->getProductoPorNombre($producto->getNombre()) === null) {
            $this->productos[] = $producto;
            $_SESSION['productos'][] = serialize($producto); // Asegúrate de serializar
        }
    }

    public function listarProductos() {
        return $this->productos;
    }

    public function eliminarProducto($nombre) {
        foreach ($this->productos as $key => $producto) {
            if ($producto->getNombre() === $nombre) {
                unset($this->productos[$key]);
                $this->productos = array_values($this->productos); // Reindexar el array
                
                // Actualizar la sesión después de eliminar
                $_SESSION['productos'] = array_map('serialize', $this->productos); 
                
                // Mensaje de éxito
                echo "Producto eliminado: " . $nombre . "<br>";
                
                return true; // Retorna true si se elimina correctamente
            }
        }
        
        // Si no encuentra el producto
        echo "Producto no encontrado: " . $nombre . "<br>";
        
        return false; // Retorna false si no se encontró
    }

    public function getProductoPorNombre($nombre) {
        foreach ($this->productos as $producto) {
            if ($producto->getNombre() === $nombre) {
                return $producto;
            }
        }
        return null; // Si no se encuentra el producto
    }

    public function calcularTotalFactura($productos) {
        $total = 0;
        foreach ($productos as $producto) {
            $total += $producto->getPrecio();
        }
        return $total;
    }

    public function setClientes(array $clientes) {
        $this->clientes = $clientes;
        $_SESSION['clientes'] = array_map('serialize', $this->clientes); // Serializar para guardar en la sesión
    }
    
    public function setProductos(array $productos) {
        $this->productos = $productos;
        $_SESSION['productos'] = array_map('serialize', $this->productos); // Serializar para guardar en la sesión
    }
}

?>
