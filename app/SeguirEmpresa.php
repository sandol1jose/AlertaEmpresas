<?php

session_start();//inicio de sesion
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

$idEmpresa = $_POST["idEmpresa"];
$Correo = $_POST["Correo"];
$position = $_POST["position"]; //Posicion en el array del cliente
$tipo = $_POST["tipo"];


$conexion = new Conexion();
$database = $conexion->Conectar();


//BUSCANDO LA CONFIGURACION DE CUANTAS ALERTAS SE PUEDEN AGREGAR
$collection = $database->config;
$filter = ["tipo" => "Alertas"];//Buscando la configuracion de tipo Alertas
$Result = $collection->findOne($filter);
$NumAlertas = 0;
if($Result != NULL){
    $NumAlertas = $Result["cantidad"];
}
$LimiteEmpresas = $NumAlertas; //Indica cuantas empresas puedo seguir

//Actualizando al Cliente
$filter = ["email" => $Correo];

$estado = true; //Activar Notificacion
if($tipo == 2){
    $estado = false; //Desactivar Notificacion
}


if($position == -1){//Si la alerta no esta creada
    $document = [
        '$addToSet' => [
            "alertas" => [
                "id_empresa" => new MongoDB\BSON\ObjectID($idEmpresa),
                "estado" => $estado
            ]
        ]
    ];
}else{//Si la alerta esta desactivada
    $document = [
        '$set' => [
            "alertas.".$position.".estado" => $estado
        ]
    ];
}

$collection = $database->Clientes;
if($position == -1){//Si la alerta no esta creada
    $Result = $collection->findOne($filter);
    if(isset($Result["alertas"])){
        $Alertas = iterator_to_array($Result["alertas"]);
        
        /*//Verificara las alertas activas
        $contadorAlertasActivas = 0;
        foreach($Alertas as $alerta){
            if($alerta["estado"] == true){
                $contadorAlertasActivas++;
            }
        }*/

        if(count($Alertas) < $LimiteEmpresas){
            $Result = $collection->updateOne($filter, $document);
        }else{
            echo 2; //llego al limite de notificaciones
            exit();
        }
    }else{
        $Result = $collection->updateOne($filter, $document);
    }
}else{
    $Result = $collection->findOne($filter);
    if(isset($Result["alertas"][$position])){
        $Result = $collection->updateOne($filter, $document);
    }else{
        $Result = NULL;
    }
}


//Actualizando a la empresa
$filter = ["_id" => new MongoDB\BSON\ObjectID($idEmpresa) ];
$collection = $database->empresas;
$Posicion_Empresa = indexOfArray($Correo, "alertas.correo_cliente");

if($Posicion_Empresa === NULL){//Si la alerta no esta creada
    $document = [
        '$addToSet' => [
            "alertas" => [
                "correo_cliente" => $Correo,
                "estado" => $estado
            ]
        ]
    ];
}else{//Si la alerta esta desactivada
    $document = [
        '$set' => [
            "alertas.".$Posicion_Empresa.".estado" => $estado
        ]
    ];
}

if($Result != NULL){
    /*Si existe la alerta en cliente entonces la agregramos */
    $Result2 = $collection->updateOne($filter, $document);
}else{
    /*Si no existe la alerta en cliente quiere decir que la hemos eliminado por lo tanto
    no hay que crear una nueva*/
    $Result2 = NULL;
}


if($Result != NULL && $Result2 != NULL){
    if($Result->getModifiedCount() == 1 && $Result2->getModifiedCount() == 1){
        if(isset($_POST["desde"])){//Se esta consultado este archivo desde Cuenta/index.php
            echo 1;//Exito
        }else{//Se esta consultado este archivo desde PruebaConsulta.php
            echo VerificarSeguimiento();
        }
    }else{
        echo 0;//Algo Salio Mal
    }
}else{
    echo VerificarSeguimiento();
}


function VerificarSeguimiento(){
    global $database;
    global $idEmpresa;
    $Retorno = NULL;

    if(isset($_SESSION["Cliente"])){
        $collection = $database->Clientes;
        $filter = ["_id" => new MongoDB\BSON\ObjectID($_SESSION["Cliente"]["IDCliente"])];
        $Result2 = $collection->findOne($filter);

        $position = -1;
        $encontrado = false;
        if(isset($Result2["alertas"])){
            $alertas = $Result2["alertas"];
            foreach($alertas as $alerta){
                $position++;
                if($alerta["id_empresa"] == $idEmpresa){
                    if($alerta["estado"] == true){
                        $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('" . $idEmpresa . "','" . $_SESSION["Cliente"]["Correo"] . "','" . $position . "', 2);\">Desactivar Notificacion</button>";
                    }else{
                        $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('" . $idEmpresa . "','" . $_SESSION["Cliente"]["Correo"] . "','" . $position . "', 1);\">Activar Notificacion</button>";
                    }
                    $encontrado = true;
                    break;
                }
            }

            if($encontrado == false){
                $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('" . $idEmpresa . "','" . $_SESSION["Cliente"]["Correo"] . "','" . -1 . "', 1);\">Activar Notificacion</button>";
            }

        }else{
            $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('". $idEmpresa ."','". $_SESSION["Cliente"]["Correo"] ."','". $position ."', 1);\">Activar Notificacion</button>";
        }
    }
    
    return $Retorno;
}


function indexOfArray($Busqueda, $Ruta){
    //Busca el ??ndice donde se encuentra un array
    global $collection;
    global $filter;
    //ACTUALIZANDO LA FECHA DEL DIRECTIVO
    $ArrayBusqueda = [
        [ '$project' => 
            [ 'index' => 
                [ '$indexOfArray' => [ '$' . $Ruta, $Busqueda] ],
            ]
        ],
        [
            '$match' => $filter
        ]
    ];
    $Result2 = $collection->aggregate($ArrayBusqueda)->ToArray();
    return $Result2[0]['index'];
}
?>