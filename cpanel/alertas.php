<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>

<?php
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->config;
    $filter = ["tipo" => "Alertas"];//Buscando la configuracion de tipo Alertas
    $Result = $collection->findOne($filter);
    $NumAlertas = 0;
    if($Result != NULL){
        $NumAlertas = $Result["cantidad"];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas</title>

    <link rel="stylesheet" type="text/css" href="css/generalCpanel.css">
    <link rel="stylesheet" type="text/css" href="css/alertas.css">
</head>
<body>
<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<?php
    if(isset($_SESSION["Actualizacion_Exito"])){
        unset($_SESSION["Actualizacion_Exito"]);
        echo "<script> alertsweetalert2('Actualizado correctamente', '', 'success'); </script>";
    }
?>


<div class="divBackGround">
    <div class="divContenedor">
        <span> 
            <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">
            <b>Alertas</b>
        </span>

        <h1>Alertas</h1>    
        <span>Modifica la cantidad máxima de alertas por usuario</span><br><br>

        <form action="app/actalertas.php" method="POST">
            <input class="InputGeneral" type="number" name="numalertas" min="0" value="<?php echo $NumAlertas; ?>"><br><br>
            <button class="BotonGeneral2" type="submit">Actualizar</button>
        </form>
    </div>
</div>
</body>
</html>