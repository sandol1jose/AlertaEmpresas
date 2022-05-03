<?php
    session_start();//inicio de sesion
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');

$Anuncio_Constitucion = [
    "Capital: ",
    "Comienzo de operaciones: ",
    "Domicilio: ",
    "Objeto social: ",
    "Duración: "
];
$Anuncio_Nombramientos = [
    "Adm. Mancom.: ",
    "Adm. Unico: ",
    "ADM.UNICO: ",
    "APODERAD.SOL: ",
    "Apo.Sol.: ",
    "Adm. Solid.: ",
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
    "Liquidador M: ",
    "R.L.C.Perma.: ",
    "Apo.Man.Soli: ",
    "Apo.Manc.: ",
    "ACCIONISTA UNICO",
    "CONS. DELEG.: ",
    "Represent.143 RRM.: ",
    "Co.De.Ma.So: ",
    "Pre.No.Ejec.: ",
    "ADM.SOLIDAR.: ",
    "LiquiSoli: ",
    "Representan: ",
    "Soc.Prof.: ",
    "Con.delegado: ",
    "Con.Delegado: ",
    "Cons.del.man: ",
    "D. Gerente: ",
    "MRO.COMS.CTR: ",
    "SEC.COMS.CTR: ",
    "PRE.COMS.CTR: ",
    "AUDIT.CUENT.: ",
    "Miem.j.g.p.v: ",
    "Miem.a.g.p.v: ",
    "Sec.j.g.p.v: ",
    "Pres.a.g.p.v: ",
    "Pres.j.g.p.v: ",
    "Vpre.j.g.p.v: ",
    "Adm.conjunto: ",
    "Mro.coms.eje: ",
    "Pre.coms.eje: ",
    "Pre.com.aud.: ",
    "Repr.143 Rrm: ",
    "Vicepresiden: ",
    "V- Pre.coms.e: ",
    "V-Pre.coms.e: ",
    "Mbro.com.eje: ",
    "Mbro.com.aud: ",
    "Pre.coms.liq: ",
    "Mro.coms.liq: ",
    "Secr.no Cons: ",
    "V-Sec No Con: "
];
$AumentoCapital = [
    "Resultante Desembolsado",
    "Resultante Suscrito",
    "Capital", 
    "Desembolsado",
    "Suscrito"
];
$ReduccionCapital = [
    "Importe reducción",
    "Resultante Suscrito"
];
$EmpresarioIndividual = [
    "Fecha de Comienzo de Operaciones: ",
    "Objeto Social: ",
    "Domicilio: ",
    "Población: ",
    "Provincia: ",
    "Estado Civil",
    "Régimen Matrimonial"
];
$Anuncios = [
    "Ceses/Dimisiones. " => $Anuncio_Nombramientos,
    "Constitución. " => $Anuncio_Constitucion,
    "Apertura de sucursal. " => $Anuncio_Constitucion,
    "Empresario Individual. " => $EmpresarioIndividual,
    "Primera sucursal de sociedad extranjera" => $Anuncio_Constitucion,
    "Datos registrales. " => NULL,
    "Declaración de unipersonalidad. " => ["Socio único: "],
    "DECLARACION DE UNIPERSONALIDAD" => $Anuncio_Nombramientos, //Para "otros conceptos" 
    "Sociedad unipersonal. " => ["Cambio de identidad del socio único: "], 
    "Pérdida del caracter de unipersonalidad. " => NULL,
    "Cancelaciones de oficio de nombramientos. " => $Anuncio_Nombramientos,
    "Nombramientos. " => $Anuncio_Nombramientos,
    "Reelecciones. " => $Anuncio_Nombramientos, //No se procesa nada
    "Revocaciones" => $Anuncio_Nombramientos,
    "Modificaciones estatutarias. " => ["Artículo de los Estatutos"],
    "Ampliación de capital" => $AumentoCapital,
    "Reducción de capital" => $ReduccionCapital,
    "Disolución. " => ["Fusion", "Voluntaria", "Escision"],
    "Extinción" => NULL,
    "Cambio de domicilio social. " => NULL,
    "Cambio de objeto social. " => NULL,
    "Cambio de denominación social. " => NULL,
    "Transformación de sociedad. " => ["Denominación y forma adoptada: "],
    "Ampliacion del objeto social. " => NULL,
    "Página web de la sociedad. " => ["Nueva página web", "Creación de la página web corporativa"],
    "Modificación de poderes. " => $Anuncio_Nombramientos,
    "Otros conceptos: " => NULL,
    "Fusión por absorción. " => NULL,
    "Situación concursal. " => NULL,
];

//$_GET["id"] = "6261d731d109000007092ed6";
//$_GET["sucursal"] = "EN CANARIAS";

if(!isset($_GET["id"])){
    header('Location: ../AlertaEmpresas');
}

$id = $_GET["id"];
//$id = "6261d731d109000007092ed5";
//$id = "624c98d2e07900000003dbcf";
$conexion = new Conexion();
$database = $conexion->Conectar();
$Result = $database->anuncios->findOne([ "_id" => new MongoDB\BSON\ObjectID($id)]);

if($Result == NULL){
    header('Location: ../AlertaEmpresas');
}

$nombre_comercial = $Result["nombre_comercial"];
$Result = NULL;

//Consultado si existe cambios de nombre en archivos diferentes
$idNombre_comercial = Id_DeNombre($nombre_comercial);
$filter = ["id_otros_nombres" => $idNombre_comercial];
$Result = $database->cambios_nombres->findOne($filter);

