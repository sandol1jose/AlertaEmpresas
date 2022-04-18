<?php
session_start();

if(isset($_SESSION["Cliente"])){
	header('Location: ../');
}

$Email = "";
if(isset($_GET["email"])){
	$Email = $_GET["email"];
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Inicio de Sesion</title>

	<link rel="stylesheet" type="text/css" href="css/index.css">
</head>

<body>
<?php include '../templates/encabezadoBlack.php'; ?>

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

		if(strcmp($Alerta, 'CorreoYaExiste') === 0){
			echo "<script> alertsweetalert2('Error', 'El correo ya existe', 'error'); </script>";
		}

		unset($_SESSION["Alerta"]);
	}
?>


<div class="grid-container3">
	<div class="item1">
		<div class="Formularios">

			<div class="divBotones">
				<div class="divBtn_Izquierdo">
					<button class="boton" onclick="CambiarDiv('Registro')">Registrarse</button>
				</div>
				
				<div class="divBtn_Derecho">
					<button class="boton" onclick="CambiarDiv('Login')">Iniciar Sesion</button>
				</div>
			</div>

			<div class="divdatos" id="divLogin" style="display: block;">
				<center>
					<div class="TituloDatos">Ingresa a tu cuenta</div>
				</center>
				<form method="POST" action="Logear.php">
					
					<div class="TituloCampo">Usuario</div>

					<div>
						<input class="InputGeneral" type="email" name="email" id="email" placeholder="Introduce tu email o usuario" autocomplete="off" value="<?php echo $Email; ?>" required>
					</div>

					<div class="TituloCampo">Contraseña</div>
					
					<div>
						<input class="InputGeneral" type="password" name="password" id="password" placeholder="Introduce tu contraseña"  autocomplete="off" required>			
					</div>


					<div class="divVinculo1">
						<a class="inputVinculos" href="RecuperarPass.php">He olvidado la contraseña</a><br>
					</div>

					<input class="BotonGeneral" type="submit" name="enviar" value="Ingresar">
				</form>

				<!--
				<div class="divVinculo">
					<a class="inputVinculos" href="registro.php">Registrarme</a>
				</div>-->
			</div>



			<div class="divdatos" style="display: none;" id="divRegistro">
				<form method="POST" action="../app/RegistrarUsuario.php">
					<center>
					<p>Crea un usuario y contraseña nueva</p>
					</center>

					<div class="TituloCampo">Nombres *</div>
					<input class="InputGeneral" type="text" id="nombres" name="nombres" placeholder="Nombres" autocomplete="off" required><br>
					<div class="TituloCampo">Apellidos *</div>
					<input class="InputGeneral" type="text" id="apellidos" name="apellidos" placeholder="Apellidos" utocomplete="off" required><br>
					<div class="TituloCampo">Empresa</div>
					<input class="InputGeneral" type="text" id="empresa" name="empresa" placeholder="Empresa" autocomplete="off"><br>
					<div class="TituloCampo">Correo *</div>
					<input class="InputGeneral" placeholder="Correo" type="text" name="correo" id="correo" autocomplete="off" spellcheck="false" required><br>
					<div class="TituloCampo">Contraseña nueva *</div>
					<input class="InputGeneral" onkeyup="verificarContrasenia();" placeholder="Contraseña nueva" type="password" name="pass" id="pass" autocomplete="off" spellcheck="false" required><br>
					<div class="TituloCampo">Confirmar contraseña *</div>
					<input class="InputGeneral" onkeyup="verificarContrasenia();" placeholder="Confirmar contraseña" type="password" name="pass2" id="pass2" autocomplete="off" spellcheck="false" required><br>

					<div class="ImagenContrasenia">
						<img id="IMGSeguridad" name="IMGSeguridad" src="../imagenes/Seguridad4.png" width="40px;">
						<span id="txtSeguridadpass" style="font-size: 12px; font-weight: normal;"></span>
					</div>
							
					<center>
					<p style="font-size: 12px;">Recuerda utilizar mayúsculas, minúsculas, 
					números y mínimo 8 caracteres para que tu contraseña sea segura</p>

					<div id="DivImg2" style="display: none;">
						<img src="../imagenes/Cargando6Recorte.gif" width="40px"><br>
						<span style="font-size: 12px;">Cargando</span>
					</div>
					</center>
								
								
					<div id="DivButton" style="display: block;">
						<input class="BotonGeneral" disabled class="BotonGuardar" type="submit" name="btn" id="btn" value="Siguiente" onclick="CambiarImagen();">
					</div>
				</form>
			</div>


		</div>
	</div>

	<div class="item2">
		<div class="divAdorno">
			<center>
				<img src="../imagenes/fluent_globe-search-24-filled.png" alt="" width="200px"><br>
				<span class="TituloDatos">Accede a tu cuenta y gestiona <br> tus alertas</span><br>
				<div class="parrafo">Programa alertas de las empresas <br> que quieras saber</div>
			</center>
		</div>
	</div>
</div>


