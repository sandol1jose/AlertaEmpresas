<?php
//$tiempo_inicio = microtime(true);
set_time_limit(50000);
ini_set('memory_limit', '512M');
//ini_set('memory_limit', '256M');

$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');
require_once($root . '/Librerias/pdfparser-master/alt_autoload.php-dist');//Clase para pasar pdf a texto plano
require_once($root . '/app/Notificacion.php');
require_once($root . '/ArchivosExtraccionDB/UnirCambiosDeNombre.php');
require_once($root . '/ArchivosExtraccionDB/top5.php');
//include 'scraping.php';

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
$collection = $database->anuncios;

//$_GET["tipo"] = 1;
if(isset($_GET["tipo"])){
    //$_POST["fecha"] = '20090102';
    //$_POST["fecha"] = '20220407';
    $fechaActual = $_POST["fecha"];
    RecorrerXML($fechaActual);
    //UnirCambiosDeNombre(2); // /ArchivosExtraccionDB/UnirCambiosDeNombre.php
}else{
    $collection2 = $database->anuncios_dia;
    $Result = $collection2->deleteMany([]);

    //date_default_timezone_set("Europe/Madrid");
    date_default_timezone_set("America/Guatemala");

    $fecha_Millis = getdate();
    $fechaActual = NULL;
    $dia = date("w", $fecha_Millis[0]);

    date_default_timezone_set("UTC");
    if($dia != 0 && $dia != 6){//Verificando que no sea Sabado o Domingo
        $fechaActual = date("Ymd", $fecha_Millis[0] - 86400);
        RecorrerXML($fechaActual);
        Notificar(); // /app/Notificacion.php
        UnirCambiosDeNombre(1); // /ArchivosExtraccionDB/UnirCambiosDeNombre.php
        Top5Empresas(); // /ArchivosExtraccionDB/top5.php
        unset($fechaActual);
    }else{
        Top5Empresas(); //Borra las constituciones del dia sabado o domingo
        echo "es sabado o domingo";
    }
}