$anuncio_borme = NULL; //Juntará todos los anuncios borme
$anuncio_borme_numid = NULL; //Juntará todos los anuncios borme (Solo los numid)
$filter = NULL;
$Result_anuncios = NULL;
$primer_nombre_comercial = NULL;
$nombre_original = NULL;
$provincia = NULL;
if($Result != NULL){
    //Si hay registro en "cambio_nombres"
    foreach($Result["id_otros_nombres"] as $otro_nombre){//Buscamos entre todos los nombres
        if(isset($_GET["sucursal"])){
            $filter = ["id_nombre_comercial" => $otro_nombre, "sucursal" => $_GET["sucursal"]];
        }else{
            $filter = ["id_nombre_comercial" => $otro_nombre];
        }
        $Result_anuncios = $database->anuncios->find($filter)->toArray();
        foreach($Result_anuncios as $anuncio){//Traemos todos los anuncios encontrados con ese nombre
            $anuncio_borme[] = [
                "borme" => $anuncio["borme"],
                "numero" => $anuncio["numero"],
                "numid" => $anuncio["numid"],
                "fecha" => $anuncio["fecha"],
                "tipo" => $anuncio["tipo"],
                "anuncio" => $anuncio["anuncio"]
            ];

            $anuncio_borme_numid[] = [
                "numid" => $anuncio["numid"]
            ];
        }
        if($Result_anuncios != NULL){
            $provincia = $Result_anuncios[0]["provincia"];
        }
    }
    $nombre_original = Id_DeNombre($Result["nombre_original"]);
    //$primer_nombre_comercial = $Result["otros_nombres"][count($Result["otros_nombres"])-1];
    $primer_nombre_comercial = $Result["nombre_original"];
    $id_primer_nombre_comercial = Id_DeNombre($primer_nombre_comercial);
}else{
    /*$filtro = [ "_id" => new MongoDB\BSON\ObjectID($id), "activo" => ['$ne' => 0 ] ];*/
    if(isset($_GET["sucursal"])){
        $filter = ["id_nombre_comercial" => $idNombre_comercial, "sucursal" => $_GET["sucursal"]];
    }else{
        $filter = ["id_nombre_comercial" => $idNombre_comercial];
    }
    
    $collection2 = $database->anuncios;
    $Result_anuncios = $collection2->find($filter)->toArray();

    foreach($Result_anuncios as $res){
        $anuncio_borme[] = [
            "borme" => $res["borme"],
            "numero" => $res["numero"],
            "numid" => $res["numid"],
            "fecha" => $res["fecha"],
            "tipo" => $res["tipo"],
            "anuncio" => $res["anuncio"]
        ];

        $anuncio_borme_numid[] = $res["numid"];
    }
    $nombre_original = Id_DeNombre($Result_anuncios[0]["nombre_comercial"]);
    $primer_nombre_comercial = $Result_anuncios[0]["nombre_comercial"];
    $id_primer_nombre_comercial = Id_DeNombre($primer_nombre_comercial);
    $provincia = $Result_anuncios[0]["provincia"];
}

$anuncio_borme = OrdenarArray($anuncio_borme); //Ordenamos los anuncios

//Funcion que ordena el array
function OrdenarArray($Array){
    //Ordenando primero por anio
    $ArrayPorAnio = NULL;
    foreach($Array as $arr){
        $anio = date("Y", strval($arr["fecha"])/1000);
        $ArrayPorAnio[$anio][] = $arr;
    }

    //$Array = iterator_to_array($Array);
    foreach ($ArrayPorAnio as $key => $row) {
        $aux[] = intval(strval($key));
    }
    array_multisort($aux, SORT_ASC, $ArrayPorAnio);
    $aux = NULL;
    
    $ArrayPorAnio2 = NULL;
    foreach($ArrayPorAnio as $arranio){
        foreach ($arranio as $key => $row) {
            $aux[$key] = intval(strval($row["numero"]));
        }
        array_multisort($aux, SORT_ASC, $arranio);

        foreach($arranio as $arr2){
            $ArrayPorAnio2[] = $arr2;
        }
        $aux = NULL;
    }
    unset($ArrayPorAnio);

    return $ArrayPorAnio2;
}

/*
function OrdenarArray($Array){
    //$Array = iterator_to_array($Array);
    foreach ($Array as $key => $row) {
        $aux[$key] = intval(strval($row["numero"]));
    }
    array_multisort($aux, SORT_ASC, $Array);
    return $Array;
}*/

//BUSCAMOS SI EXISTE LA EMPRESA EN LA COLECCION "EMPRESAS"
$collection = $database->empresas;
if(isset($_GET["sucursal"])){
    $filter = ["nombre_original" => $nombre_original, "sucursal" => $_GET["sucursal"]];
}else{
    $filter = ["nombre_original" => $nombre_original];
}

$Result_Empresas = $collection->findOne($filter);

