<?php
session_start();
?>


<!DOCTYPE html>
<html>
<head>
	<title>Registro de usuarios</title>
	
	<script src="../js/general.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<?php
	if(isset($_SESSION["Alerta"])){
		$Alerta = $_SESSION["Alerta"];
		if(strcmp($Alerta, 'CorreoYaExiste') === 0){
			echo "<script> alertsweetalert2('Error', 'El correo ya existe', 'error'); </script>";
		}
		unset($_SESSION["Alerta"]);
	}
?>

<p>Registrarse</p>

<form method="POST" action="../app/RegistrarUsuario.php">

    <p>Crea un usuario y contraseña nueva</p>

    <input type="text" id="nombres" name="nombres" placeholder="Nombres"><br>
    <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos"><br>
    <input type="text" id="empresa" name="empresa" placeholder="Empresa"><br>
    <input placeholder="Correo" type="text" name="correo" id="correo" autocomplete="off" spellcheck="false" required><br>
    <input onkeyup="verificarContrasenia();" placeholder="Contraseña nueva" type="password" name="pass" id="pass" autocomplete="off" spellcheck="false" required><br>
    <input onkeyup="verificarContrasenia();" placeholder="Confirmar contraseña" type="password" name="pass2" id="pass2" autocomplete="off" spellcheck="false" required><br>

    <img id="IMGSeguridad" name="IMGSeguridad" src="../imagenes/Seguridad4.png" width="40px;">

    <span id="txtSeguridadpass" style="font-size: 12px; font-weight: normal;"></span>
            
    <p>Recuerda utilizar mayúsculas, minúsculas, 
    números y mínimo 8 caracteres para que tu contraseña sea segura</p>

    <div id="DivImg2" style="display: none;">
        <img src="../imagenes/Cargando6Recorte.gif" width="70px"><br>
        <span>Cargando</span>
    </div>
                
                
    <div id="DivButton">
		<input disabled class="BotonGuardar" type="submit" name="btn" id="btn" value="Siguiente" onclick="CambiarImagen();">
	</div>
</form>



</body>
</html>

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
		console.log("Cambiando");
		$("#DivButton").hide();
		$("#DivImg2").show();
	}
</script>