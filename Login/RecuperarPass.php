<?php
session_start();
?>

<?php include '../templates/encabezadoBlack.php'; ?>
<!DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" type="text/css" href="css/RecuperarPass.css">
</head>
<body>

<?php
	if(isset($_SESSION["Alerta"])){
		$Alerta = $_SESSION["Alerta"];
		if (strcmp($Alerta, "MailNoExist") === 0){
			echo "<script> alertsweetalert2('El correo no está registrado', '', 'error'); </script>";
		}
		unset($_SESSION["Alerta"]);
	}
?>

<div class="divBackGround">
	<div class="divBase">
		<b>Recuperar contraseña</b>

		<!--
		<img src="../imagenes/jingle-keys.gif" width="100px">-->

		<p>Enviaremos un codigo a tu correo:</p>

		<form action="../app/RecuperarPass.php" method="POST" >
			<input class="inputGeneral" type="email" name="Email" id="Email" placeholder="Escribe tu correo" autocomplete="off" required>
			<br><br>
			<input class="BotonGeneral2"  type="submit" name="" value="Recuperar" onclick="CambiarImagen();">

			<div  class="DivContenedorImagen">
				<div id="DivImg2" style="display: none;">
					<br>
					<img class="imgCargando" src="../imagenes/Cargando6Recorte.gif" height="30px">
				</div>
			</div>
		</form>
	</div>
</div>

<?php include '../templates/footer.php'; ?>	

</body>
</html>



<script>
	function CambiarImagen(){
		var campo = document.getElementById("Email").value;
		if(campo != ""){
			//Solo si se escribio algo en el campo "Email"
			$("#DivImg2").show();
		}
	}
</script>