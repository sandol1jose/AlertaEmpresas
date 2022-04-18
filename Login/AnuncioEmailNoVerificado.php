<?php

	if(!isset($_GET["email"])){
		header('Location: ../Login/index.php');
	}

    $email = $_GET["email"];
?>
<?php include '../templates/encabezadoBlack.php'; ?>

<!DOCTYPE html>
<html>
<head>
	<title>Email no verificado</title>

	<link rel="stylesheet" type="text/css" href="css/EmailNoVerificado.css">
</head>
<body>

<div class="divBase">

	
	<span class="Titulo"><b>Email no verificado</b></span>
	<img src="../imagenes/Error.png" width="20px">

	<p>Necesita verificacion</p>

	<span>Escribe tu correo</span><br>

	

	<form action="../app/ReenviarEmail.php" method="POST" >

		<input class="InputGeneral" type="email" name="Email" id="Email" autocomplete="off" required value="<?php echo $email; ?>">
		<br>

		<br>
		<input class="BotonGeneral2" type="submit" name="" value="Reenviar codigo" onclick="CambiarImagen();">
		<br><br>
		<a style="font-size: 13px;" href="Verificacion.php?email=<?php echo $email; ?>">-Tengo un código-</a>
	</form>

	<div id="DivImg2" style="display: none;">
		<img src="../imagenes/Cargando6Recorte.gif" width="30px">
	</div>
</div>	
<?php include '../templates/footer.php'; ?>	
</body>
</html>



<script>
	function CambiarImagen(){
		var email = document.getElementById("Email").value;
		if(email != ""){
			$("#DivForm").hide();
			$("#DivimgError").hide();
			$("#DivImg2").show();
		}
	}
</script>