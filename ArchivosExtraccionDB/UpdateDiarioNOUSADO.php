<?php
//Archivo que se ejecutará diariamente para comprobar y recopilar las actualizaciones diarias en el borme

ini_set('memory_limit', '256M');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');
require_once($root . '/Librerias/pdfparser-master/alt_autoload.php-dist');//Clase para pasar pdf a texto plano
require_once($root . '/app/Notificacion.php');
require_once($root . '/ArchivosExtraccionDB/UnirCambiosDeNombre.php');
//include "../Librerias/pdfparser-master/alt_autoload.php-dist"; //Clase para pasar pdf a texto plano
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
//require_once('../Archivos de Ayuda PHP/conexion.php');

//Codigo para que ignore la alerta cuando no existe un archivo
set_error_handler("warning_handler", E_WARNING);
function warning_handler($errno, $errstr) { 
    throw new ErrorException($errstr, 0, $errno);
}

$NumeroEntrada = 0;//Contador de los registros en el BORME 1 - PROYECTOS 
$Provincia = NULL; //Guarda la provincia donde se realizo el registro
$borme = NULL; //borme actual;
$ArrayInsertador = NULL; //Almacenará 20 registros para almacenar de 20 en 20

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->anuncios_dia;

//$collection2 = $database->NuevasEmpresasDia;
$collection2 = $database->anuncios2;
//$Result = $collection->deleteMany([]);

$fecha_Millis = getdate();
$fechaActual = NULL;
$dia = date("w", $fecha_Millis[0]);
if($dia != 0 && $dia != 6){//Verificando que no sea Sabado o Domingo
    $fechaActual = date("Ymd", $fecha_Millis[0]);
    RecorrerXML($fechaActual);
    Notificar(); // /app/Notificacion.php
    UnirCambiosDeNombre(1); // /ArchivosExtraccionDB/UnirCambiosDeNombre.php
    unset($fechaActual);
}

function RecorrerXML($fecha){
    global $borme;
    global $Provincia;
    global $ArrayInsertador;
    $xml = ("https://www.boe.es/diario_borme/xml.php?id=BORME-S-" . $fecha);
    
    $diario = NULL;

    try{
        //Recorriendo xml
        $documento = simplexml_load_file($xml);
        $diario = $documento->diario->seccion[0]->emisor->item;
    }catch(Exception $e){
        echo "El Archivo XML no tiene nada<br>";
        echo $xml . "<br>";
        echo "<span style='background-color: red;  color: white;'>" . $e->getMessage() . "</span><br><br>";
        unset($e);
    }


    if($diario != NULL){
        foreach($diario as $dia){
            try{
                if(strpos($dia->titulo, "ÍNDICE ALFABÉTICO DE SOCIEDADES") === false){
                    $Provincia = strval($dia->titulo);
                    $borme = strval($dia["id"]);
                    $URL = "https://www.boe.es" . $dia->urlPdf;
                    ConvertirATexto($URL, $fecha);
                    if($ArrayInsertador != NULL){
                        if(count($ArrayInsertador) > 0){
                            global $collection;
                            $Result1 = $collection->insertMany($ArrayInsertador);
                            NuevosAnunciosPorDia($ArrayInsertador);
                            $ArrayInsertador = NULL;
                        }
                    }

                    echo $URL . "\n";
                    unset($URL);
                    unset($dia);         
                }
            }catch(Exception $e){
                echo "Error al abrir el archivo PDF<br>";
                $URL = "https://www.boe.es" . $dia->urlPdf;
                echo $URL . "<br>";
                echo "<span style='background-color: red;  color: white;'>" . $e->getMessage() . "</span><br><br>";
                unset($e);
                unset($xml);
                $ArrayInsertador = NULL;
            }

        }


        unset($diario); 
        unset($documento);

        /*global $database;
        $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($fecha . " 00:00:00")*1000);
        $documentInsert = [
            "archivo_actualizado" => $xml,
            "fecha" => $FechaMilis
        ];
        $collection2 = $database->control;
        $Result = $collection2->insertOne($documentInsert);*/


        /*echo "FIN DEL SUMARIO";
        echo "---------------------------------------------------\n\n\n\n";*/
        unset($xml);
    }
}

