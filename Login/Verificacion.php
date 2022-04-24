<?php
session_start();
?>
<?php include '../templates/encabezadoBlack.php'; ?>

<!DOCTYPE html>
<html>
<head>
	<title>Verificacion</title>

	<link rel="stylesheet" type="text/css" href="css/Verificacion.css">
</head>
<body>



<?php
	if(!isset($_GET['email'])){
		header('Location: ../index.php'); //envia a la página de inicio.
	}else{
		$Correo = $_GET['email'];
	}
	
	if(isset($_SESSION["Alerta"])){
		$Alerta = $_SESSION["Alerta"];
		if(strcmp($Alerta, 'CodigoIncorrecto') === 0){
			echo "<script> alertsweetalert2('Error', 'Código incorrecto', 'error'); </script>";
		}
		unset($_SESSION["Alerta"]);
	}
?>

<div class="divBase">

	
	<p class="parrafo1">Enviamos un código a tu correo electrónico</p>	
	<div class="divTextoAviso">
		<p class="TextoAviso">Por favor ingresa el código enviado 
			a <b> <?php echo $Correo; ?> </b> para verificarlo</p>
	</div>

	<form action="../app/VerificarEmail.php" method="POST">
		<input class="InputGeneral" style="text-transform:uppercase" autocomplete="off" class="Input" placeholder="0000" type="text" name="codigo" id="codigo">
		<input hidden type="text" name="email" id="email" value="<?php echo $Correo; ?>"><br><br>
		<input class="BotonGeneral2" type="submit" name="" value="Siguiente">
	</form>

	<div class="divTextoAviso">
		<p class="TextoAviso">Si no aparece, revisa la carpeta de spam</p>
	</div>

</div>


<?php include '../templates/footer.php'; ?>

</body>
</html>