<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Entrada</title>

    <link rel="stylesheet" type="text/css" href="css/generalCpanel.css">
    <link rel="stylesheet" type="text/css" href="css/alertas.css">

</head>
<body>
<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<style>
    .InputGeneral{
        width: 250px;
    }
</style>

<div class="divBackGround">
    <div class="divContenedor">

        <span> 
            <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">
            <b>Eliminar Entrada</b>
        </span>

        <h1>Eliminar Entrada</h1>    
        <span>Busca la empresa que quieras eliminar</span><br><br>

        <form action="../app/buscador.php" method="POST">
            <input class="InputGeneral" style="width: 600px" type="text" name="buscador" id="buscador" autocomplete="off"><br><br>
            <button class="BotonGeneral2" id="btn" type="submit">Buscar</button>
        </form>

    </div>
</div>
</body>
</html>