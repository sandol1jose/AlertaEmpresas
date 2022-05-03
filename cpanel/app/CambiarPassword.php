<?php
session_start();//inicio de sesion
$root = str_replace('\\', '/', dirname(__DIR__));
$root = str_replace('/cpanel', '', $root);
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

if(!isset($_POST["pass_ant"]) || !isset($_POST["pass_new"])){
    header('Location: ../index.php');
}

$pass_ant = $_POST["pass_ant"];
$pass_new = $_POST["pass_new"];

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->config;
$filter = ["tipo" => "usuariocpanel"];//Buscando la configuracion de tipo usuariocpanel
$Result = $collection->findOne($filter);

if($Result != NULL){
    if($pass_ant == $Result["pass"]){//La contraseña anterior es correcta
        $document = [ '$set' => ["pass" => $pass_new]];
        $Result = $collection->UpdateOne($filter, $document);
        if($Result->getModifiedCount() == 1){
            $_SESSION["Alerta_Cpanel"] = "passUpdate";
        }else{
            $_SESSION["Alerta_Cpanel"] = "passNoUpdate";
        }
    }else{
        $_SESSION["Alerta_Cpanel"] = "passIncorrect";
    }
}

header('Location: ../CambiarPass.php');
?>