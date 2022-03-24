<?php

    $email = $_GET["email"];

?>

<!DOCTYPE html>
<html>
<head>
	<title>Email no verificado</title>
</head>
<body>

	<h5>Email no verificado</h5>

	<h2>Necesita <br> verificacion</h2>

	<p>Tu correo electronico no ha sido verificado</p>

	<img src="../imagenes/ErorConfirm.gif" width="150px">

	<form action="../app/ReenviarEmail.php" method="POST" >

		<input type="email" name="Email" id="Email" autocomplete="off" required value="<?php echo $email; ?>">
		<span>Escribe tu correo</span>

		<a style="font-size: 13px;" href="Verificacion.php?email=<?php echo $email; ?>">-Tengo un código-</a>

		<input type="submit" name="" value="Reenviar codigo" onclick="CambiarImagen();">
	</form>

	<div id="DivImg2" style="display: none;">
		<img src="../imagenes/Cargando6Recorte.gif" width="100px">
		<h6>cargando...</h6>
		<br><br>
	</div>

</body>
</html>



<script>
	function CambiarImagen(){
		console.log("Cambiando");
		$("#DivForm").hide();
		$("#DivimgError").hide();

		$("#DivImg2").show();
	}
</script>