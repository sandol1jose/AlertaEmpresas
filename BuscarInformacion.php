<?php
$tiempo_inicio = microtime(true);
set_time_limit(50000);
//ini_set('memory_limit', '512M');
ini_set('memory_limit', '256M');

include "Librerias/pdfparser-master/alt_autoload.php-dist"; //Clase para pasar pdf a texto plano

//date_default_timezone_set("Europe/Madrid");

//Codigo para que ignore la alerta cuando no existe un archivo
set_error_handler("warning_handler", E_WARNING);
function warning_handler($errno, $errstr) { 
    throw new ErrorException($errstr, 0, $errno);
}

/*$desde = $_POST["desde"];
$hasta = $_POST["hasta"];

$desde = '2022/02/17';
$hasta = '2022/02/17';*/

$NumeroEntrada = 0;//Contador de los registros en el BORME 1 - PROYECTOS 
$borme = NULL; //borme actual;

//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
//require 'Archivos de Ayuda PHP/conexion.php';
$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;

$fechaActual = $_POST["fecha"];
RecorrerXML($fechaActual);

//Recorrer fechas
/*$fechaInicio=strtotime($desde);
$fechaFin=strtotime($hasta);
unset($desde);
unset($hasta);
for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
    $dia = date("w", $i);
    if($dia != 0 && $dia != 6){//Verificando que no sea Sabado o Domingo
        $fechaActual = date("Ymd", $i);
        RecorrerXML($fechaActual);
        unset($fechaActual);
    }
}*/



function RecorrerXML($fecha){
    global $borme;
    $xml = ("https://www.boe.es/diario_borme/xml.php?id=BORME-S-" . $fecha);
  
    try {
        
        //$contents = file_get_contents($xml);
        
        //Recorriendo xml
        $documento = simplexml_load_file($xml);
        $diario = $documento->diario->seccion[0]->emisor->item;
        
        foreach($diario as $dia){
            //echo strval($dia["id"]) . "<br>";
            //echo $dia->titulo . "<br>";
            //echo $dia->urlPdf . "<br><br>";
            if(strpos($dia->titulo, "ÍNDICE ALFABÉTICO DE SOCIEDADES") === false){
                $borme = strval($dia["id"]);
                $URL = "https://www.boe.es" . $dia->urlPdf;
                echo $URL . " / ";
                ConvertirATexto($URL, $fecha);    
                unset($URL);
                unset($dia);         
            }
        }
        unset($diario); 
        unset($documento);
        /*
        for($i=0; $i<1; $i++){
            //$URL = "https://www.boe.es" . $diario[$i]->urlPdf;
            $borme = "BORME-A-2022-33-02.pdf";
            $URL = "https://www.boe.es/borme/dias/2022/02/11/pdfs/BORME-A-2022-29-38.pdf";
            echo $URL . " / "; 
            ConvertirATexto($URL, $fecha);
            unset($URL);
            unset($dia); 
        }*/

        global $database;
        //$filtro = [ "_id" => new MongoDB\BSON\ObjectID("62240d2cc69d280981437b15") ];
        $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($fecha . " 00:00:00")*1000);
        $documentInsert = [
            "archivo_actualizado" => $xml,
            "fecha" => $FechaMilis
        ];
        $collection2 = $database->control;
        $Result = $collection2->insertOne($documentInsert);


        echo "FIN DEL SUMARIO";
        echo "---------------------------------------------------<br><br><br><br>";
        unset($xml);
    } catch (Exception $e) {
        echo "Este Archivo no tiene nada<br>";
        echo $e->getMessage();
        echo $xml . "<br><br>";
        unset($e); 
        unset($xml); 
    }
}

restore_error_handler();//Restaura el mensaje de alerta


function ConvertirATexto($URL, $fecha){
    $parseador = new \Smalot\PdfParser\Parser();
    $nombreDocumento = $URL;
    $documento = $parseador->parseFile($nombreDocumento);
    $texto = $documento->getText();
    //echo "<pre>" . $texto . "</pre>";
    RecorrerTexto($texto, $fecha);
    //separartexto("<pre>" . $texto . "</pre>");
    unset($texto);
    unset($documento);
    unset($parseador);
    unset($nombreDocumento);
    //GuardarRegistro("", "", 0, $fecha);
}



