<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>

    <link rel="stylesheet" type="text/css" href="css/generalCpanel.css">
    <link rel="stylesheet" type="text/css" href="css/alertas.css">

</head>
<body>
<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<style>
    .InputGeneral{
        width: 250px;
    }
</style>

<?php
	if(isset($_SESSION["Alerta_Cpanel"])){
		$Alerta = $_SESSION["Alerta_Cpanel"];
        unset($_SESSION["Alerta_Cpanel"]);
		//echo = $Alerta;
		if (strcmp($Alerta, "passUpdate") === 0){
			echo "<script> alertsweetalert2('Contraseña actualizada', '', 'success'); </script>";
		}

		if (strcmp($Alerta, "passNoUpdate") === 0){
			echo "<script> alertsweetalert2('La contraseña no se pudo actualizar', '', 'error'); </script>";
		}

		if (strcmp($Alerta, "passIncorrect") === 0){
			echo "<script> alertsweetalert2('La contraseña anterior no es correcta', '', 'error'); </script>";
		}
	}
?>

<div class="divBackGround">
    <div class="divContenedor">

        <span> 
            <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">
            <b>Cambiar Contraseña</b>
        </span>

        <h1>Cambiar contraseña</h1>    
        <span>Modifica la contraseña para acceder al panel de administración</span><br><br>

        <form action="app/CambiarPassword.php" method="POST">
            <span>Contraseña anterior</span><br>
            <input class="InputGeneral" type="password" name="pass_ant" id="pass_ant" min="0"><br><br>
            
            <span>Contraseña nueva</span><br>
            <input onInput="VerificarPass()" class="InputGeneral" type="password" name="pass_new" id="pass_new" min="0"><br><br>

            <span>Repetir contraseña</span><br>
            <input onInput="VerificarPass()" class="InputGeneral" type="password" name="pass_new2" id="pass_new2" min="0"><br><br>
            <button disabled class="BotonGeneral2" id="btn" type="submit">Actualizar</button>
        </form>

    </div>
</div>
</body>
</html>



<script>
    function VerificarPass(){
        var pass1 = document.getElementById("pass_new").value;
        var pass2 = document.getElementById("pass_new2").value;
        var btn = document.getElementById("btn");

        if(pass1 != "" || pass2 != ""){
            if(pass1 == pass2 ){
                btn.disabled = false;
            }

            if(pass1 != pass2){
                btn.disabled = true;
            }
        }
    }
</script>