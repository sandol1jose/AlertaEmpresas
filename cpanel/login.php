<?php
session_start();//inicio de sesion
if(isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: index'); //Agregamos el producto
}
?>

<?php   
    if(isset($_GET["datos"])){
        $Alerta = $_GET["datos"];
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/generalCpanel.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
<?php include '../templates/encabezadoBlack.php'; ?>

<div class="divBackGround">
    <div class="divContenedor">
        <div class="divCentrar">
        <h3>Panel de Administración</h3>

        <form action="app/logear.php" method="POST">
            <span>Usuario</span><br>
            <input class="InputGeneral" type="text" name="user" placeholder="Usuario"><br><br>
            
            <span>Contraseña</span><br>
            <input class="InputGeneral" type="password" name="pass" placeholder="Contraseña"><br><br>

            <?php
                if(isset($_GET["alerta"])){
                    if($_GET["alerta"] == '0'){?>
                        <span>Los datos son incorrectos</span><br><br>
                    <?php
                    }
                }
            ?>

            <input class="BotonGeneral2" type="submit" value="Entrar">
        </form>
        </div>
    </div>
</div>
</body>
</html>