<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos/estilos.css">
    <title>Agenda Electronica</title>
</head>
<body>
<?php
require 'base_datos/conexion.php';
$mensaje = '';

function borrarCookies(){
    setcookie("visitas", 1, time()-1);  // Borrar las cookies
    setcookie("hora", date("d-m-y h:i:s"),time()-1);
    session_start();
    session_destroy(); // Borrar sesion
    header('location:index.php'); 
}

session_start();
if(empty($_SESSION['usuario'])) {
    header('location:index.php'); 
}

/****** COOKIES Y VALIDACION DE USUARIO ******/
// Salir y borrar cookies a index.php
if (isset($_POST['btnSalir'])){    //Comprobar acceso a traves del formulario
    borrarCookies();
}

/* Guardar cookies en caso de que se marque la casilla */
if (isset($_POST['checkSesion'])){
    if(isset($_COOKIE['visitas'])){
        $_COOKIE['visitas']++;
        setcookie("visitas", $_COOKIE['visitas'], time()+600);
        setcookie("hora", date("d-m-y h:i:s"),time()+600);  
    }else{
        setcookie("visitas", time()+600);
        setcookie("hora", date("d-m-y h:i:s"),time()+600); 
    }
}

/* Consultar usuario y contraseña para la validacion */
if(isset($_POST['btnEnviar'])){
    $consulta = "SELECT * FROM usuarios WHERE usuario = :nombre;";
    $datos = $conexion->prepare($consulta);
    $consultaCodigo = $_POST['nombre'];
    $datos->bindParam(':nombre', $consultaCodigo);
    $datos->execute();
    $registro = $datos->fetch(PDO:: FETCH_ASSOC);
    // Validar acceso con usuario y password
    $pass=$_POST['pass'];
    if($registro['usuario']==$_POST['nombre'] && password_verify($pass, $registro['password'])){ // Comprobacion pass cifrada
        if(session_status()==1) session_start();
        $_SESSION['usuario'] = $registro['usuario'];
        echo "pass correcto";
    }else{
        borrarCookies();
    }
}

// Comprobamos si la sesion esta abierta o no
if(session_status()==1) session_start();
echo "<p>Has iniciado sesion como <b>".$_SESSION['usuario']."</b></p>";

/****** CRUD ********/
/* BUSCAR */
if(isset($_REQUEST['btnBuscar']) ){
    try{
        if($_POST['codigoAgenda']){ // Buscar por codigo
            $consultaNombre = $_POST['codigoAgenda'];
            $consulta = "SELECT * FROM agenda WHERE codigo = $consultaNombre ORDER BY codigo ASC;";
            $datos = $conexion->prepare($consulta);
            $datos->execute();
        }
        if($_POST['nombreAgenda']){ // Buscar por nombre
            $consultaNombre = $_POST['nombreAgenda'];
            $consulta = "SELECT * FROM agenda WHERE nombre LIKE '%$consultaNombre%' ORDER BY codigo ASC;";
            $datos = $conexion->prepare($consulta);
            $datos->execute();
        }

        if($_POST['telefonoAgenda']){ // Buscar por telefono
            $consultaNombre = $_POST['telefonoAgenda'];
            $consulta = "SELECT * FROM agenda WHERE telefono LIKE '%$consultaNombre%' ORDER BY codigo ASC;";
            $datos = $conexion->prepare($consulta);
            $datos->execute();
        }
        if($_POST['correoAgenda']){ // Buscar por mail
            $consultaNombre = $_POST['correoAgenda'];
            $consulta = "SELECT * FROM agenda WHERE correo LIKE '%$consultaNombre%' ORDER BY codigo ASC;";
            $datos = $conexion->prepare($consulta);
            $datos->execute();
        }
        /*Tabla de busqueda */
        echo "<table id='tblBuscar' style='background-color:grey'>";
        $contadorRegistros = 0;
        if(!empty($datos)){ // Si no se ha creado $datos en la busqueda no se ejecuta
        while ($registro = $datos->fetch(PDO:: FETCH_ASSOC)){
            $contadorRegistros++;
            echo "<tr >";
            echo "<form action='' method='POST' id='formularioBuscar'>";
            echo "<td><input type='number' name='codigoAgenda' value='".$registro['codigo']."' readonly></td>";
            echo "<td><input type='text' name='nombreAgenda' value='".$registro['nombre']."'></td>";
            echo "<td><input type='number' name='telefonoAgenda' value='".$registro['telefono']."'></td>";
            echo "<td><input type='mail' name='correoAgenda' value='".$registro['correo']."'></td>";
            echo "<td><input type='date' name='fechaAgenda' value='".$registro['fechaNac']."'></td>";
            echo "<td><input type='submit' name='btnBuscarTabla' value='Editar' style='background-color:#F0A63C'</td>";
            echo "<td><input type='submit' name='btnEliminar' value='Eliminar' style='background-color:#F05146;'</td>";
            echo "</form>";
            echo "</tr>";
        }
        if($contadorRegistros == 0) echo "<p style='color:red;'>No hay registros</p>";
        else{
            echo "<tr>";
            echo "<th>codigo</th>";
            echo "<th>nombre</th>";
            echo "<th>telefono</th>";
            echo "<th>correo</th>";
            echo "<th>fechaNac</th>";
            echo "<th>Editar</th>";
            echo "<th>Eliminar</th>";
            echo "</tr>"; 
        }
    }
    }catch(PDOException $e){
        echo "Error: ".$e->getMessage();
    }
}
echo "</table>";

