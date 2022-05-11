<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos/estilos.css">
    <title>Agenda Electronica</title>
</head>
<body>
    <div id="login">    
        <div id="titulo">
            <h1>Log in</h1>
            <div>
                <h2>Agenda</h2>
                <p>Desarrollo Web Entorno Servidor</p>
                <p>Guillermo Aser Garcia Diez</p>
            </div>
        </div>
        <form action='login.php' method='POST' id="formulario">
                <label>Usuario</label>
                <input type='text' name='nombre' class="txtIntro" required/><br>
                <label>Password</label>
                <input type='password' name='pass' class="txtIntro" required/><br>
                <input type='submit' name='btnEnviar' value='Log in' id="btnIntro"/><br>
                <div id="check">
                    <input type="checkbox" name="checkSesion" id="chkSesion" checked>Mantener la sesión abierta
                </div>
        </form>
    </div>
    <a href="registro.php" id="btnRegistro">Registrarse</a>
    <a href="base_datos/crearBD_agenda.php" id="btnCarga">Carga de la BD Agenda</a>
</body>
</html>
<?php
// Comprobar si hay cookies activadas y entrar directamente en login.php
if(isset($_COOKIE['visitas']))
header('location:login.php'); 
?>