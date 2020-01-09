<?php
$servidor = "mysql:host=localhost;dbname=empresa";
$usuario = "root";
$password = "";

try {
    $conn = new PDO($servidor, $usuario, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexion exitosa";
    }
catch(PDOException $e)
    {
    echo "Conexion fallo : " . $e->getMessage();
    }
?>