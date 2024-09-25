<?php
class Database {
    private $conn;

    public function __construct() {
        // Conexión a la base de datos
        $this->conn = new mysqli("localhost", "root", "", "sistema_facturacion");
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function executeQuery($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if ($params) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params); // Ajusta 's' si hay tipos diferentes
        }
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Si es un SELECT, devuelve los resultados
            if (stripos($query, 'SELECT') === 0) {
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC); // Devuelve todos los resultados como un array asociativo
            }
            return true; // Para INSERT, UPDATE, DELETE
        } else {
            // Manejo de errores
            echo "Error en la consulta: " . $stmt->error;
            return false;
        }
    }

    public function __destruct() {
        $this->conn->close(); // Cerrar la conexión
    }
}
?>