if($Result_Empresas != NULL){
    //Si existe la empresa
    $CantidadAnunciosEmpresa = count(iterator_to_array($Result_Empresas["anuncio_borme"]));
    $CantidadAnuncios_Actuales = count($anuncio_borme);

    if($CantidadAnunciosEmpresa != $CantidadAnuncios_Actuales){
        //Actualizamos la empresa por que hay anuncios nuevos
        $filter = ["nombre_original" => $nombre_original];
        $DocumentoEmpresa = [
            '$set' => [
            "nombre_original" => $nombre_original,
            "nombre_comercial" => $primer_nombre_comercial,
            "id_nombre_comercial" => $id_primer_nombre_comercial,
            "anuncio_borme" => $anuncio_borme_numid//Guardamos solo los numid de anuncios
            ],

            '$unset' => [
                "Directivos" => "",
                "denominaciones_sociales" => "",
                "id_denominaciones_sociales" => "",
                "actualizado" => "",
                "Constitucion" => "",
                "socio_unico" => ""
            ]
        ];

        $options = [
            'upsert' => true,
            'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER
        ];
        $Result = $collection->findOneAndUpdate($filter, $DocumentoEmpresa,  $options);
    }else{
        $Result = $Result_Empresas;
    }
    $id = $Result_Empresas["_id"];
}else{
    //Creamos una nueva empresa
    if(isset($_GET["sucursal"])){
        $DocumentoEmpresa = [
            "_id" => new MongoDB\BSON\ObjectID($id),
            "nombre_original" => $nombre_original,
            "nombre_comercial" => $primer_nombre_comercial,
            "id_nombre_comercial" => $id_primer_nombre_comercial,
            "provincia" => $provincia,
            "sucursal" => $_GET["sucursal"],
            "anuncio_borme" => $anuncio_borme_numid//Guardamos solo los numid de anuncios
        ];
    }else{
        $DocumentoEmpresa = [
            "_id" => new MongoDB\BSON\ObjectID($id),
            "nombre_original" => $nombre_original,
            "nombre_comercial" => $primer_nombre_comercial,
            "id_nombre_comercial" => $id_primer_nombre_comercial,
            "provincia" => $provincia,
            "anuncio_borme" => $anuncio_borme_numid//Guardamos solo los numid de anuncios
        ];
    }

    $Result = $collection->insertOne($DocumentoEmpresa);
    $id = $Result->getInsertedId();
    $Result = $DocumentoEmpresa;
}

$filter = ["_id" => new MongoDB\BSON\ObjectID($id)];

$_SESSION['AnuncioBorme'] = [
    "id" => $id,
    "anuncio_borme" => $anuncio_borme
];

//GUARDANDO EL NOMBRE SEO FRIENDLY
$NombreEmpresa_Friendly = LimpiarNombre($nombre_comercial);


if(isset($Result["actualizado"])){
    $Actualizado = $Result["actualizado"];
}else{
    $Actualizado = 0;
}
$nombre_comercial = $Result["nombre_comercial"];
if($Actualizado == 0){//Si los datos no estan actualizados
    foreach($anuncio_borme as $anuncio){
        $numero_borme = $anuncio["numero"];
        $fecha_borme = $anuncio["fecha"];
        $Texto_anuncio_borme = $anuncio["anuncio"];
        ProcesarNivel($Texto_anuncio_borme, $Anuncios, 1, NULL);
        GuardarResultados();
    }
    //Documento actualizado = 1
    $document = ['$set' => [ "actualizado" => 1]];
    $Result = $collection->updateOne($filter, $document);
    header('Location: PantallaConsulta/' . $NombreEmpresa_Friendly);
}else{
    header('Location: PantallaConsulta/' . $NombreEmpresa_Friendly);
}

$Array_Nivel1; //Guardará el parrafo seccionado como Array
function ProcesarNivel($Texto, $ArrayAnuncios, $Nivel, $PalabraNivel1){
    $cadena1 = NULL;
    $cadena2 = NULL;
    $Nivel1_String = NULL; //Guarda la palabra clave Nivel 1 encontrada
    $Niverl2_Array = NULL; //Guarda el array Nivel 2 de la palabra clave encontrada
   
    $ArrayBusquedas;
    if($Nivel == 1){
        foreach($ArrayAnuncios as $Arrayanuncio=>$valor){
            if(!(strpos($Texto, $Arrayanuncio) === false)){
                $ArrayBusquedas[$Arrayanuncio] = $valor;
            }
        }
    }else{
        $ArrayBusquedas =  $ArrayAnuncios;
    }
   
   
    for($i=0; $i<strlen($Texto); $i++){
        $cadena1 = $cadena1 . $Texto[$i];
        if($i+1 < strlen($Texto)){

            if(($Texto[$i-1] == ":" || $Texto[$i-1] == ".") && ($Texto[$i] == " ")){//solo busca cuando halla espacios
                foreach($ArrayBusquedas as $Arrayanuncio=>$valor){
                    $Comparador = $Arrayanuncio;
                    if(is_numeric($Comparador)){//Si no es un numero
                        $Comparador = $valor;
                    }
                    if(!(strpos(strtoupper($cadena1), strtoupper($Comparador)) === false)){ //Se encontró una coicidencia en Nivel 1
                        $cadena2 = $cadena1;
                        $cadena2 = str_replace(strtoupper($Comparador), "", strtoupper($cadena2));
                        $cadena1 = "";
                        if($Nivel1_String != NULL){
                            if($Nivel == 1 && $Niverl2_Array != NULL){
                                ProcesarNivel($cadena2, $Niverl2_Array, 2, $Nivel1_String);
                            }else{
                                CrearArray_Nivel2($Nivel1_String, $cadena2, $Niverl2_Array, $PalabraNivel1);
                            }   
                            $Nivel1_String = $Comparador;
                            $cadena2 = NULL;
                            $cadena1 = NULL;
                        }
                        $Nivel1_String = $Comparador;
                        $Niverl2_Array = $valor;
                        $Nivel1_String_Siguiente = $Comparador;
                        break;
                    }
                }
            }
        }else{//Para el ultimo archivo
            $cadena2 = $cadena1;
            if($Nivel == 1 && $Niverl2_Array != NULL){
                ProcesarNivel($cadena2, $Niverl2_Array, 2, $Nivel1_String);
            }else{
                CrearArray_Nivel2($Nivel1_String, $cadena2, $Niverl2_Array, $PalabraNivel1);
            }
            $Nivel1_String = NULL;
            $cadena2 = NULL;
            $cadena1 = NULL; 
        }
    }
}

