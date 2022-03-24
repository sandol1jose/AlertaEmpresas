<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Recuperara password</title>
	<script src="../js/general.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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


<p>Recuperar contraseña</p>

	<img src="../imagenes/jingle-keys.gif" width="199px">

	<p>Enviaremos un codigo a tu correo:</p>

	<form action="../app/RecuperarPass.php" method="POST" >
		
		<input type="email" name="Email" id="Email" placeholder="Escribe tu correo" autocomplete="off" required>
		
		<div  class="DivContenedorImagen">
			<div id="DivImg2" style="display: none;">
				<img class="imgCargando" src="../imagenes/Cargando6Recorte.gif" height="30px">
			</div>
		</div>

		
		<input  type="submit" name="" value="Enviar codigo" onclick="CambiarImagen();">
	</form>
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