/* INSERTAR */
if(isset($_REQUEST['btnInsertar'])){
    // Comprobamos que no exista ese mismo registro
    $nombre = $_POST['nombreAgenda'];
    $telefono = $_POST['telefonoAgenda'];
    $correo = $_POST['correoAgenda'];
    $fecha = $_POST['fechaAgenda'];
    $consulta = "SELECT * FROM agenda WHERE (nombre LIKE '%$nombre%') AND (telefono LIKE '%$telefono%') AND (correo LIKE '%$correo%') AND (fechaNac LIKE '$fecha');";
    $datos = $conexion->prepare($consulta);
    $datos->execute();
    $registro = $datos->fetch(PDO:: FETCH_ASSOC);
    if($registro==0){ // Si no existe insertamos el registro nuevo
        try{
            $consulta= 'INSERT INTO `agenda`(`nombre`, `telefono`,`correo`, `fechaNac`) 
                        VALUES (:nombre, :telefono, :correo, :fechaNac);';
            $datos=$conexion->prepare($consulta); 
            $datos->bindParam(':nombre', $_REQUEST['nombreAgenda']);
            $datos->bindParam(':telefono', $_REQUEST['telefonoAgenda']);
            $datos->bindParam(':correo', $_REQUEST['correoAgenda']);
            $datos->bindParam(':fechaNac', $_REQUEST['fechaAgenda']);
            if($datos->execute()){
                $mensaje = "El registro se ha insertado correctamente.";
            }
            $registro = $datos->fetch(PDO:: FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error: ".$e->getMessage();
        }
    }else{
        $mensaje = "El registro ya existe.";
    }
}
/* ELIMINAR */
if(isset($_REQUEST['btnEliminar'])){
    try{
        if(!empty($_REQUEST['chkEliminarTabla'])){
            $listaEliminar = $_POST['chkEliminarTabla']; // REcogemos valores del checkbox array
            $eliminar = "";
            foreach($listaEliminar as $clave => $valor){ // Sacamos los valores para la select
                $eliminar.= $valor.",";
            }
            $eliminar = substr($eliminar, 0, -1); // Quitamos la ultima coma
        }else{
            $eliminar = $_POST['codigoAgenda'];
        }

        $consulta = "DELETE FROM agenda WHERE codigo IN ($eliminar);";
        $datos = $conexion->prepare($consulta);
        if($datos->execute()){
            $mensaje = "El registro se ha eliminado correctamente.";
        }
        $registro = $datos->fetch(PDO:: FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: ".$e->getMessage();
    }
}

/* MODIFICAR */
if(isset($_REQUEST['btnModificar']) || isset($_REQUEST['btnBuscarTabla'])){
    try{
        $consulta = "UPDATE agenda SET codigo=:codigo, nombre=:nombre, telefono=:telefono, correo=:correo, fechaNac=:fechaNac
                    WHERE codigo=:codigo;";
        $datos = $conexion->prepare($consulta);
        $datos->bindParam(':codigo', $_REQUEST['codigoAgenda']);
        $datos->bindParam(':nombre', $_REQUEST['nombreAgenda']);
        $datos->bindParam(':telefono', $_REQUEST['telefonoAgenda']);
        $datos->bindParam(':correo', $_REQUEST['correoAgenda']);
        $datos->bindParam(':fechaNac', $_REQUEST['fechaAgenda']);
        if($datos->execute()){
            $mensaje = "El registro se ha actualizado correctamente.";
        }
        $registro = $datos->fetch(PDO:: FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: ".$e->getMessage();
    }
}

/************** FORMULARIO **************/
$html = '';
$html = "<br>";
$html.= "<form action='' method='POST' id='formTabla'>";
$html.= "<fieldset  id='fomrPrin'>";
$html.= "<legend>Formulario</legend>";
$html.= "<label>Codigo</label>";
$html.= "<input type='number' name='codigoAgenda' value='' size='10'>";
$html.= "<label>Nombre</label>";
$html.= "<input type='text' name='nombreAgenda' value=''>";    
$html.= "<label>Telefono</label>";
$html.= "<input type='number' name='telefonoAgenda' value='' >";
$html.= "<label>Correo</label>";
$html.= "<input type='mail' name='correoAgenda' value=''>";
$html.= "<label>Fecha Nacimiento</label>";
$html.= "<input type='date' name='fechaAgenda' value=''>";
$html.= "&nbsp;&nbsp;<input type='submit' name='btnBuscar' value='Buscar' id='btnForm'>";
$html.= "<input type='submit' name='btnInsertar' value='Insertar' id='btnForm'>";
$html.= "<input type='submit' name='btnSalir' value='Cerrar sesión' id='btnSalir' >";
$html.= "</fieldset>";
$html.= "</form>";
echo $html;
echo "</br><p style='color:blue;'>$mensaje</br>";

/* PAGINACION */
// Calcular el total de los registros
$numRegistrosPagina = 6;

$consulta = "SELECT count(*) FROM agenda;";
$datos = $conexion->prepare($consulta);
$datos->execute();
$totalAgenda = $datos->fetch(PDO:: FETCH_ASSOC);

// Numero de paginas que vamos a necesitar en base al total de registros
foreach($totalAgenda as $clave => $valor) $paginas = $valor;
$paginas = $paginas/$numRegistrosPagina;
//Redondeo hacia arriba si hay decimales
if(strpos($paginas, ".")){
$paginas++;
$paginas= substr($paginas, 0, strpos($paginas, '.')); 
}

// Total registros por pagina
$registroPaginas=6;
// Pagina de inicio
//$pagina=1;
// Recibe la pagina a visitar
if(isset($_GET['pagina'])){
    if($_GET['pagina']==1){
        header('Location:login.php'); // Si es 1 va a la pagina principal
    }else{
        $pagina=$_GET['pagina'];
    }
}else{
    $pagina=1;
}
// Marcar el limite de inicio de la select, ira aumentando en base a la pagina
$empezarPagina = ($pagina-1)*$registroPaginas;

// Consula de datos en base a los criterios del limit, para ofrecer datos en cada pagina
$consulta = "SELECT * FROM agenda LIMIT $empezarPagina, $registroPaginas";
$datos = $conexion->prepare($consulta);
$datos->execute();
/*Pintar las tablas */
echo "<h3 id='titTablas'>Agenda</h3>";
echo "<table>";
echo "<tr>";
echo "<th>codigo</th>";
echo "<th>nombre</th>";
echo "<th>telefono</th>";
echo "<th>correo</th>";
echo "<th>fechaNac</th>";
echo "<th></th>";
echo "<th>Eliminar</th>";
echo "</tr>";
$listacodigosCheck = "";
while ($registro = $datos->fetch(PDO:: FETCH_ASSOC)){
    echo "<form action='' method='POST' id='formulario'>";
    echo "<tr>";
    echo "<td><input type='number' name='codigoAgenda' value='".$registro['codigo']."' readonly></td>";
    echo "<td><input type='text' name='nombreAgenda' value='".$registro['nombre']."'></td>";
    echo "<td><input type='number' name='telefonoAgenda' value='".$registro['telefono']."'></td>";
    echo "<td><input type='mail' name='correoAgenda' value='".$registro['correo']."'></td>";
    echo "<td><input type='date' name='fechaAgenda' value='".$registro['fechaNac']."'></td>";
    echo "<td><input type='submit' id='btnEditar' name='btnBuscarTabla' value='Editar' style='background-color:#F0A63C'</td>";
    echo "<td><input type='submit'name='btnEliminar' value='Eliminar'></td>";
    echo "</form>";
}
echo "</table>";

/* Tabla de eliminar */
$consulta = "SELECT * FROM agenda;";
$datos = $conexion->prepare($consulta);
$datos->execute();
echo "<h3 id='titTablas'>Eliminar</h3>";
echo "<table>";
echo "<form action='' method='POST' id='formulario'>";
echo "<input type='submit' id='btnEliminarChk' name='btnEliminar' value='Eliminar'>";
echo "<td>";
while ($registro = $datos->fetch(PDO:: FETCH_ASSOC)){
    echo "<td>ID</td>";
    echo "<td>".$registro['codigo']." </td>";
    echo "<td><input type='checkbox' id='chkControl' name='chkEliminarTabla[]' onclick='validate()' value='".$registro['codigo']."'></td>";
}
echo "</td>";
echo "</form>";
echo "</table>";

// Salida de los numeros para la seleccion de paginacion
echo "<br><br>";
for($i=1; $i<=$paginas; $i++){
    echo "<a style='margin-left: 5%; font-size: 1.5em;' href='?pagina=". $i. "' >" . $i. "</a> ";
}
echo "<br><br><br><br><br>";

// Cambiar de pagina si no hay registros para que no se quede vacia
foreach($totalAgenda as $c=>$v)$totalAgenda = $v;
if(isset($_POST['btnEliminar'])){
    if($totalAgenda<=6){
        header('Location:login.php?pagina=1');
        die();
    }elseif($totalAgenda<=12){
        header('Location:login.php?pagina=2'); 
        die();
    }elseif($totalAgenda<=18){
        header('Location:login.php?pagina=3');
        die();
    }
}


?>
</body>
</html>
