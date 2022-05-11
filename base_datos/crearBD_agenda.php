<?php
    /* Conexion a la BBDD */
    $servername = "localhost";
    $dbname = "";
    $username = "root";
    $password = "";

    try {
        $conexion = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
        $conexion -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p id='loginConexion'>Login correcto $dbname</p>";
    }
    catch(PDOException $e){
        echo "<h1>Conexion a la BBDD <b> $dbname >/b> fallida</h1>".$e->getMessage();
    }

/************* CREAR LA BASE DE DATOS AGENDA ***********/
try{
    $mensaje="DROP DATABASE IF EXISTS agenda;";
    $mensaje.="CREATE DATABASE agenda;";
    $mensaje.="USE agenda;";
    
    /* Creacion tabla 'agenda' */
    $mensaje.="DROP TABLE IF EXISTS `agenda`;";
    $mensaje.="CREATE TABLE `agenda` (";
    $mensaje.="  `codigo` int(6) unsigned NOT NULL AUTO_INCREMENT,";
    $mensaje.=" `nombre` varchar(30)  NOT NULL,";
    $mensaje.=" `telefono` int(9) DEFAULT NULL,";
    $mensaje.=" `correo` varchar(50)  DEFAULT NULL,";
    $mensaje.=" `fechaNac` date,";
    $mensaje.=" PRIMARY KEY (`codigo`)";
    $mensaje.=") ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;";
    
    /* Creacion tabla 'usuarios' */
    $mensaje.="DROP TABLE IF EXISTS `usuarios`;";
    $mensaje.="CREATE TABLE `usuarios` (";
    $mensaje.="  `usuario` varchar(30) NOT NULL,";
    $mensaje.="  `password` varchar(100) NOT NULL,";
    $mensaje.="  PRIMARY KEY (`usuario`)";
    $mensaje.=") ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;";
    
    /* Crear usuario y privilegios */
    $mensaje.="DROP USER IF EXISTS 'agenda'@'localhost';";
    $mensaje.="CREATE USER 'agenda'@'localhost' IDENTIFIED BY '2DAWdwes';";
    $mensaje.="GRANT ALL PRIVILEGES ON * . * TO 'agenda'@'localhost';";
    $mensaje.="FLUSH PRIVILEGES;";

    $consulta = "$mensaje";
    $datos = $conexion->prepare($consulta);
    $datos->execute();
    $registro = $datos->fetchAll(PDO:: FETCH_ASSOC);
    if(!$registro==0){
        $mensaje =  "Error al crear la Base de datos";
    }
    else{
        echo "<br>Base de datos $dbname creada CORRECTO";
    }
}catch(PDOException $e){
    echo "<br>Error: ".$e->getMessage();
}
    

/************* INSERTAR DATOS EN AGENDA ***********/

/* Extraer datos del fichero */
$fichero = fopen("sorteo.csv", "rb");
$datos_completo = "";

if ($fichero==false){
    echo "Error al abrir el fichero";
}else{

    while (!feof($fichero)){
        $linea = fgetcsv($fichero, 10000, ';');
        if ($linea) {
            foreach($linea as $valor){
                $datos_completo.= $valor;
            }
        }
    }
    fclose($fichero);
}

/* Insertar datos en la BBDD agenda */
$datos_completo = substr($datos_completo, 3); //Quitamos los 3 primeros caracteres ya que daba problema al cargar el archivo
// Insertar datos en la tabla agenda
$consulta= "INSERT INTO `agenda`(`codigo`, `nombre`, `telefono`, `correo`, `fechaNac`) 
            VALUES ".$datos_completo.";";
$datos=$conexion->prepare($consulta); 
if($datos->execute()){
    echo "<br>Datos introducidos en la tabla agenda CORRECTO";
}
$registro = $datos->fetch(PDO:: FETCH_ASSOC);

// Insertar datos en la tabla usuarios
$consulta= 'INSERT INTO `usuarios`(`usuario`, `password`) VALUES ("amiguis", "'.password_hash("PHP4ever", PASSWORD_DEFAULT).'");';

$datos=$conexion->prepare($consulta); 

if($datos->execute()){
    echo "<br>Datos introducidos en la tabla usuarios CORRECTO";
}
$registro = $datos->fetch(PDO:: FETCH_ASSOC);

?><br><br><br><br>
<a href="index.php" id="btnHome">Regresar al home</a>




<?php 

// conexión

if (isset($_POST['enviar']))
{
	
  $filename=$_FILES["file"]["name"];
  $info = new SplFileInfo($filename);
  $extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);

   if($extension == 'csv')
   {
	$filename = $_FILES['file']['tmp_name'];
	$handle = fopen($filename, "r");

	while( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE )
	{
	   $q = "INSERT INTO importacion (nombre, apellido, correo) VALUES (
		'$data[0]', 
		'$data[1]',
		'$data[2]'
	)";

	$mysqli->query($q);
   }

      fclose($handle);
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Importación</title>
</head>
<body>
	
<form enctype="multipart/form-data" method="post" action="">
   CSV File:<input type="file" name="file" id="file">
   <input type="submit" value="Enviar" name="enviar">
</form>

</body>
</html>