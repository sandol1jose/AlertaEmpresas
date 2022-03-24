<?php
    session_start();//inicio de sesion
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="js/general.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <title>Inicio</title>
</head>
<body>

    <?php
		if(isset($_SESSION['Alerta'])){
			$Alerta = $_SESSION['Alerta'];
			if (strcmp($Alerta, "Logout") === 0){
				echo "<script> alertsweetalert2('Has salido de tu cuenta', '', 'success'); </script>";
			}
			unset($_SESSION["Alerta"]);
		}
	?>


    <?php
    	if(isset($_SESSION["Cliente"])){?>
        <a href="Cuenta"><?php echo $_SESSION["Cliente"]["Nombres"]; ?></a>
        <a href="Login/LogOut.php">cerrar sesion</a>
    <?php } ?>

    <h1>Alerta Empresas</h1>
    <form action="ListarResultados.php" method="POST">
        <input type="text" name="buscador"><br><br>
        <input type="submit" value="Buscar">
    </form>
</body>
</html>