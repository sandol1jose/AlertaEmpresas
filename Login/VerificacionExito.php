<?php
session_start();
?>

<?php include '../templates/encabezadoBlack.php'; ?>
<!DOCTYPE html>
<html>
<head>
	<title>Verificacion exitosa</title>

	<link rel="stylesheet" type="text/css" href="css/Verificacion.css">
</head>
<body>

<?php if(isset($_SESSION["VERIFICACIONEXITO"])){ ?>

<script>
	alertsweetalert2('Accion exitosa', '', 'success'); 
</script>

<?php 
unset($_SESSION["VERIFICACIONEXITO"]);
} ?>
		
<div class="divBase">

	<p class="Texto2">Verificación exitosa</p>
	<img class="imagen" src="../imagenes/check2.png" width="15px">
	<span class="parrafo1">Tu correo electrónico ahora está verificado, ya puedes ingresar a nuestro sistema</span>
	<br><br>
	<button class="BotonGeneral2" onclick="window.location.href='index.php'">Siguiente</button>
				
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>