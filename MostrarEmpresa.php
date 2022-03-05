<?php
require 'Archivos de Ayuda PHP/conexion.php';

$Anuncio_Constitucion = [
    "Capital: ",
    "Comienzo de operaciones: ",
    "Domicilio: ",
    "Objeto social: "
];
$Anuncio_Nombramientos = [
    "Adm. Mancom.: ",
    "Adm. Unico: ",
    "Apo.Sol.: ",
    "Apoderado: ",
    "Auditor: ",
    "Cons.Del.Sol: ",
    "Consejero: ",
    "Liquidador: ",
    "Presidente: ",
    "SecreNoConsj: ",
    "Secretario: ",
    "Vicepresid.: ",
    "VsecrNoConsj: ",
    "VsecrNoConsj: ",
    "Liquidador M: "
];
$AumentoCapital = [
    "Suscrito",
    "Capital", 
    "Resultante Suscrito",
    "Desembolsado",
    "Resultante Desembolsado"
];
$ReduccionCapital = [
    "Importe reducción",
    "Resultante Suscrito"
];
$Anuncios = [
    "Ceses/Dimisiones. " => $Anuncio_Nombramientos,
    "Constitución. " => $Anuncio_Constitucion,
    "Datos registrales. " => NULL,
    "Declaración de unipersonalidad. " => ["Socio único: "],
    /*"Modificaciones estatutarias. " => NULL,*/
    "Nombramientos. " => $Anuncio_Nombramientos,
    "Reelecciones. " => $Anuncio_Nombramientos,
    "Sociedad unipersonal. " => ["Cambio de identidad del socio único: "],
    "Revocaciones" => $Anuncio_Nombramientos,
    "Modificaciones estatutarias. " => ["Artículo de los Estatutos"],
    "Ampliación de capital" => $AumentoCapital,
    "Reducción de capital" => $ReduccionCapital
    /*"Desembolso de dividendos pasivos" => NULL*/
];


//$id = $_GET["id"];
$id = "6211d15916dcda4b0a52e0c5";
$filter = [ "_id" => new MongoDB\BSON\ObjectID($id) ];

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas2;
$collection2 = $database->empresas2;
$Result = $collection->find($filter)->toArray();

foreach($Result as $res){
    $id = strval($res["_id"]);
    $nombre_comercial = strval($res["nombre_comercial"]);
    $anuncio_borme = $res["anuncio_borme"];

    echo "_id: " . $id . "<br>";
    echo "nombre_comercial: " . $nombre_comercial . "<br>";
    
    //var_dump($anuncio_borme);
    foreach($anuncio_borme as $anuncio_nivel2){
        DescomponerParrafo($anuncio_nivel2);
    }
    
    /*
    foreach($anuncio_borme as $anuncio_nivel2){
        echo "borme: " . strval($anuncio_nivel2["borme"]) . "<br>";
        echo "numero: " . strval($anuncio_nivel2["numero"]) . "<br>";
        $fechaFormateada = date("d-m-Y H:i:s", strval($anuncio_nivel2["fecha"])/1000);
        echo "fecha: " . strval($fechaFormateada) . "<br>";
        echo "tipo: " . strval($anuncio_nivel2["tipo"]) . "<br>";
        echo "anuncio_nivel2: " . strval($anuncio_nivel2["anuncio_nivel2"]) . "<br><br><br>";
    }*/
}


