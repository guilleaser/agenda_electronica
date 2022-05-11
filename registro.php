<?php
    require "base_datos/conexion.php";
    $mensaje ="";
    $boton = false;
    // Registrarse
    if(isset($_REQUEST['btnRegistrar'])){
        $usuario = $_POST['usuario'];

        // Comprobamos que no exista ese mismo registro
        $consulta = "SELECT * FROM usuarios WHERE (usuario LIKE '%$usuario%');";
        $datos = $conexion->prepare($consulta);
        $datos->execute();
        $registro = $datos->fetch(PDO:: FETCH_ASSOC);
        if($registro==0){ // Si no existe insertamos el registro nuevo
            try{
                $consulta= 'INSERT INTO `usuarios`(`usuario`, `password`) 
                            VALUES (:usuario, :pass);';
                $datos=$conexion->prepare($consulta); 
                $datos->bindParam(':usuario', $_REQUEST['usuario']);
                $datos->bindParam(':pass', password_hash($_REQUEST['pass'], PASSWORD_DEFAULT));
                if($datos->execute()){
                    $mensaje = "Te has registrado correctamente";
                    $boton = true;
                }
                $registro = $datos->fetch(PDO:: FETCH_ASSOC);
            }catch(PDOException $e){
                echo "Error: ".$e->getMessage();
            }
        }else{
            $mensaje = "El registro ya existe.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos/estilos.css">
    <title>Agenda Electronica</title>
</head>
<body>
<a href="index.php" style="flex: left;" id=" btnRegistro">Volver a inicio</a>
    <div id="login">    
        <div id="titulo">
            <h1>Registro</h1>
            <div>
                <h2>Registrarse en Agenda</h2>
            </div>
        </div>
        <form action='' method='POST' id="formularioRegistro">
                <label>Usuario</label>
                <input type='text' name='usuario' class="txtIntro" required/><br>
                <label>Password</label>
                <input type='Password' name='pass' class="txtIntro" required/><br>
                <input type='submit' name='btnRegistrar' value='Registrarse' id="btnIntro"/><br>
                <?php echo "<p style='text-align:center'>$mensaje</p>"; ?>
        </form>
    </div>
<!-- Codigo PHP -->
<?php 
// Alerta en cado de haber registrado correctamente
if($boton == true) {
    // echo '<script>alert("Usuario creado correctamente");</script>'; 
    // Reenvio a index.php despues de haber aceptado la alerta
    $mensaje = "Usuario creado correctamente. \nSi acepta será redirigido a la página de inicio.";
    echo "<script>";
    echo "alert('$mensaje');";  
    echo "window.location = 'http://localhost/GitHub/Agenda_electr%C3%B3nica/index.php';";
    echo "</script>"; 

}





?>
    
</body>
</html>