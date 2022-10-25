<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cpanel</title>
    <link rel="stylesheet" type="text/css" href="css/generalCpanel.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>
<div class="divBackGround">
    <div class="divContenedor">
        
        <h1>Cpanel</h1><br>

        <div class="divCont2">
        <div class="grid-container">
            <div class="item1" onclick="location.href='alertas.php'">
                <br><br>
                <img src="../imagenes/cpanel/notification.png" alt="" width="70px"><br><br>
                <span class="subtitulo">Alertas</span><br><br>
                <span class="parrafo1">Modifica la cantidad de alertas que recibirán los usuarios</span>
            </div>

            <div class="item2" onclick="location.href='usuarios.php'">
                <br><br>
                <img src="../imagenes/cpanel/users2.png" alt="" width="70px"><br><br>
                <span class="subtitulo">Usuarios</span><br><br>
                <span class="parrafo1">Visualiza los usuarios registrados en el sistema</span>  
            </div>

            <div class="item3" onclick="location.href='titulopaginas.php'">
                <br><br>
                <img src="../imagenes/cpanel/title.png" alt="" width="70px"><br><br>
                <span class="subtitulo">Titulos de páginas</span><br><br>
                <span class="parrafo1">Modifica los titulos que aparecen en las páginas</span>          
            </div>

            <div class="item4" onclick="location.href='agregarpaginas.php'">
                <br><br>
                <img src="../imagenes/cpanel/document.png" alt="" width="70px"><br><br>
                <span class="subtitulo">Agregar Páginas</span><br><br>
                <span class="parrafo1">Agrega, modifica y elimina páginas del "Footer"</span>          
            </div>

            <div class="item5" onclick="location.href='CambiarPass.php'">
                <br><br>
                <img src="../imagenes/cpanel/Pass.png" alt="" width="70px"><br><br>
                <span class="subtitulo">Contraseña</span><br><br>
                <span class="parrafo1">Cambia la contraseña del usuario administrador</span>          
            </div>

            <div class="item5" onclick="location.href='EliminarEntrada.php'">
                <br><br>
                <img src="../imagenes/cpanel/Eliminar.png" alt="" width="70px"><br><br>
                <span class="subtitulo">Eliminar Entrada</span><br><br>
                <span class="parrafo1">Elimina los datos de una empresa en particular</span>          
            </div>
        </div>
        </div>



    </div>
</div>
</body>
</html>