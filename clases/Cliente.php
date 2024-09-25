<?php
class Cliente {
    private $id;
    private $nombre;
    private $apellidos;
    private $email;
    private $direccion;
    private $telefono;
    private $dni;

    // Constructor que permite crear un cliente con o sin parámetros
    public function __construct($id = null,$nombre = '', $apellidos = '', $email = '', $direccion = '', $telefono = '', $dni = '') {
       $this->id = $id;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->dni = $dni;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setDni($dni) {
        $this->dni = $dni;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getDni() {
        return $this->dni;
    }

    // Método para mostrar los datos del cliente
    public function mostrarDatos() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'dni' => $this->dni,
        ];
    }
}
?>
