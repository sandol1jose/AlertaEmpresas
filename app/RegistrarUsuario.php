<?php
    //REGISTRO DEL USUARIO EN LA BASE DE DATOS
    session_start();
    //require '../Archivos de Ayuda PHP/conexion.php';
    //require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
    $root = str_replace('\\', '/', dirname(__DIR__));
	require_once($root . '/Archivos de Ayuda PHP/conexion.php');
	include 'EnviarCorreo.php';

	$Correo = $_POST["correo"];
	$Correo = strtolower($Correo);//Convirtiendo todo el correo a minusculas

    $Pass = $_POST["pass"];
    $Nombres = $_POST["nombres"];
    $Apellidos = $_POST["apellidos"];
    $Empresa = $_POST["empresa"];
    
    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;
    $filter = ["email" => $Correo];
    $Result = $collection->find($filter)->toArray();

	if(count($Result) == 0){ //El correo no existe en nuestra base de datos
		$PassCifrada = password_hash($Pass, PASSWORD_DEFAULT); //Encriptando contraseñas

        $document = [
            "nombres" => $Nombres,
            "apellidos" => $Apellidos ,
            "email" => $Correo,
            "password" => $PassCifrada,
            "empresa" => $Empresa
        ];

        $Result = $collection->insertOne($document);
        $insertado = $Result->getInsertedCount();
		if($insertado == 1){
			//SE AGREGO CORRECTAMENTE AL CLIENTE
			EnviarEmail($Correo, $collection);
			header('Location: ../Login/Verificacion.php?email=' . $Correo); //envia a la página de inicio.
		}else{
			echo "ocurrio un error";
		}
	}else{
		//El correo ya existe en la base de datos
		$_SESSION["Alerta"] = "CorreoYaExiste";
		header('Location: ../Login/registro.php'); //envia a la página de inicio.
	}
?>