<?php
require_once 'persona.php';

class Cliente extends Persona {

    public function __construct($nombre, $email) {
        if (empty($nombre) || empty($email)) {
            throw new InvalidArgumentException("Nombre y email no pueden estar vacíos.");
        }
        parent::__construct($nombre, $email);
    }
    
    // Implementación del método abstracto
    public function mostrarDatos() {
        return "Cliente: {$this->nombre} <br>" . 
               "Email: {$this->email} <br>";
    }
}