<?php
session_start();
    //require '../Archivos de Ayuda PHP/conexion.php';
	//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');
    include_once "../Sesiones/sesCliente.php";

	$Correo = $_POST["email"];
	$password = $_POST["password"];

	//verificando si el usuario existe en la base de datos
    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;
    $filter = ["email" => $Correo];
    $Result = $collection->find($filter)->toArray();

	if(count($Result) == 1){
		RecorrerRegistros($Result, $password, $Correo);
	}else{
        //correo no existe
        $_SESSION['Alerta'] = "EmailIsNotExist";
        header('Location: index.php?email=' . $Correo);
	}

	//$modo es para verificar si el usuario ya tiene todos sus datos o no
	function RecorrerRegistros($registro, $pass, $email){
        $PassReal = $registro[0]["password"];
        $Verificado = $registro[0]["verificado"];
        if(password_verify($pass, $PassReal)){
            //contrasenia correcta
            if($Verificado == true){//Vemos si el correo esta verificado
                $IDCliente = $registro[0]["_id"];
                $Nombres = $registro[0]["nombres"];
                $Apellidos = $registro[0]["apellidos"];
                $Empresa = $registro[0]["empresa"];

                //Creando la sesion
                CrearSesion($IDCliente, $Nombres, $Apellidos, $email, $Empresa);

                //Creando las cookies
                setcookie("COOKIE_CLIENTE_EMAIL", $email, time() + (86400 * 30)); // 86400 = 1 day
                setcookie("COOKIE_CLIENTE_PASS", $pass, time() + (86400 * 30)); // 86400 = 1 day
                $_SESSION['Alerta'] = "inicioSesion";
                header('Location: ../');
            }else{
                //El correo no ha sido verificado
                header('Location: AnuncioEmailNoVerificado.php?email=' . $email);
            }
        }else{
            //contraseña incorrecta
            $_SESSION['Alerta'] = "passIncorrect";
            header('Location: index.php?email=' . $email);
        }
	}
?>