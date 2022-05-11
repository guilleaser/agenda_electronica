<?php
/* Conexion a la BBDD */
$servername = "localhost";
$dbname = "agenda";
$username = "agenda";
$password = "2DAWdwes";

try {
    $conexion = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    $conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p id='loginConexion' style='color:green;'>@ $dbname</p>";
}
catch(PDOException $e){
    echo "<h1>Conexion a la BBDD <b> $dbname </b> fallida</h1>".$e->getMessage();
}


?>