$ArrayNivel2;
$ArrayPorAnuncio;
$numero_borme;
$fecha_borme;
function DescomponerParrafo($ParrafoAnuncio){
    global $ArrayNivel2;
    global $numero_borme;
    global $fecha_borme;
    global $Anuncios; //Variable que guarda las palabras clave Nivel 1
    $anuncio_borme = $ParrafoAnuncio["anuncio"];
    $numero_borme = $ParrafoAnuncio["numero"];
    $fecha_borme = $ParrafoAnuncio["fecha"];//Fecha en milisegundos
    $cadena = NULL;
    $cadena2 = NULL;
    $cadena3 = NULL;

//Buscando en cadena Nivel 1
for($i=0; $i<strlen($anuncio_borme); $i++){
    $cadena = $cadena . $anuncio_borme[$i];
    if(strlen($cadena) >= 10){
    foreach($Anuncios as $Arranuncio=>$valoranuncio){
        if(!(strpos($cadena, $Arranuncio) === false)){
            CrearArray("ANUNCIO_PRINCIPAL", $Arranuncio);
            $ArrayNivel2 = NULL;
            $cadena = "";
            
            //Buscando en cadena Nivel 2
            $Anuncios_Nivel2 = $valoranuncio;
            for($j=$i+1; $j<strlen($anuncio_borme); $j++){
                $cadena2 = $cadena2 . $anuncio_borme[$j];
                if($j+1 < strlen($anuncio_borme)){//Verificando el ultimo registro
                    //if($Anuncios_Nivel2 != NULL){
                        foreach($Anuncios_Nivel2 as $anuncio_nivel2){
                            if(!(strpos($cadena2, $anuncio_nivel2) === false)){//Coincidio Nivel 2
                                $Anuncio2_Actual = $anuncio_nivel2;
                                $cadena2 = str_replace($anuncio_nivel2, "", $cadena2);
                                //$cadena3 = str_replace($anuncio_nivel2, "", $cadena3);
                                $encontrado = 0;
                                for($k=$j+1; $k<strlen($anuncio_borme); $k++){
                                    $cadena3 = $cadena3 . $anuncio_borme[$k];
                                    
                                    foreach($Anuncios_Nivel2 as $anuncio_nivel2){//Buscando el siguiente Nivel 2
                                        if(!(strpos($cadena3, $anuncio_nivel2) === false)){
                                            $cadena3 = str_replace($anuncio_nivel2, "", $cadena3);
                                            CrearArray($Anuncio2_Actual, $cadena3);
                                            $cadena2 = $anuncio_nivel2;
                                            $cadena3 = "";
                                            $j = $k;
                                            $encontrado = 1;
                                            break;
                                        }else{
                                            $encontrado = 0;
                                        }
                                    }

                                    if($encontrado == 0){
                                        foreach($Anuncios as $Arranuncio=>$valoranuncio){ //Buscamos si hay un Nivel 1
                                            if(!(strpos($cadena3, $Arranuncio) === false)){
                                                $cadena3 = str_replace($Arranuncio, "", $cadena3);
                                                CrearArray($Anuncio2_Actual, $cadena3);
                                                CrearArrayGlobalAnuncio();
                                                $cadena = $Arranuncio;
                                                $cadena3 = "";
                                                $i = $k;
                                                $j = strlen($anuncio_borme);
                                                $encontrado = 2;
                                                break;
                                            }
                                        }
                                    }else{
                                        break;
                                    }

                                    if($encontrado == 2){
                                        break;
                                    }
                                }
                            }
                        }
                    //}
                }else{//El ultimo registro
                    $ArrayNivel2 = NULL;
                    CrearArray("ANUNCIO_PRINCIPAL", $Arranuncio);
                    CrearArray($Arranuncio, $cadena2);
                    CrearArrayGlobalAnuncio();
                    $cadena = "";
                    $i = $j;
                }
            }

        }
    }
    }
}
ImprimirArrayGlobal();
}


//Array que guarda los registros encontrados de [Nivel 1 => Nivel 2 => Valor]
function CrearArray($Clave, $Valor){
    global $ArrayNivel2;
    $Valor = trim($Valor, ' ');
    $Valor = trim($Valor, '.');
    $Clave = trim($Clave, ' ');
    $Clave = trim($Clave, ':');
    if($Clave == "Comienzo de operaciones: "){
        $Valor = str_replace(".", "/", $Valor);
    }
    $ArrayNivel2[$Clave] = $Valor;
}


//Array global que guarda todos los datos de Nivel1 => Nivel2 => Dato
function CrearArrayGlobalAnuncio(){
    global $ArrayPorAnuncio;
    global $ArrayNivel2;
    $Tipo = eliminar_acentos($ArrayNivel2["ANUNCIO_PRINCIPAL"]);
    $ArrayPorAnuncio[$Tipo] = $ArrayNivel2;
    $ArrayNivel2 = NULL;
}