<!--	
<div class="Base">
	<div class="Cuadro1">

	</div>
</div>
-->


<?php include '../templates/footer.php'; ?>

</body>
</html>




<script>
	function CambiarDiv(Cambiar_A){
		var divLogin = document.getElementById("divLogin");
		var divRegistro = document.getElementById("divRegistro");
		if(Cambiar_A == "Login"){
			divRegistro.style.display = "none";
			divLogin.style.display = "block";
		}else if(Cambiar_A == "Registro"){
			divRegistro.style.display = "block";
			divLogin.style.display = "none";
		}
	}
</script>














<script type="text/javascript">

    function verificarContrasenia(){
        var pass1 = document.getElementById("pass").value;
        var pass2 = document.getElementById("pass2").value;
        if(pass1 != "" || pass2 != ""){
			validar_clave();
            if(pass1 == pass2){
				console.log("Las contraseñas son iguales");
				if(Puntuacion == 100){
					//hideKeyboard($('input'));//Ocultando el teclado si la contraseña es correcta
					/*alertsweetalert2('Las contraseñas coinciden', '', 'success');*/
                	document.getElementById("btn").disabled = false;
					$("#btn").focus();
				}else{
					alertsweetalert2('Alerta', 'La contraseña debe estar en verde', 'error');
				}
            }else{
                document.getElementById("btn").disabled = true;
            }
        }else{
			document.getElementById("IMGSeguridad").src = "../imagenes/Seguridad4.png";
			document.getElementById("txtSeguridadpass").innerHTML = "";
		}
    }


	var Puntuacion = 0;
	function validar_clave(){
		var contrasenna = document.getElementById('pass').value;
		//valida la seguridad de la contrasenia
		//if(contrasenna.length >= 8)
		//{		
			Puntuacion = 0
			var mayuscula = false;
			var minuscula = false;
			var numero = false;
			var caracter_raro = false;
			var seguraTotal = false;
			
			for(var i = 0;i<contrasenna.length;i++)
			{
				if(contrasenna.charCodeAt(i) >= 65 && contrasenna.charCodeAt(i) <= 90)
				{
					mayuscula = true;
				}
				else if(contrasenna.charCodeAt(i) >= 97 && contrasenna.charCodeAt(i) <= 122)
				{
					minuscula = true;
				}
				else if(contrasenna.charCodeAt(i) >= 48 && contrasenna.charCodeAt(i) <= 57)
				{
					numero = true;
				}/*
				else
				{
					caracter_raro = true;
				}*/
			}
			if(mayuscula == true && minuscula == true /*&& caracter_raro == true*/ && numero == true && (contrasenna.length >= 8))
			{
				console.log('Contrasenia segura');
				seguraTotal = true;
				//return true;
			}
		//}

		if(mayuscula == true){Puntuacion = Puntuacion + 15;}
		if(minuscula == true){Puntuacion = Puntuacion + 10;}
		//if(caracter_raro == true){Puntuacion = Puntuacion + 20;}
		if(numero == true){Puntuacion = Puntuacion + 15;}
		if(seguraTotal == true){Puntuacion = Puntuacion + 20;}
		if(contrasenna.length >= 8){Puntuacion = Puntuacion + 40;}
		console.log("Seguridad de contraseña: " + Puntuacion + "%");

		if(Puntuacion <= 25){
			document.getElementById("IMGSeguridad").src = "../imagenes/Seguridad1.png";
			document.getElementById("txtSeguridadpass").innerHTML = "Contraseña débil";
			$("#txtSeguridadpass").css("color", "#ed1818");
		}else if((Puntuacion >= 26) && (Puntuacion <= 99)){
			document.getElementById("IMGSeguridad").src = "../imagenes/Seguridad2.png";
			document.getElementById("txtSeguridadpass").innerHTML = "Contraseña semisegura";
			$("#txtSeguridadpass").css("color", "#e3bf00");
		}else if(Puntuacion == 100){
			document.getElementById("IMGSeguridad").src = "../imagenes/Seguridad3.png";
			document.getElementById("txtSeguridadpass").innerHTML = "Contraseña segura";
			$("#txtSeguridadpass").css("color", "#37cd30");
		}

		//return false;
	}
</script>


<script>
//Ocultar teclado
function hideKeyboard(element) {
    element.attr('readonly', 'readonly'); // Force keyboard to hide on input field.
    element.attr('disabled', 'true'); // Force keyboard to hide on textarea field.
    setTimeout(function() {
        element.blur();  //actually close the keyboard
        // Remove readonly attribute after keyboard is hidden.
        element.removeAttr('readonly');
        element.removeAttr('disabled');
    }, 100);
}
</script>


<script>
	function CambiarImagen(){
		//console.log("Cambiando");
		var nombres = document.getElementById("nombres").value;
		var apellidos = document.getElementById("apellidos").value;
		var correo = document.getElementById("correo").value;
		if(nombres != "" && apellidos != "" && correo != ""){
			$("#DivButton").hide();
			$("#DivImg2").show();
		}
	}
</script>