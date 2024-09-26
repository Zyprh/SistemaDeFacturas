<?php

require_once 'DB/db.php';
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Factura.php'; // Asegúrate de incluir la clase Factura

class Crud {
    private $db;
    private $conn; // Definir la propiedad conn

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection(); 
    }
    public function getDatabase() {
        return $this->db; // Agregar un método para acceder a la instancia de Database
    }

    // Ejemplo de un método que verifica la conexión
    public function verificarConexion() {
        if ($this->conn) {
            echo "Conexión exitosa";
        } else {
            echo "Error en la conexión";
        }
    }

    // Métodos para Clientes
    public function listarClientes() {
        $query = "SELECT * FROM clientes"; 
        return $this->db->executeQuery($query);
    }

    public function agregarCliente($cliente) {
        $query = "INSERT INTO clientes (nombre, apellidos, email, direccion, telefono, dni) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $cliente->getNombre(),
            $cliente->getApellidos(),
            $cliente->getEmail(),
            $cliente->getDireccion(),
            $cliente->getTelefono(),
            $cliente->getDni()
        ];
        // Especificar tipos de parámetros
        $types = 'ssssss'; // todos son strings
        return $this->db->executeQuery($query, $params, $types);
    }
    
    public function editarCliente($cliente) {
        $query = "UPDATE clientes SET nombre = ?, apellidos = ?, direccion = ?, telefono = ? WHERE id = ?";
        $params = [
            $cliente->getNombre(),
            $cliente->getApellidos(),
            $cliente->getDireccion(),
            $cliente->getTelefono(),
            $cliente->getId()
        ];
        $types = 'ssssi'; // cuatro strings y un entero
        return $this->db->executeQuery($query, $params, $types);
    }
    
    public function eliminarCliente($id) {
        $query = "DELETE FROM clientes WHERE id = ?";
        return $this->db->executeQuery($query, [$id], 'i'); // solo un entero
    }

    public function getClientePorId($cliente_id) {
        if (!$cliente_id) {
            throw new Exception("Error: Cliente ID es nulo.");
        }
    
        $query = "SELECT id, nombre, apellidos, email, dni FROM clientes WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Devuelve el cliente o null si no existe
    }
      

    // Métodos para Productos
    public function listarProductos() {
        $query = "SELECT * FROM productos"; 
        return $this->db->executeQuery($query);
    }

    public function agregarProducto($producto) {
        $query = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria, codigo) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getCategoria(),
            $producto->getCodigo()
        ];
        $types = 'ssddss'; // dos strings, dos doubles y dos strings
        return $this->db->executeQuery($query, $params, $types);
    }

    public function editarProducto($producto) {
        $query = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ?, codigo = ? WHERE id = ?";
        $params = [
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getCategoria(),
            $producto->getCodigo(),
            $producto->getId()
        ];
        $types = 'ssddssi'; // dos strings, dos doubles, dos strings y un entero
        return $this->db->executeQuery($query, $params, $types);
    }

    public function eliminarProducto($id) {
        $query = "DELETE FROM productos WHERE id = ?";
        return $this->db->executeQuery($query, [$id], 'i'); // solo un entero
    }

    public function getProductoPorId($id) {
        $query = "SELECT * FROM productos WHERE id = ?";
        $productos = $this->db->executeQuery($query, [$id], 'i'); // solo un entero
        return !empty($productos) ? $productos[0] : null; 
    }

    // Métodos para Facturas
    public function listarFacturas() {
        $query = "SELECT facturas.id, clientes.nombre AS cliente_nombre, facturas.fecha, facturas.total, facturas.tipo_documento 
                  FROM facturas 
                  JOIN clientes ON facturas.cliente_id = clientes.id";
        return $this->db->executeQuery($query);
    }

    public function agregarFactura(Factura $factura, $productos_json, $cantidad_total, $total) {
        $cliente_id = $factura->getCliente()->getId();
        $tipo_documento = $factura->getTipoDocumento();
        $fecha = date('Y-m-d H:i:s'); // Agregar la fecha actual
    
        $stmt = $this->conn->prepare("INSERT INTO facturas (cliente_id, fecha, total, tipo_documento, productos, cantidad_total, precio_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssdi", $cliente_id, $fecha, $total, $tipo_documento, $productos_json, $cantidad_total, $total);
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return false;
        }
    }
    
        
    /*public function editarFactura($factura) {
        $query = "UPDATE facturas SET cliente_id = ?, fecha = ?, total = ?, tipo_documento = ? WHERE id = ?";
        $params = [
            $factura->getClienteId(),
            $factura->getFecha(),
            $factura->getTotal(),
            $factura->getTipoDocumento(),
            $factura->getId()
        ];
        $types = 'isssi'; // un entero y cuatro strings
        return $this->db->executeQuery($query, $params, $types);
    }*/

    public function eliminarFactura($id) {
        $query = "DELETE FROM facturas WHERE id = ?";
        return $this->db->executeQuery($query, [$id], 'i'); // solo un entero
    }

    public function getFacturaPorId($id) {
        $query = "SELECT * FROM facturas WHERE id = ?";
        $facturas = $this->db->executeQuery($query, [$id], 'i'); // solo un entero
        return !empty($facturas) ? $facturas[0] : null; 
    }

    // Método para obtener clientes
    public function obtenerClientes() {
        $query = "SELECT id, nombre FROM clientes"; // Asegúrate de que la tabla clientes tenga estos campos
        return $this->db->executeQuery($query);
    }

    // Método para obtener productos
    public function obtenerProductos() {
        $query = "SELECT id, nombre, precio, stock FROM productos"; // Asegúrate de seleccionar los campos correctos
        return $this->db->executeQuery($query); // Llama al método de ejecución de consultas
    }
    public function actualizarStockProducto($producto_id, $nuevo_stock) {
        $stmt = $this->conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $nuevo_stock, $producto_id);
        return $stmt->execute();
    }

    public function getProductosPorFactura($facturaId) {
        // Obtener la factura
        $query = "SELECT productos FROM facturas WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $facturaId);
        $stmt->execute();
    
        // Obtener el resultado
        $result = $stmt->get_result();
        $factura = $result->fetch_assoc();
        
        // Decodificar JSON si existe
        if (!empty($factura['productos'])) {
            $productosArray = json_decode($factura['productos'], true);
    
            // Aquí recorremos cada producto y agregamos el nombre y precio
            foreach ($productosArray as &$producto) {
                // Accedemos al ID del producto
                $productoId = $producto['id']; // Ahora usamos 'id' en lugar de 'producto_id'
                $productoDetails = $this->getProductoPorId($productoId); // Obtener detalles del producto
    
                // Agregar detalles del producto a la estructura
                if ($productoDetails) {
                    $producto['nombre'] = $productoDetails['nombre'];
                    $producto['precio'] = $productoDetails['precio'];
                } else {
                    $producto['nombre'] = 'Producto desconocido';
                    $producto['precio'] = 0;
                }
            }
            
            return $productosArray; // Devuelve el array modificado
        }
        
        return []; // Devuelve un array vacío si no hay productos
    }
    

}

?>