function ImprimirArrayGlobal(){
    /*global $ArrayPorAnuncio;
    $DatosRegistrales = $ArrayPorAnuncio["Datos registrales"];
    $DatosRegistrales2 = $DatosRegistrales["Datos registrales."];
    
    foreach($ArrayPorAnuncio as $arr){
        $Tipo = $arr["ANUNCIO_PRINCIPAL"];
        switch($Tipo){
            case "Constitución":
                Constitucion($DatosRegistrales2, $arr);
                break;
            case "Nombramientos":
                Nombramientos($DatosRegistrales2, $arr, "Nombramientos", "entidad", "tipo_directivo");
                break;
            case "Reelecciones":
                Nombramientos($DatosRegistrales2, $arr, "Reelecciones", "entidad", "tipo_directivo");
                break;
            case "Ampliación de capital":
                Nombramientos($DatosRegistrales2, $arr, "Ampliacion_de_capital", "monto", "tipo");
                break;
            default:
                break;
        }
    }*/
}


function Constitucion($DatosRegistrales, $arr){
   
    foreach($arr as $clave=>$valor){
        $Palabra = $clave;
        $Cadena = $valor;
    
        echo $Palabra . ": " . $Cadena . "<br><br>";
    }
    echo $DatosRegistrales . "<br>";
    echo "-------------------------------------------------------------<br><br>";
    
    unset($arr["ANUNCIO_PRINCIPAL"]);//Eliminando ANUNCIO_PRINCIPAL
    $document = ['$set' => $arr];
    global $filter;
    global $collection2;
    
    $Result = $collection2->updateOne($filter, $document);
    var_dump($Result);
}

function Nombramientos($DatosRegistrales, $arr, $Nombre, $clave1, $clave2){
    global $numero_borme;
    global $fecha_borme;
    global $filter;
    global $collection2;
    unset($arr["ANUNCIO_PRINCIPAL"]);//Eliminando ANUNCIO_PRINCIPAL
    
    //Sustrayendo solo la fecha
    $FechaInscripcion = substr($DatosRegistrales, strlen($DatosRegistrales)-9, strlen($DatosRegistrales));
    $FechaInscripcion = trim($FechaInscripcion, ')');
    $FechaInscripcion = str_ireplace(".", "/", $FechaInscripcion);
    $FechaInscripcion = str_ireplace(" ", "0", $FechaInscripcion);
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($FechaInscripcion . " 00:00:00")*1000);

    //Sustrayendo solo los datos registrales
    $DatosRegistrales = substr($DatosRegistrales, 0, strlen($DatosRegistrales)-10);
    $DatosRegistrales = trim($DatosRegistrales, " ");

    $arraysEntidades = [];
    foreach($arr as $clave=>$valor){
        $Palabra = $clave;
        $Cadena = $valor;
        $Lista = explode(";", $Cadena);

        foreach($Lista as $lis){
            $lis = trim($lis, ":");
            $lis = trim($lis, " ");
            $arrayEntidad = [
                $clave1 => $lis,
                $clave2 => $Palabra
            ];
            array_push($arraysEntidades, $arrayEntidad);
            
        }
    }
    echo "-------------------------------------------------------------<br><br>";

    $Array_Nombramientos = [
        "numero_borme" => $numero_borme,
        "fecha_borme" => $fecha_borme,
        "datos_registrales" => $DatosRegistrales,
        "fecha_incripcion" => $FechaMilis,
        "entidades" => $arraysEntidades
    ];

    var_dump($Array_Nombramientos);

    
    $document2 = ['$addToSet' => [ $Nombre => $Array_Nombramientos]];
    $Result = $collection2->updateOne($filter, $document2);
    var_dump($Result);
}


function eliminar_acentos($cadena){
		
    //Reemplazamos la A y a
    $cadena = str_replace(
    array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
    array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
    $cadena
    );

    //Reemplazamos la E y e
    $cadena = str_replace(
    array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
    array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
    $cadena );

    //Reemplazamos la I y i
    $cadena = str_replace(
    array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
    array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
    $cadena );

    //Reemplazamos la O y o
    $cadena = str_replace(
    array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
    array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
    $cadena );

    //Reemplazamos la U y u
    $cadena = str_replace(
    array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
    array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
    $cadena );

    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
    array('Ñ', 'ñ', 'Ç', 'ç'),
    array('N', 'n', 'C', 'c'),
    $cadena
    );
    
    return $cadena;
}



?>