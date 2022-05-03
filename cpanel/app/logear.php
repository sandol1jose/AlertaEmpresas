<?php
session_start();//inicio de sesion
session_start();//inicio de sesion
$root = str_replace('\\', '/', dirname(__DIR__));
$root = str_replace('/cpanel', '', $root);
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

if(!isset($_POST['user']) || !isset($_POST['pass'])){
    header('Location: ../login.php');
}

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->config;
$filter = ["tipo" => "usuariocpanel"];//Buscando la configuracion de tipo usuariocpanel
$Result = $collection->findOne($filter);

if($Result != NULL){
    $USUARIO = $Result["user"];
    $PASS = $Result["pass"];

    $User = $_POST['user'];
    $Pass = $_POST['pass'];

    if($User == $USUARIO && $Pass == $PASS){
        $arrayADMIN_CPANEL = array(
            'Usuario'=>$User,
        );
        $_SESSION['ADMIN_CPANEL'] = $arrayADMIN_CPANEL;

        header('Location: ../index.php');
    }else{
        header('Location: ../login.php?alerta=0');
    }
}else{
    header('Location: ../login.php');
}
?>