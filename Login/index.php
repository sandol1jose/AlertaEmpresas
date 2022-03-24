<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Inicio de Sesion</title>

	<script src="../js/general.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
<?php
	if(isset($_SESSION["Alerta"])){
		$Alerta = $_SESSION["Alerta"];
		//echo = $Alerta;
		if (strcmp($Alerta, "passUpdate") === 0){
			echo "<script> alertsweetalert2('Contraseña recuperada', '', 'success'); </script>";
		}

		if (strcmp($Alerta, "passIncorrect") === 0){
			echo "<script> alertsweetalert2('Contraseña incorrecta', '', 'error'); </script>";
		}

		if (strcmp($Alerta, "EmailIsNotExist") === 0){
			echo "<script> alertsweetalert2('El correo ingresado no existe', '', 'error'); </script>";
		}

		if (strcmp($Alerta, "CodCaducate") === 0){
			echo "<script> alertsweetalert2('El código ha caducado', '', 'error'); </script>";
		}

		unset($_SESSION["Alerta"]);
	}
?>


<p>Iniciar Sesión</p>


<p>Ingresa tus credenciales</p>

	<form method="POST" action="Logear.php">
		<input type="email" name="email" id="email" placeholder="usuario@ejemplo.com" autocomplete="off" required>

		<input type="password" name="password" id="password" placeholder="Contraseña"  autocomplete="off" required><br>				

		<a href="RecuperarPass.php">¿Olvidaste tu contraseña?</a><br>

		<input type="submit" name="enviar" value="Ingresar">
	</form>

	<a href="registro.php">Registrarme</a><br>

</body>
</html>