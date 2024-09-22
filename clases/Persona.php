<?php

abstract class Persona {
    protected $nombre;
    protected $email; 

    public function __construct($nombre,$email) {
        $this->nombre = $nombre;
        $this->email = $email; 
    }

    // Getters para acceder a los atributos
    public function getNombre() {
        return $this->nombre;
    }

    public function getEmail() {
        return $this->email; // Método getter para email
    }

    // Método abstracto que las clases hijas deben implementar
    abstract public function mostrarDatos();
}