function CrearArray_Nivel2($Titulo, $Texto, $Niverl2_Array, $PalabraNivel1){
    global $Array_Nivel1;
    if($PalabraNivel1 == NULL) $PalabraNivel1 = $Titulo; //Esto es para "Datos Registrales"
    $Titulo = Trimear($Titulo);
    $Texto = Trimear($Texto);
    $Array_Nivel2 = [$Titulo => $Texto];
    $PalabraNivel1 = eliminar_acentos($PalabraNivel1);
    $PalabraNivel1 = Trimear($PalabraNivel1);

    if(isset($Array_Nivel1[$PalabraNivel1])){
        if(isset($Array_Nivel1[$PalabraNivel1][$Titulo])){
            $TextoCompleto = $Array_Nivel1[$PalabraNivel1][$Titulo] . ";" . $Texto;
            $Array_Nivel2 = [$Titulo => $TextoCompleto];
        }else{
            $Array_Nivel2 = [$Titulo => $Texto];
        }
        $Array_Anterior = $Array_Nivel1[$PalabraNivel1];
        $Array_Nivel2 = array_merge($Array_Anterior,$Array_Nivel2);
    }
    $Array_Nivel1[$PalabraNivel1] = $Array_Nivel2;
}

function GuardarResultados(){
    global $Array_Nivel1;
    global $numero_borme;
    global $fecha_borme;
    $Datos_Registrales = $Array_Nivel1["Datos registrales"]["Datos registrales"];
    $fecha_formateada = date("d/m/Y", strval($fecha_borme)/1000);
    echo $fecha_formateada . "<br><br>";

    //Sustrayendo solo la fecha
    $FechaInscripcion = substr($Datos_Registrales, strlen($Datos_Registrales)-9, strlen($Datos_Registrales));
    $FechaInscripcion = trim($FechaInscripcion, ')');
    $FechaInscripcion = str_ireplace(".", "/", $FechaInscripcion);
    $FechaInscripcion = str_ireplace(" ", "0", $FechaInscripcion);

    //Sustrayendo solo los datos registrales
    $Datos_Registrales = substr($Datos_Registrales, 0, strlen($Datos_Registrales)-10);
    $Datos_Registrales = trim($Datos_Registrales, " ");

    //$Array_Nivel1 = array_reverse($Array_Nivel1);
    foreach($Array_Nivel1 as $Clave => $Valor){
        switch($Clave){
            case "Constitucion":
                Constitucion($Datos_Registrales, $Valor, $FechaInscripcion);
                //Constitucion2($Datos_Registrales, $Valor, $FechaInscripcion);
                break;
            case "Apertura de sucursal":
                Constitucion($Datos_Registrales, $Valor, $FechaInscripcion);
                break;
            case "Empresario Individual":
                PrimeraSucursalExtranjera($Datos_Registrales, $Valor, $FechaInscripcion, "Empresario Individual");
                break;
            case "Primera sucursal de sociedad extranjera":
                PrimeraSucursalExtranjera($Datos_Registrales, $Valor, $FechaInscripcion, "Primera sucursal de sociedad extranjera");
                break;
            case "Nombramientos":
                Nombramientos2($Datos_Registrales, $Valor, $FechaInscripcion);
                Nombramientos3($Datos_Registrales, $Valor, $FechaInscripcion);
            case "Reelecciones":
                Nombramientos2($Datos_Registrales, $Valor, $FechaInscripcion);
                Nombramientos3($Datos_Registrales, $Valor, $FechaInscripcion);
                break;
            case "Ceses/Dimisiones":
                Ceses_Dimisiones($Datos_Registrales, $Valor, $FechaInscripcion);
                break;
            case "Cancelaciones de oficio de nombramientos":
                CancelacionOficioNombramiento($Valor, $FechaInscripcion);
                break;
            case "Modificacion de poderes":
                ModificacionPoderes($Datos_Registrales, $Valor, $FechaInscripcion);
                break;
            case "Revocaciones":
                CancelacionOficioNombramiento($Valor, $FechaInscripcion);
                break;
            case "Declaracion de unipersonalidad":
                Nombramientos3($Datos_Registrales, $Valor, $FechaInscripcion);
                DeclaracionUnipersonal($Valor);
                break;
            case "Perdida del caracter de unipersonalidad":
                PerdidaUnipersonalidad($Datos_Registrales, $Valor, $FechaInscripcion);
                break;;
            case "Sociedad unipersonal":
                SociedadUnipersonal($Datos_Registrales, $Valor, $FechaInscripcion);
                break;
            case "Cambio de domicilio social":
                CambioDomicilio($Valor, "Constitucion.datos.Domicilio");
                break;
            case "Cambio de objeto social":
                CambioDomicilio($Valor, "Constitucion.datos.Objeto social");
                break;
            case "Cambio de denominacion social":
                CambioDenominacionSocial($Valor, "Cambio de denominación social");
                break;
            case "Transformacion de sociedad":
                CambioDenominacionSocial($Valor, "Denominación y forma adoptada");
                break;
            case "Ampliacion del objeto social":
                AmpliaciónObjetoSocial($Valor);
                break;
            case "Ampliacion de capital":
                CambioCapital($Valor);
                break;
            case "Reduccion de capital":
                CambioCapital($Valor);
                break;
            case "Extincion":
                Extincion($FechaInscripcion);
                break;
            case "Pagina web de la sociedad":
                Paginaweb($Valor);
                break;
            default:
                break;
        }
        //Actualizando los datos
        global $filter;
        global $collection;
        global $nombre_comercial;
        global $DocumentoEntero;

        $Result = $collection->findOne($filter);
        $nombre_comercial = $Result["nombre_comercial"];
        $DocumentoEntero = $Result;
    }
    $Array_Nivel1 = NULL;
}

function Constitucion($DatosRegistrales, $arr, $FechaInscripcion){
    global $numero_borme;
    global $fecha_borme;
    global $filter;
    global $collection;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    //Sustrayendo solo los datos registrales
    $DatosRegistrales = substr($DatosRegistrales, 0, strlen($DatosRegistrales)-10);
    $DatosRegistrales = trim($DatosRegistrales, " ");

    $Array_Constitucion = [
        "numero_borme" => $numero_borme,
        "fecha_borme" => $fecha_borme,
        "datos_registrales" => $DatosRegistrales,
        "fecha_incripcion" => $FechaMilis,
        "datos" => $arr
    ];
    
    $document = ['$set' => [ "Constitucion" => $Array_Constitucion]];
    $Result = $collection->updateOne($filter, $document);
}

