-- Crear base de datos
CREATE DATABASE sistema_facturacion;
USE sistema_facturacion;

-- Crear tabla clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    dni VARCHAR(8),
    
    UNIQUE(email),
    UNIQUE(dni)
);

-- Crear tabla productos 
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria VARCHAR(50),
    codigo VARCHAR(20) UNIQUE
);

-- Crear tabla de facturas
CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha DATE NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    tipo_documento ENUM('factura', 'boleta') NOT NULL,
    productos TEXT,            -- Para almacenar información sobre los productos en formato JSON
    cantidad_total INT DEFAULT 0,  -- Total de productos en la factura
    precio_total DECIMAL(10, 2) DEFAULT 0.00, -- Precio total de la factura
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) 
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- Insertar clientes
INSERT INTO clientes (nombre, apellidos, email, direccion, telefono, dni) VALUES
('Juan', 'Pérez', 'juan.perez@example.com', 'Av. Principal 123', '987654321', '12345678'),
('Ana', 'Gómez', 'ana.gomez@example.com', 'Calle Secundaria 456', '976543210', '23456789'),
('Carlos', 'Lopez', 'carlos.lopez@example.com', 'Pasaje de la Amistad 789', '965432109', '34567890');

-- Insertar productos
INSERT INTO productos (nombre, descripcion, precio, stock, categoria, codigo) VALUES
('Laptop', 'Laptop de 15 pulgadas, 16GB RAM', 1500.00, 10, 'Electrónica', 'LAP-001'),
('Mouse', 'Mouse inalámbrico', 25.50, 50, 'Accesorios', 'MOU-002'),
('Teclado', 'Teclado mecánico', 70.00, 30, 'Accesorios', 'TEK-003'),
('Monitor', 'Monitor LED 24 pulgadas', 300.00, 20, 'Electrónica', 'MON-004'),
('Impresora', 'Impresora multifuncional', 120.00, 15, 'Electrónica', 'IMP-005');

-- Insertar facturas
INSERT INTO facturas (cliente_id, fecha, total, tipo_documento) VALUES
(1, '2024-09-20', 1625.50, 'factura'),  -- Factura para Juan
(2, '2024-09-21', 400.00, 'boleta'),    -- Factura para Ana
(1, '2024-09-22', 300.00, 'factura');   -- Segunda factura para Juan

select * from clientes;
select * from productos;
select * from facturas;
SELECT nombre, apellidos, email, dni FROM clientes WHERE id = 1;


