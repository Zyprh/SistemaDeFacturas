<?php
class Producto {
    private $id; // Agregado para manejar el ID del producto
    private $nombre;
    private $descripcion;
    private $precio;
    private $stock;
    private $categoria;
    private $codigo;

    public function __construct($nombre = null, $descripcion = null, $precio = null, $stock = null, $categoria = null, $codigo = null, $id = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->categoria = $categoria;
        $this->codigo = $codigo;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getStock() {
        return $this->stock;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function setStock($stock) {
        $this->stock = $stock;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function mostrarDatosProducto() {
        return "Producto: {$this->nombre} <br>" .
               "Descripción: {$this->descripcion} <br>" .
               "Precio: $" . number_format($this->precio, 2) . "<br>" .
               "Stock: {$this->stock} unidades<br>" .
               "Categoría: {$this->categoria} <br>" .
               "Código: {$this->codigo} <br>";
    }
}