function Constitucion2($Datos_Registrales, $Valor, $FechaInscripcion){
    echo "Constitución<br>";
    echo "Se constituye la empresa con fecha ".$FechaInscripcion." mediante escritura pública. 
    La empresa se crea con un capital social inicial de ".$Valor["Capital"]." e inicia su actividad 
    el ".$Valor["Comienzo de operaciones"].". El domicilio social en el momento de su constitución se 
    establece en ".$Valor["Domicilio"].". La empresa indica que 
    su actividad es " . $Valor["Objeto social"] . ".<br><br>";
    echo "Fuente: Boletín Oficial del Registro Mercantil<br>";
    echo "Fecha inscripcion: " . $FechaInscripcion . " " . $Datos_Registrales . "<br><br><br>";
}

function PrimeraSucursalExtranjera($DatosRegistrales, $Valor, $FechaInscripcion, $tipo){
    global $numero_borme;
    global $fecha_borme;
    global $filter;
    global $collection;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    //Sustrayendo solo los datos registrales
    $DatosRegistrales = substr($DatosRegistrales, 0, strlen($DatosRegistrales)-10);
    $DatosRegistrales = trim($DatosRegistrales, " ");

    $Array_Constitucion = [
        "numero_borme" => $numero_borme,
        "fecha_borme" => $fecha_borme,
        "datos_registrales" => $DatosRegistrales,
        "fecha_incripcion" => $FechaMilis,
        "tipo" => $tipo,
        "datos" => $Valor
    ];
    
    $document = ['$set' => [ "Constitucion" => $Array_Constitucion]];
    $Result = $collection->updateOne($filter, $document);
}

function DeclaracionUnipersonal2($Datos_Registrales, $Valor, $FechaInscripcion){
    echo "Declaración de unipersonalidad<br>";
    echo "Con fecha ".$FechaInscripcion." se inscribe en el Registro Mercantil la adquisición del carácter 
    unipersonal de la sociedad, siendo el titular jurídico y formal de las acciones o participaciones 
    societarias la sociedad ".$Valor["Socio único"].".<br><br>";
    echo "Fuente: Boletín Oficial del Registro Mercantil<br>";
    echo "Fecha inscripcion: " . $FechaInscripcion . " " . $Datos_Registrales . "<br><br><br>";
}

function DeclaracionUnipersonal($Valor){
    global $filter;
    global $collection;
    $SocioUnico = Trimear($Valor["Socio único"]);
    $documentUpdate = ['$set' => ["socio_unico" => $SocioUnico]];
    $Result = $collection->updateOne($filter, $documentUpdate);
}

function PerdidaUnipersonalidad($Datos_Registrales, $Valor, $FechaInscripcion){
    global $filter;
    global $collection;
    global $DocumentoEntero;
    $Result = $collection->find($filter)->toArray();

    $SocioUnico_Actual = NULL;
    if(isset($Result[0]["socio_unico"])){
        $SocioUnico_Actual = $Result[0]["socio_unico"];
        
        $Posicion = -1;
        if(isset($DocumentoEntero["Directivos"])){
            $Posicion = indexOfArray_Varios($DocumentoEntero["Directivos"], $SocioUnico_Actual, "Socio único");
        }

        //$Posicion = indexOfArray($SocioUnico_Actual, "Directivos.datos.entidad");****

        $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
        $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

        if($Posicion >= 0){
            $documentUpdate = ['$set' => ["Directivos.".$Posicion.".datos.hasta" => $FechaMilis]];
            $Result = $collection->updateOne($filter, $documentUpdate);
        }

        $documentUpdate = ['$set' => ["socio_unico" => ""]];
        $Result = $collection->updateOne($filter, $documentUpdate);
    }
}

function ModificacionPoderes($Datos_Registrales, $Valor, $FechaInscripcion){
    global $filter;
    global $collection;
    global $DocumentoEntero;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    foreach($Valor as $clave=>$valor){
        $Relacion = $clave;
        $Cadena = $valor;
        $Lista = explode(";", $Cadena);
        foreach($Lista as $lis){
            $Posicion = indexOfArray_Varios($DocumentoEntero["Directivos"], $lis, $Relacion);
            //$Posicion = indexOfArray($lis, "Directivos.datos.entidad");
            $documentUpdate = ['$set' => ["Directivos.".$Posicion.".datos.hasta" => $FechaMilis]];
            $Result = $collection->updateOne($filter, $documentUpdate);
        }    
    }

    $arraysEntidades = [];
    foreach($Valor as $clave=>$valor){
        $Relacion = $clave;
        $Cadena = $valor;
        $Lista = explode(";", $Cadena);

        $array_individual["fecha"] = $FechaMilis;
        foreach($Lista as $lis){
            $lis = trim($lis, ":");
            $lis = trim($lis, " ");
            $arrayEntidad = [
                "entidad" => $lis,
                "relacion" => $Relacion,
                "desde" => $FechaMilis,
                "hasta" => ""
            ];
            $array_individual["datos"] = $arrayEntidad;

            $IndiceArray = -1;
            if(isset($DocumentoEntero["Directivos"])){
                $IndiceArray = indexOfArray_Varios($DocumentoEntero["Directivos"], $lis, $Relacion);
            }
            //$IndiceArray = indexOfArray($lis, 'Directivos.datos.entidad');
            //if($IndiceArray === -1 || $IndiceArray === NULL){
            if($IndiceArray == -1){
                //No existe el nombramiento
                $document2 = ['$addToSet' => [ "Directivos" => $array_individual]];
                $Result = $collection->updateOne($filter, $document2);
            }else{
                //Si ya existe el nombramiento
                $filtro2 = ["Directivos.datos.entidad" => $lis];
                $Result = $collection->findOne($filtro2, ['projection' => ["Directivos.datos.hasta.$" => 1]]);
                $FechaHasta = $Result["Directivos"][0]["datos"]["hasta"];
                if($FechaHasta == ""){
                    $documentUpdate = ['$set' => ["Directivos.".$IndiceArray.".datos.hasta" => ""]];
                    $Result = $collection->updateOne($filter, $documentUpdate);
                }else{
                    $document2 = ['$addToSet' => [ "Directivos" => $array_individual]];
                    $Result = $collection->updateOne($filter, $document2);
                }
            }
        }   
    }
}