function RecorrerTexto($texto, $fecha){
    global $NumeroEntrada;
    $NumeroEntrada = 0;
    $lineas = explode("\n", $texto);
    $buscador = "Empresa";
    $NombreEmpresa = "";
    $Entrada = "";
    echo "<pre>";
    for($i=8; $i<count($lineas); $i++){
        
        if($i==8){
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
        
        }else{
            if($buscador == "Empresa"){//Buscando nombre de empresa
                if(!(strpos($lineas[$i], $NumeroEntrada) === false)){
                    $LineaActual = $lineas[$i];
                    $tamañoLinea = strlen($LineaActual);
                    $Penultimo = $LineaActual[$tamañoLinea-1];
                    if($Penultimo != "." && strlen($LineaActual) > 85){//Si termina en un punto
                        /*if(strlen($LineaActual) > 85){//un titulo de 2 lineas
                            
                        }else{
                            $buscador = "Entrada";
                        }*/
                        $buscador = "Empresa";
                    }else{
                        if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si es mayuscula
                            $buscador = "Empresa";
                        }else{
                            $buscador = "Entrada";
                        }
                    }
                    $NombreEmpresa = $lineas[$i];
                }else{
                    //Es un titulo de 2 o mas lineas lineas
                    if(strtoupper($lineas[$i]) === $lineas[$i]){//Si es mayuscula todo
                        $LineaActual = $lineas[$i];
                        $tamañoLinea = strlen($LineaActual);
                        $Penultimo = $LineaActual[$tamañoLinea-1];
                        if($Penultimo == "." && strlen($LineaActual) > 85){//Si termina en un punto
                            $buscador = "Empresa";
                            $NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                        }else{
                            if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si es mayuscula
                                $buscador = "Empresa";
                                $NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                            }else{
                                $buscador = "Entrada";
                                $NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                            }
                        }
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

    echo "</pre>";
    unset($NumeroEntrada);
    unset($lineas);
    unset($texto);
    unset($buscador);
    unset($i);
}
//echo "Fase 1, el uso de memoria es de: ", round(memory_get_usage()/1024, 2), "KB";

function GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha){
    global $borme;
    $NombreEmpresa = str_replace($NumeroEntrada . " - ", "", $NombreEmpresa);
    $NombreEmpresa = preg_replace("/\s+/", " ", trim($NombreEmpresa)); //Quitando espacios de mas
    //$Entrada = str_replace("\n", " ", $Entrada); //Quitando espacios de mas
    //vamos a crear un ID a partir del nombre
    $id_NombreEmpresa = str_replace(",", "", $NombreEmpresa);
    $id_NombreEmpresa = str_replace(".", "", $id_NombreEmpresa);
    $id_NombreEmpresa = str_replace(" ", "", $id_NombreEmpresa);
    
    $Entrada = preg_replace("/\s+/", " ", trim($Entrada)); //Quitando espacios de mas

    
    
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
    //echo strval($FechaMilis) . "<br>";
    //var_dump($FechaMilis);
    //echo "La fecha guardada es: ". date("Y-m-d H:i:s", strval($FechaMilis)/1000) . "<br><br>";
    
    
    $filtro = ["id_nombre_comercial" => $id_NombreEmpresa];


    $actualizar = 
    [
        '$set' => ["nombre_comercial" => $NombreEmpresa],
        
        '$addToSet' => 
        [ "anuncio_borme" =>
            [
                "borme" => $borme,
                "numero" => $NumeroEntrada,
                "fecha" => $FechaMilis,
                "tipo" => $tipo,
                "anuncio" => $Entrada
            ]
        ]
    ];
    
    
    $document = 
    [
        "nombre_comercial" => $NombreEmpresa,
        "anuncio_borme" => [
            [
                "borme" => $borme,
                "numero" => $NumeroEntrada,
                "fecha" => $FechaMilis,
                "tipo" => $tipo,
                "anuncio" => $Entrada
            ]
        ]
    ];

    
    global $collection;
    $Result1 = $collection->findOneAndUpdate($filtro, $actualizar, ['upsert' => true]);
    
    if($Result1 == null){
        //$Result2 = $collection->insertOne($document);
        echo "Archivo Nuevo <br>";
    }else{
        echo "Archivo Actualizado / " . $NombreEmpresa . "<br>";
    }

    echo $NombreEmpresa;
    echo "<br>";
    echo $tipo;
    echo "<br>";
    echo $Entrada;
    echo "<br>";
    echo "Registro No. " . $NumeroEntrada . "<br><br>";
}



/*
function separartexto($texto){
    $cadena1 = "";//Guarda la cadena principal
    $cadena2 = "";//Guarda la cadena auxiliar que no se va a guardar
    $Encontrado = -1;//Se encontro los encabezados que se quieren eliminar
    $EsPrimera = 1;//Verifica si es primera vez que aparece el encabezado "BOLETIN OFICIAL..."
    $saltos = 0;//Lleva control de los saltos de linea que se van a omitir
    global $NumeroEntrada;
    $Empresa = ""; //Nombre de empresa;
    $Entrada = ""; //Texto de la empresa;
    $RolDeBusqueda = "Empresa"; //Guarda si estamos buscando nombre de Empresa o Entrada
    //echo ;//
    for($i=0; $i<strlen($texto); $i++){
        if(strpos($cadena1, $NumeroEntrada . " - ") == false){
            //Bloque para eliminar el primer encabezado
            if(strpos($cadena1, "BOLETÍN") == true){
                $cadena2 = $cadena1;
                $cadena1 = str_replace("BOLETÍN", "", $cadena1);
                for($t=$i; $t<strlen($texto); $t++){
                    if(strpos($cadena2, "https://www.boe.es") == true){
                        if($saltos > 5 && $EsPrimera == 1){//Elimina las siguientes 6 lineas...
                            
                            //Sacando el NUMERO DE ENTRADA
                            $cadenaNumEntrada = "";
                            for($r=$t; $r<strlen($texto); $r++){
                                if(strpos($cadenaNumEntrada, " -") == true){
                                    $cadenaNumEntrada = str_replace(" -", "", $cadenaNumEntrada);
                                    $NumeroEntrada = intval($cadenaNumEntrada);
                                    $cadenaNumEntrada = "";
                                    break;
                                }
                                $cadenaNumEntrada = $cadenaNumEntrada . $texto[$r];
                            }
                            $EsPrimera = 0;
                            $cadena2 = "";
                            $i=$t;
                            break;
                        }else{
                            if($texto[$t] == "\n"){
                                $saltos++;
                            }
                        }
                    }
                    $cadena2 = $cadena2 . $texto[$t];
                }              
            }
            $cadena1 = $cadena1 . $texto[$i];

        }else{
            for($j=$i; $j<strlen($texto); $j++){
                
                //Si encuentra un nuevo registro
                if($texto[$j] != "\n"){
                    if($RolDeBusqueda == "Empresa"){
                        $Empresa = $cadena1;
                    }else if($RolDeBusqueda == "Entrada"){
                        $Entrada = $cadena1;
                    }
                }else{
                    if($RolDeBusqueda == "Empresa"){
                        $cadena1 = "";
                        $RolDeBusqueda = "Entrada";
                        $Verificador = "";
                    }else if($RolDeBusqueda == "Entrada"){
                        
                        for($x = $j; $x<=($j+strlen($NumeroEntrada)); $x++){
                            $Verificador = $Verificador . $texto[$x];
                        }
                        
                        if(strpos($Verificador, "BOLETÍN") == true){//Viendo si es una nueva página
                            $cadena3 = $cadena1;
                            $saltos2 = 0;
                            for($s=$j+1; $s<strlen($texto); $s++){
                                if(strpos($cadena3, "https://www.boe.es") == true){
                                    if($saltos2 > 0){//Elimina las siguientes 6 lineas...
                                        $cadena3 = "";
                                        $j=$s;
                                        break;
                                    }else{
                                        if($texto[$s] == "\n"){
                                            $saltos2++;
                                        }
                                    }
                                }else if(strpos($cadena3, "</pre") == true){//Para la ultima linea
                                    GuardarRegistro($Empresa, $Entrada, $NumeroEntrada);
                                    echo "------------------------------------------<br><br>";
                                    $cadena3 = "";
                                    $Empresa = "";
                                    $Entrada = "";
                                    $NumeroEntrada = 0;
                                    $j=$s;
                                    $i=$s;
                                    break;
                                }
                                $cadena3 = $cadena3 . $texto[$s];
                            }
                            $Verificador = "";
                        }

                        if(strpos($Verificador, $NumeroEntrada+1) == true){
                            $cadena1 = "";
                            $RolDeBusqueda = "Empresa";
                            GuardarRegistro($Empresa, $Entrada, $NumeroEntrada);
                            echo "------------------------------------------<br><br>";
                            $Empresa = "";
                            $Entrada = "";
                            $NumeroEntrada++;
                            $i=$j-1;
                            break;
                        }
                        $Verificador = "";
                    }
                }
                $cadena1 = $cadena1 . $texto[$j];

            }           
        }
    }
}*/


/*
$tiempo_fin = microtime(true);
$tiempo = $tiempo_fin - $tiempo_inicio;
echo "\n\n";
echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);*/


?>