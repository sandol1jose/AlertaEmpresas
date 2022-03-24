<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Verificacion</title>

	<script src="../js/general.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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




<p class="parrafo1">Enviamos un código a tu correo electrónico</p>	

	


<form action="../app/VerificarEmail.php" method="POST">

	
	<input style="text-transform:uppercase" autocomplete="off" class="Input" placeholder="0000" type="text" name="codigo" id="codigo">

	<input hidden type="text" name="email" id="email" value="<?php echo $Correo; ?>">

	<div class="divTextoAviso">
		<p class="TextoAviso">Por favor ingresa el código enviado 
			a <b> <?php echo $Correo; ?> </b> para verificarlo</p>
	</div>
	
	
	<input class="BotonGuardar" type="submit" name="" value="Siguiente">
</form>

</body>
</html>