function Nombramientos2($Datos_Registrales, $Valor, $FechaInscripcion){
    /*echo "Nombramientos<br>";
    foreach($Valor as $Clave => $valor){
        $Lista = explode(";", $valor);
        if(count($Lista) == 1){
            echo "Con fecha ".$FechaInscripcion." se inscribe en el Registro Mercantil el nombramiento 
            de ".$valor." como ".$Clave." de la sociedad.<br><br>";
        }else{
            echo "Publicación en el BORME de los siguientes nombramientos:<br>";
            //$Lista = explode(";", $valor);
            echo $Clave . "<br>";
            foreach($Lista as $lis){
                echo $lis . "<br>";
            }
            echo "<br>";
        }
    }

    echo "Fuente: Boletín Oficial del Registro Mercantil<br>";
    echo "Fecha inscripcion: " . $FechaInscripcion . " " . $Datos_Registrales . "<br><br><br>";*/

}

function Nombramientos3($Datos_Registrales, $Valor, $FechaInscripcion){
    global $filter;
    global $collection;
    global $DocumentoEntero;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    $arraysEntidades = [];
    foreach($Valor as $clave=>$valor){
        $Relacion = $clave;
        $Cadena = $valor;
        $Lista = explode(";", $Cadena);

        $array_individual["fecha"] = $FechaMilis;
        foreach($Lista as $lis){
            $lis = trim($lis, ":");
            $lis = trim($lis, " ");
            $arrayEntidad = [
                "entidad" => $lis,
                "relacion" => $Relacion,
                "desde" => $FechaMilis,
                "hasta" => ""
            ];
            $array_individual["datos"] = $arrayEntidad;

            $IndiceArray = -1;
            if(isset($DocumentoEntero["Directivos"])){
                $IndiceArray = indexOfArray_Varios($DocumentoEntero["Directivos"], $lis, $Relacion);
            }

            if($IndiceArray == -1){
                //No existe el nombramiento, agregamos uno nuevo
                $document2 = ['$addToSet' => [ "Directivos" => $array_individual]];
                $Result = $collection->updateOne($filter, $document2);
            }else{
                //Si hay nombramiento con ese nombre y "relación"
                $DesdeAnterior = $DocumentoEntero["Directivos"][$IndiceArray]["datos"]["desde"];
                if($DesdeAnterior == ""){
                    $documentUpdate = [
                        '$set' => 
                        [
                            "Directivos.".$IndiceArray.".datos.desde" => $FechaMilis,
                            "Directivos.".$IndiceArray.".datos.hasta" => ""
                        ]
                    ];
                    $Result = $collection->updateOne($filter, $documentUpdate);
                }else{
                    $documentUpdate = ['$set' => ["Directivos.".$IndiceArray.".datos.hasta" => ""]];
                    $Result = $collection->updateOne($filter, $documentUpdate);
                }
            }
        }   
    }
}

function SociedadUnipersonal($Datos_Registrales, $Valor, $FechaInscripcion){
    global $filter;
    global $collection;
    global $DocumentoEntero;
    $Result = $collection->find($filter)->toArray();

    $SocioUnico_Actual = NULL;
    if(isset($Result[0]["socio_unico"])){
        $SocioUnico_Actual = $Result[0]["socio_unico"];
        //$Posicion = indexOfArray($SocioUnico_Actual, "Directivos.datos.entidad");
        $Posicion = indexOfArray_Varios($DocumentoEntero["Directivos"], $SocioUnico_Actual, "Socio único");
    }else{
        $Posicion = -1;
    }
    
    $SocioUnico_Nuevo = Trimear($Valor["Cambio de identidad del socio único"]);
    $ArrayNuevo = ["Socio único" => $SocioUnico_Nuevo];

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);
    if($Posicion >= 0){
        //Ya existe un registro en "Directivos"
        $documentUpdate = ['$set' => ["Directivos.".$Posicion.".datos.hasta" => $FechaMilis]];
        $Result = $collection->updateOne($filter, $documentUpdate);
    
        Nombramientos3($Datos_Registrales, $ArrayNuevo, $FechaInscripcion);
    }else{
        //No existe un registro en Directivos
        Nombramientos3($Datos_Registrales, $ArrayNuevo, $FechaInscripcion);
    }

    $documentUpdate = ['$set' => ["socio_unico" => $SocioUnico_Nuevo]];
    $Result = $collection->updateOne($filter, $documentUpdate);

}