$contador = 1;
//$contadorGeneral = 0;
function RecorrerXML($fecha){
    global $borme;
    global $Provincia;
    global $contador;
    //global $contadorGeneral;
    global $ArrayInsertador;
    $xml = ("https://www.boe.es/diario_borme/xml.php?id=BORME-S-" . $fecha);

    $diario = NULL;
    try {
        //$contents = file_get_contents($xml);
        //Recorriendo xml
        $documento = simplexml_load_file($xml);
        $diario = $documento->diario->seccion[0]->emisor->item;
    } catch (Exception $e) {
        echo "El Archivo XML no tiene nada<br>";
        echo $xml . "<br>";
        echo "<span style='background-color: red;  color: white;'>" . $e->getMessage() . "</span><br><br>";
        unset($e); 
        //unset($xml); 
    }

    if($diario != NULL){
        foreach($diario as $dia){
            //echo strval($dia["id"]) . "<br>";
            //echo $dia->titulo . "<br>";
            //echo $dia->urlPdf . "<br><br>";
            try{
                if(strpos($dia->titulo, "ÍNDICE ALFABÉTICO DE SOCIEDADES") === false){
                    $Provincia = strval($dia->titulo);
                    $borme = strval($dia["id"]);
                    $URL = "https://www.boe.es" . $dia->urlPdf;
                    //echo $URL . " / ";
                    ConvertirATexto($URL, $fecha);
                    if($ArrayInsertador != NULL){
                        if(count($ArrayInsertador) > 0){
                            global $collection;
                            $Result1 = $collection->insertMany($ArrayInsertador);
                            if(!isset($_GET["tipo"])){
                                NuevosAnunciosPorDia($ArrayInsertador);
                            }
                            $ArrayInsertador = NULL;
                        }
                    }

                    /*$AnunciosScraping = Escrapear($borme);
                    if($contador == $AnunciosScraping){
                        echo "<span style='background-color: green; color: white;'>Son iguales</span><br>";
                    }else{
                        echo "<span style='background-color: red;  color: white;'>NO SON IGUALES</span><br>";
                    }
                    echo "AnunciosEscraping: " . $AnunciosScraping . "<br>";   
                    $contadorGeneral = $contadorGeneral +  $contador;*/
                    if(!isset($_GET["tipo"])){
                        echo $URL . "\n";
                    }else{
                        echo $URL . "<br>";
                        echo "Anuncios: " . $contador . "<br><br>"; 
                    }
                    unset($URL);
                    unset($dia);         
                }
            }catch(Exception $e){
                echo "Error al abrir el archivo PDF<br>";
                $URL = "https://www.boe.es" . $dia->urlPdf;
                echo $URL . "<br>";
                echo "<span style='background-color: red;  color: white;'>" . $e->getMessage() . "</span><br><br>";
                unset($e); 
                //unset($xml); 
                $ArrayInsertador = NULL;
            }
        }
        //echo $contadorGeneral;
        unset($diario); 
        unset($documento);
        
        /*for($i=0; $i<1; $i++){
            //$URL = "https://www.boe.es" . $diario[$i]->urlPdf;
            $borme = "BORME-A-2009-3-15.pdf";
            $URL = "https://boe.es/borme/dias/2009/01/07/pdfs/BORME-A-2009-3-28.pdf";
            echo $URL . " / "; 
            ConvertirATexto($URL, $fecha);
            unset($URL);
            unset($dia); 
        }*/

        global $database;
        $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($fecha . " 00:00:00")*1000);
        $documentInsert = [
            "archivo_actualizado" => $xml,
            "fecha" => $FechaMilis
        ];
        $collection3 = $database->control;
        $Result = $collection3->insertOne($documentInsert);


        echo "FIN DEL SUMARIO";
        echo "---------------------------------------------------<br><br><br><br>";
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
    global $contador;
    $contador = 1;
    $NumeroEntrada = 0;
    $lineas = explode("\n", $texto);
    $buscador = "Empresa";
    $NombreEmpresa = "";
    $Entrada = "";
    $Sucursal = ""; //Guarda algunas empresas que tienen sucursales

    //Verificando si existe la linea Verificable en https://www.boe.es
    $Inicio = 7;
    if(strpos($lineas[2], "https://www.boe.es") == true){
        $Inicio = 8;
    }

    //echo "<pre>";
    for($i=$Inicio; $i<count($lineas); $i++){

        /*if($NumeroEntrada == 34315){
            echo "si";
        }
        $LineaActual2 = $lineas[$i];*/
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
                    /*if($Penultimo != "." && strlen($LineaActual) > 85){//Si termina en un punto
                        if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si es mayuscula
                            $buscador = "Empresa";
                        }else{
                            $buscador = "Entrada";
                        }
                        //$buscador = "Empresa";
                    }else{*/
                    
                    if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si la siguiente linea es mayuscula
                        $buscador = "Empresa";
                    }else{
                        $buscador = "Entrada";
                    }

                    $retult1 = strpos($lineas[$i-1], $NumeroEntrada . " -");
                    $retult2 = strpos($lineas[$i-2], $NumeroEntrada . " -");
                    $retult3 = strpos($lineas[$i-3], $NumeroEntrada . " -");
                    if(!($retult1 === false) || !($retult1 === false) || !($retult1 === false)){
                        /* En casos como este donde son varias sucursales
                        53526 - BIOTECHVEG SA(R.M. SANTA CRUZ DE TENERIFE).
                        53526 - EN CANARIAS.
                        el -2 o -3 es en caso de que sean titulo de 2 o mas lineas
                        */
                        $Sucursal = $lineas[$i];
                    }else{
                        $NombreEmpresa = $lineas[$i];
                    }
                    //}
                }else{
                    //Es un titulo de 2 o mas lineas lineas
                    if(strtoupper($lineas[$i]) === $lineas[$i]){//Si es mayuscula todo
                        $LineaActual = $lineas[$i];
                        $tamañoLinea = strlen($LineaActual);
                        $Penultimo = $LineaActual[$tamañoLinea-1];
                        /*if($Penultimo == "." && strlen($LineaActual) > 85){//Si termina en un punto
                            $buscador = "Empresa";
                            $NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                        }else{*/
                            if(strtoupper($lineas[$i+1]) === $lineas[$i+1]){//Si es mayuscula
                                $buscador = "Empresa";
                                //$NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                            }else{
                                $buscador = "Entrada";
                                //$NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                            }
                        //}
                        $NombreEmpresa = $NombreEmpresa . " " . $lineas[$i];
                    }else{
                        $buscador = "Entrada";
                        $Entrada = $Entrada . " " . $lineas[$i];
                    }
                }
            }else if($buscador == "Entrada"){//Buscando Entrada
                $Entrada = $Entrada . " " . $lineas[$i];
                if(!(strpos($lineas[$i+1], $NumeroEntrada+1) === false)){
                    GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha, $Sucursal);
                    $Entrada = "";
                    $Sucursal = "";
                    $NumeroEntrada++;
                    $buscador = "Empresa";
                    $contador++;
                }else if($i+2 == count($lineas)){//Si es el ultimo registro
                    GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha, $Sucursal);
                    $Entrada = "";
                    $Sucursal = "";
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


function GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada, $fecha, $Sucursal){
    global $borme;
    global $Provincia;
    global $ArrayInsertador;

    $NombreEmpresa = str_replace($NumeroEntrada . " - ", "", $NombreEmpresa);
    $NombreEmpresa = preg_replace("/\s+/", " ", trim($NombreEmpresa)); //Quitando espacios de mas
    $NombreEmpresa = trim($NombreEmpresa, ".");

    if($Sucursal != ""){
        $Sucursal = str_replace($NumeroEntrada . " - ", "", $Sucursal);
        $Sucursal = preg_replace("/\s+/", " ", trim($Sucursal)); //Quitando espacios de mas
        $Sucursal = trim($Sucursal, ".");
    }

    //$Entrada = str_replace("\n", " ", $Entrada); //Quitando espacios de mas
    //vamos a crear un ID a partir del nombre
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
    //echo strval($FechaMilis) . "<br>";
    //var_dump($FechaMilis);
    //echo "La fecha guardada es: ". date("Y-m-d H:i:s", strval($FechaMilis)/1000) . "<br><br>";
    
    /*
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
                "provincia" => $Provincia,
                "anuncio" => $Entrada
            ]
        ]
    ];*/
    
    /*
    $document = 
    [
        "nombre_comercial" => $NombreEmpresa,
        "id_nombre_comercial" => $id_NombreEmpresa,
        "anuncio_borme" => [
            [
                "borme" => $borme,
                "numero" => $NumeroEntrada,
                "fecha" => $FechaMilis,
                "tipo" => $tipo,
                "anuncio" => $Entrada
            ]
        ]
    ];*/

    if($Sucursal == ""){
        $ArrayInsertador[] = 
        [
            "nombre_comercial" => $NombreEmpresa,
            "id_nombre_comercial" => $id_NombreEmpresa,
            "borme" => $borme,
            "numero" => $NumeroEntrada,
            "numid" => date("Y", strtotime($fecha)) . $NumeroEntrada,
            "fecha" => $FechaMilis,
            "tipo" => $tipo,
            "provincia" => $Provincia,
            "anuncio" => $Entrada
        ];
    }else{
        $ArrayInsertador[] = 
        [
            "nombre_comercial" => $NombreEmpresa,
            "id_nombre_comercial" => $id_NombreEmpresa,
            "sucursal" => $Sucursal,
            "borme" => $borme,
            "numero" => $NumeroEntrada,
            "numid" => date("Y", strtotime($fecha)) . $NumeroEntrada,
            "fecha" => $FechaMilis,
            "tipo" => $tipo,
            "provincia" => $Provincia,
            "anuncio" => $Entrada
        ];
    }


    $contadorArray = count($ArrayInsertador);

    if(count($ArrayInsertador) == 50){
        global $collection;
        $Result1 = $collection->insertMany($ArrayInsertador);
        if(!isset($_GET["tipo"])){
            NuevosAnunciosPorDia($ArrayInsertador);
        }
        $ArrayInsertador = NULL;
    }

    

    /*
    global $collection;
    $Result1 = $collection->findOneAndUpdate($filtro, $actualizar, ['upsert' => true]);*/
    
    /*
    if($Result1 == null){
        //$Result2 = $collection->insertOne($document);
        echo "Archivo Nuevo <br>";
    }else{
        echo "Archivo Actualizado / " . $NombreEmpresa . "<br>";
    }*/

    /*echo $NombreEmpresa;
    echo "<br>";
    echo $tipo;
    echo "<br>";
    echo $Entrada;
    echo "<br>";
    echo "Registro No. " . $NumeroEntrada . "<br><br>";*/
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