restore_error_handler();//Restaura el mensaje de alerta

function ConvertirATexto($URL, $fecha){
    $parseador = new \Smalot\PdfParser\Parser();
    $nombreDocumento = $URL;
    $documento = $parseador->parseFile($nombreDocumento);
    $texto = $documento->getText();

    RecorrerTexto($texto, $fecha);

    //Elimiando variables
    unset($texto);
    unset($documento);
    unset($parseador);
    unset($nombreDocumento);
}

function RecorrerTexto($texto, $fecha){
    global $NumeroEntrada;
    $NumeroEntrada = 0;
    $lineas = explode("\n", $texto);
    $buscador = "Empresa";
    $NombreEmpresa = "";
    $Entrada = "";

    //Verificando si existe la linea Verificable en https://www.boe.es
    $Inicio = 7;
    if(strpos($lineas[2], "https://www.boe.es") == true){
        $Inicio = 8;
    }
    //echo "<pre>";
    for($i=$Inicio; $i<count($lineas); $i++){
        //$Linea = $lineas[$i];
        if($i==$Inicio){
            //Sacando el NUMERO DE ENTRADA
            $cadenaNumEntrada = "";
            $textoLinea = $lineas[$i];
            for($r=0; $r<strlen($textoLinea); $r++){
                if(strpos($cadenaNumEntrada, " -") == true){
                    $cadenaNumEntrada = str_replace(" -", "", $cadenaNumEntrada);
                    $NumeroEntrada = intval($cadenaNumEntrada);
                    unset($cadenaNumEntrada);
                    unset($r);
                    break;
                }
                $cadenaNumEntrada = $cadenaNumEntrada . $textoLinea[$r];
            }
            unset($textoLinea);
        }


        if(strpos($lineas[$i], "OFICIAL DEL REGISTRO MERCANTIL") == true){    
            $i++;
        }else if(strpos($lineas[$i], "https://www.boe.es") == true){

        }else if(strpos($lineas[$i], "\n") == true){
            
        }else if($lineas[$i] == ""){

        }else{
            if($buscador == "Empresa"){//Buscando nombre de empresa
                if(!(strpos($lineas[$i], $NumeroEntrada) === false)){
                    $LineaActual = $lineas[$i];
                    $tamañoLinea = strlen($LineaActual);
                    $Penultimo = $LineaActual[$tamañoLinea-1];
                    if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si la siguiente linea es mayuscula
                        $buscador = "Empresa";
                    }else{
                        $buscador = "Entrada";
                    }
                    $NombreEmpresa = $lineas[$i];
                }else{
                    //Es un titulo de 2 o mas lineas lineas
                    if(strtoupper($lineas[$i]) === $lineas[$i]){//Si es mayuscula todo
                        $LineaActual = $lineas[$i];
                        $tamañoLinea = strlen($LineaActual);
                        $Penultimo = $LineaActual[$tamañoLinea-1];
                        if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si es mayuscula
                            $buscador = "Empresa";
                        }else{
                            $buscador = "Entrada";
                        }
                        $NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                    }else{
                        $buscador = "Entrada";
                        $Entrada = $Entrada . " " . $lineas[$i];
                    }
                }
            }else if($buscador == "Entrada"){//Buscando Entrada
                $Entrada = $Entrada . " " . $lineas[$i];
                if(!(strpos($lineas[$i+1], $NumeroEntrada+1) === false)){
                    GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha);
                    $Entrada = "";
                    $NumeroEntrada++;
                    $buscador = "Empresa";
                }else if($i+2 == count($lineas)){//Si es el ultimo registro
                    GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha);
                    $Entrada = "";
                    $NumeroEntrada = 0;
                    $buscador = "Empresa";
                }
            }
        }
    }

    //echo "</pre>";
    unset($NumeroEntrada);
    unset($lineas);
    unset($texto);
    unset($buscador);
    unset($i);
}
//echo "Fase 1, el uso de memoria es de: ", round(memory_get_usage()/1024, 2), "KB";

function GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha){
    global $borme;
    global $Provincia;
    global $ArrayInsertador;

    $NombreEmpresa = str_replace($NumeroEntrada . " - ", "", $NombreEmpresa);
    $NombreEmpresa = preg_replace("/\s+/", " ", trim($NombreEmpresa)); //Quitando espacios de mas
    $NombreEmpresa = trim($NombreEmpresa, ".");

    $id_NombreEmpresa = str_replace(",", "", $NombreEmpresa);
    $id_NombreEmpresa = str_replace(".", "", $id_NombreEmpresa);
    $id_NombreEmpresa = str_replace(" ", "", $id_NombreEmpresa);
    
    $Entrada = preg_replace("/\s+/", " ", trim($Entrada)); //Quitando espacios de mas
    //$Provincia = preg_replace("/\s+/", " ", trim($Provincia)); //Quitando espacios de mas
    
    $tipo = "";
    for($i=0; $i<strlen($Entrada); $i++){
        if($Entrada[$i] == "." || $Entrada[$i] == ":"){
            if($Entrada[$i+1] == " " || $Entrada[$i+1] == " "){
                break;
            }
        }
        $tipo = $tipo . $Entrada[$i];
    }

    //hay que guardar la fecha en milisegundos
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($fecha . " 00:00:00")*1000);
    
    $ArrayInsertador[] = 
    [
        "nombre_comercial" => $NombreEmpresa,
        "id_nombre_comercial" => $id_NombreEmpresa,
        "borme" => $borme,
        "numero" => $NumeroEntrada,
        "numid" => date("Y", strtotime($fecha)) . $NumeroEntrada,
        "fecha" => $FechaMilis,
        "tipo" => $tipo,
        "anuncio" => $Entrada
    ];

    $contadorArray = count($ArrayInsertador);

    if(count($ArrayInsertador) == 30){
        global $collection;
        $Result1 = $collection->insertMany($ArrayInsertador);
        NuevosAnunciosPorDia($ArrayInsertador);
        $ArrayInsertador = NULL;
    }
    
    /*if($Result1 == null){
        echo "Archivo Nuevo \n";
    }else{
        echo "Archivo Actualizado / " . $NombreEmpresa . "\n";
    }

    echo $NombreEmpresa;
    echo "\n";
    echo $tipo;
    echo "\n";
    echo $Entrada;
    echo "\n";
    echo "Registro No. " . $NumeroEntrada . "\n\n";*/
}


function NuevosAnunciosPorDia($ArrayInsertador){
    /*Guarda en una colllection nueva, las empresas que se agregaron el dia de hoy*/
        //Agregando el registro de nueva empresa agregadas en el dia
        global $collection2;
        //if($Result1 != null){
            //if(isset($Result1["alertas"])){
                /*
                    $filtro = ["id_nombre_comercial" => $id_NombreEmpresa];
                    $Result1 = $collection->findOne($filtro);
                    $id_Empresa = $Result1["_id"];

                    $filtro = ["_id" => new MongoDB\BSON\ObjectID($id_Empresa)];

                    $actualizar = 
                    [
                        '$set' => [
                            "nombre_empresa" => $NombreEmpresa,
                            "id_empresa" => new MongoDB\BSON\ObjectID($id_Empresa),
                        ],
                        
                        '$addToSet' => 
                        [ "bormes_agregados" => $NumeroEntrada]
                    ];
                    $Result = $collection2->findOneAndUpdate($filtro, $actualizar, ['upsert' => true]);
                */
                $Result = $collection2->insertMany($ArrayInsertador);
            //}
        //}
}

?>