function Ceses_Dimisiones($Datos_Registrales, $Valor, $FechaInscripcion){
    
    //IMPRIMIENDO EL TEXTO CESES Y DIMISIONES
    /*
        echo "Ceses_Dimisiones<br>";
        foreach($Valor as $Clave => $valor){
            if(count($Valor) == 1){
                echo "Con fecha ".$FechaInscripcion." se inscribe en el Registro Mercantil el cese o dimisión de
                ".$valor." como ".$Clave." de la sociedad.<br><br>";
            }else{
                echo "Publicación en el BORME de los siguientes ceses-dimisiones:<br>";
                $Lista = explode(";", $valor);
                echo $Clave . "<br>";
                foreach($Lista as $lis){
                    echo $lis . "<br>";
                }
                echo "<br>";
            }
        }
        echo "Fuente: Boletín Oficial del Registro Mercantil<br>";
        echo "Fecha inscripcion: " . $FechaInscripcion . " " . $Datos_Registrales . "<br><br><br>";
    */

    //GUARDANDO LOS DATOS DEL CESE Y DIMISION
    global $filter;
    global $collection;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    $arraysEntidades = [];
    foreach($Valor as $clave=>$valor){
        $Relacion = $clave;
        $Cadena = $valor;
        $Lista = explode(";", $Cadena);

        $array_individual["fecha"] = $FechaMilis;
        foreach($Lista as $lis){
            $lis = trim($lis, ":");
            $lis = trim($lis, " ");
            $arrayEntidad = [
                "entidad" => $lis,
                "relacion" => $Relacion,
                "desde" => "",
                "hasta" => $FechaMilis
            ];
            /*$array_individual["datos"] = $arrayEntidad;
            $document2 = ['$addToSet' => [ "Cese_Dimisiones" => $array_individual]];
            $Result = $collection->updateOne($filter, $document2);*/
            Cese_Dimision_Update($lis, $FechaMilis, $arrayEntidad);
        }   
    }


}

function Cese_Dimision_Update($Directivo, $Fecha_Cese, $ArrayCompleto){
    global $collection;
    global $filter;
    global $DocumentoEntero;

    //ACTUALIZANDO LA FECHA DEL DIRECTIVO
    $Relacion = $ArrayCompleto["relacion"];
    $IndiceArray = -1;
    if(isset($DocumentoEntero["Directivos"])){
        $IndiceArray = indexOfArray_Varios($DocumentoEntero["Directivos"], $Directivo, $Relacion);
    }

    if($IndiceArray == -1){
        //No habian nombramientos se crea el nombramiento
        $ArrayDirectivo = [
            "fecha" => 0,
            "datos" => $ArrayCompleto
        ];        
        $document2 = ['$addToSet' => [ "Directivos" => $ArrayDirectivo]];
        $Result = $collection->updateOne($filter, $document2);
    }else{
        //Si hay nombramiento con ese nombre y relacion
        $documentUpdate = ['$set' => ["Directivos.".$IndiceArray.".datos.hasta" => $Fecha_Cese]];
        $Result = $collection->updateOne($filter, $documentUpdate);
    }
}

function CancelacionOficioNombramiento($Valor, $FechaInscripcion){
    //GUARDANDO LOS DATOS DEL CESE Y DIMISION
    global $filter;
    global $collection;
    global $DocumentoEntero;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $Fecha_Cese = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    foreach($Valor as $clave=>$valor){
        $Relacion = $clave;
        $Cadena = $valor;
        $Lista = explode(";", $Cadena);

        foreach($Lista as $lis){
            $lis = trim($lis, ":");
            $lis = trim($lis, " ");
            //$IndiceArray = indexOfArray($lis, 'Directivos.datos.entidad');
            $IndiceArray = -1;
            if(isset($DocumentoEntero["Directivos"])){
                $IndiceArray = indexOfArray_Varios($DocumentoEntero["Directivos"], $lis, $Relacion);
            }
            //if(!($IndiceArray === -1 || $IndiceArray === NULL)){
            if($IndiceArray == -1){
                //No habian nombramientos se crea el nombramiento
                $ArrayDirectivo = [
                    "fecha" => 0,
                    "datos" => [
                        "entidad" => $lis,
                        "relacion" => $Relacion,
                        "desde" => "",
                        "hasta" => $Fecha_Cese
                    ]
                ];

                $document2 = ['$addToSet' => [ "Directivos" => $ArrayDirectivo]];
                $Result = $collection->updateOne($filter, $document2);
            }else{
                $documentUpdate = ['$set' => ["Directivos.".$IndiceArray.".datos.hasta" => $Fecha_Cese]];
                $Result = $collection->updateOne($filter, $documentUpdate);
            }
        }   
    }
}

function CambioDomicilio($Valor, $Ruta){
    global $filter;
    global $collection;
    foreach($Valor as $clave=>$Direccion_Nueva){
        $documentUpdate = ['$set' => [$Ruta => $Direccion_Nueva]];
        $Result = $collection->updateOne($filter, $documentUpdate);
    }
}

function CambioDenominacionSocial_Anterior($Valor, $String){
    global $filter;
    global $collection;
    global $nombre_comercial;

    $nombre_comercial = $nombre_comercial;

    $Result = $collection->findOne($filter, ['projection' => ['denominaciones_sociales' => 1, '_id' => 0]]);
    $Denominaciones = "";
    if(count($Result) != 0){
        $Denominaciones = $Result["denominaciones_sociales"];
    }
    
    //$DenominacionNueva = $Valor[$String] . ".";
    $DenominacionNueva = $Valor[$String];

    if(strtoupper($nombre_comercial) != strtoupper($DenominacionNueva)){
        if(strpos(strtoupper($Denominaciones), strtoupper($nombre_comercial)) === false){
            //Si el nombre comercial no existe ya en denominaciones_sociales
            if($Denominaciones != ""){
                $Denominaciones = $Denominaciones . "; " . $nombre_comercial;
            }else{
                $Denominaciones = $Denominaciones . $nombre_comercial;
            }

            //Actualizando "denominaciones_sociales"
            $documentUpdate = ['$set' => ["denominaciones_sociales" => $Denominaciones]];
            $Result = $collection->updateOne($filter, $documentUpdate);

            //Actualizando "nombre_comercial"
            $documentUpdate = ['$set' => ["nombre_comercial" => $DenominacionNueva]];
            $Result = $collection->updateOne($filter, $documentUpdate);
        }
    }
}

