<?php
//AGREGANDO NUEVA CUENTA BANCARIA
session_start();//inicio de sesion
//require '../Archivos de Ayuda PHP/conexion.php';
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');

    if(!isset($_SESSION["Correo"])){
        $_SESSION["Alerta"] = "CodCaducate"; 
        header('Location: ../Login/index.php');
    }

    $Correo = $_SESSION["Correo"];
    $Pass = $_POST["pass"];
    $codigo = strtoupper($_POST["codigo"]);

    $PassCifrada = password_hash($Pass, PASSWORD_DEFAULT); //Encriptando contraseñas

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;
    $filter = ["email" => $Correo];
    $Result = $collection->findOne($filter);

    $encontrado = false;
    $Arrayindex = 0;
    if(isset($Result["codigo_update_pass"])){
        foreach($Result["codigo_update_pass"] as $valor){
            $cod = $valor["codigo"];
            $confirmacion = $valor["confirmacion"];
            if($cod == $codigo && $confirmacion == false){
                $encontrado = true;
                break;
            }
            $Arrayindex++;
        }
    }

    if($encontrado == true){
        //El codigo es correcto
        $filter = ["email" => $Correo];
        $document = ['$set' => 
            [
                "password" => $PassCifrada,
                "codigo_update_pass.".$Arrayindex.".confirmacion" => true
            ]
        ];
        $Result = $collection->updateOne($filter, $document);

        if($Result->getModifiedCount() == 1){
            //SE CAMBIO CORRECTAMENTE LA CONTRASEÑA
            unset($_SESSION["Correo"]);//Borramos la sesion de correo
            $_SESSION["Alerta"] = "passUpdate"; //Pass actualizada correctamente
            header('Location: ../Login/index.php');
        }else{
            echo "ocurrio un error";
        }
    }else{
        //Codigo Incorrecto
        $_SESSION["Alerta"] = "CodPassIncorrect";
        header('Location: ../Login/CambiarPass.php');
    }
?>