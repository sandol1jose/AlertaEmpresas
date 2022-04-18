<?php
session_start();
	//require '../Archivos de Ayuda PHP/conexion.php';
    //require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
    $root = str_replace('\\', '/', dirname(__DIR__));
	require_once($root . '/Archivos de Ayuda PHP/conexion.php');
    include 'EnviarCorreo.php';

    $Email = $_POST["Email"];

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;

    $filtro = ["email" => $Email];
    $Result = $collection->findOne($filtro);

    if($Result != NULL){
        //El correo si existe
        EnviarEmail($Email, $collection);
        header('Location: ../Login/Verificacion.php?email=' . $Email); //envia a la página de inicio.
    }else{
        //El correo no existe
        $_SESSION["Alerta"] = "EmailIsNotExist";
        header('Location: ../Login/index.php'); //envia a la página de inicio.
    }
?>