function CambioDenominacionSocial($Valor, $String){
    global $filter;
    global $collection;
    global $nombre_comercial;



    $Result = $collection->findOne($filter, ['projection' => ['denominaciones_sociales' => 1, '_id' => 0]]);
    $Denominaciones = NULL;
    if(count($Result) != 0){
        $Denominaciones = $Result["denominaciones_sociales"];
    }

    $DenominacionNueva = trimear($Valor[$String]);
    //$DenominacionNueva = $DenominacionNueva . ".";
    $DenominacionNueva = $DenominacionNueva;

    if(strtoupper($nombre_comercial) != strtoupper($DenominacionNueva)){
        $nombre_comercial2 = Id_DeNombre($nombre_comercial);

        $encontrado = 0;
        foreach($Denominaciones as $denominacion){
            if($nombre_comercial2 == $denominacion){
                $encontrado = 1;
                break;
            }
        }
        if($encontrado == 0){
            //Si el nombre comercial no existe ya en denominaciones_sociales

            //Actualizando "denominaciones_sociales"
            //$Result = $collection->updateOne($filter, $documentUpdate);

            //Actualizando "nombre_comercial"

            $id_nombre_comercial_nuevo = str_replace(".", "", $DenominacionNueva);
            $id_nombre_comercial_nuevo = str_replace(",", "", $id_nombre_comercial_nuevo);
            $id_nombre_comercial_nuevo = str_replace(" ", "", $id_nombre_comercial_nuevo);

            $documentUpdate = [
                '$addToSet' => [
                    "id_denominaciones_sociales" => $nombre_comercial2,
                    "denominaciones_sociales" => $nombre_comercial
                ],
                '$set' => [
                    "nombre_comercial" => $DenominacionNueva,
                    "id_nombre_comercial" => $id_nombre_comercial_nuevo
                ]
            ];
            $Result = $collection->updateOne($filter, $documentUpdate);
        }
    }
}

function AmpliaciónObjetoSocial($Valor){
    global $filter;
    global $collection;
    global $nombre_comercial;

    $Result = $collection->findOne($filter, ['projection' => ['Constitucion.datos.Objeto social' => 1, '_id' => 0]]);
    
    $ObjetoSocial_Actual = "";
    if(isset($Result["Constitucion"]["datos"]["Objeto social"])){
        if(strpos(strtoupper($ObjetoSocial_Actual), strtoupper($Valor["Ampliacion del objeto social"])) === false){
            //Si no se ha actualizado el Objeto Social
            $ObjetoSocial_Actual = $ObjetoSocial_Actual . ". " . $Valor["Ampliacion del objeto social"];
        }
    }else{
        //No existia el objeto social
        $ObjetoSocial_Actual = $Valor["Ampliacion del objeto social"];
    }

    if($ObjetoSocial_Actual != ""){
        //Actualizando "Objeto social"
        $documentUpdate = ['$set' => ["Constitucion.datos.Objeto social" => $ObjetoSocial_Actual]];
        $Result = $collection->updateOne($filter, $documentUpdate);
    }
}

function CambioCapital($Valor){
    global $filter;
    global $collection;
    $ResultanteSuscrito = Trimear($Valor["Resultante Suscrito"]);
    //Actualizando "Capital"
    $documentUpdate = ['$set' => ["Constitucion.datos.Capital" => $ResultanteSuscrito]];
    $Result = $collection->updateOne($filter, $documentUpdate);
}

function Extincion($FechaInscripcion){
    global $filter;
    global $collection;

    $dateNew = DateTime::createFromFormat('d/m/y', $FechaInscripcion)->format('Y/m/d');
    $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime($dateNew . " 00:00:00")*1000);

    //Actualizando "Extincion"
    $documentUpdate = ['$set' => ["extincion" => $FechaMilis]];
    $Result = $collection->updateOne($filter, $documentUpdate);
}

function Paginaweb($Valor){
    global $filter;
    global $collection;
    foreach($Valor as $val){
        $PaginaWeb = Trimear($val);
        //Actualizando "Capital"
        $documentUpdate = ['$set' => ["pagina_web" => $PaginaWeb]];
        $Result = $collection->updateOne($filter, $documentUpdate);
    }
}





function Trimear($Texto){
    if($Texto != NUll){
        $Texto = trim($Texto, ' ');
        $Texto = trim($Texto, '.');
        $Texto = trim($Texto, ':');
    }
    return $Texto;
}

function indexOfArray($Busqueda, $Ruta){
    //Busca el índice donde se encuentra un array
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

function indexOfArray_Varios($Array_Busqueda, $Directivo, $Relacion){
    $Array_respuesta = -1;
    foreach($Array_Busqueda as $Clave => $Valor){
        if(($Valor["datos"]["entidad"] == $Directivo) &&  ($Valor["datos"]["relacion"] == $Relacion)){
            $Array_respuesta = $Clave;
        }
    }

    return $Array_respuesta;
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

function Id_DeNombre($Nombre){
    $IdNombre = str_replace(".", "", $Nombre);
    $IdNombre = str_replace(",", "", $IdNombre);
    $IdNombre = str_replace(" ", "", $IdNombre);
    return $IdNombre;
}

function LimpiarNombre($Nombre){
    $Nombre = str_replace(".", "", $Nombre);
    $Nombre = str_replace(",", "", $Nombre);
    $Nombre = str_replace("(", "-", $Nombre);
    $Nombre = str_replace(")", "", $Nombre);
    $Nombre = mb_strtolower($Nombre, 'UTF-8');
    $Nombre = str_replace(" ", "-", $Nombre);
    return $Nombre;
}

?>