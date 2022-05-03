<?php

$tiempo_inicio = microtime(true);
set_time_limit(50000);
ini_set('memory_limit', '1024M');

$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

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


/*
    $filter = [
        '$or' => 
            [
                ["anuncio_borme.anuncio" => ['$regex' => "Cambio de denominación social"]],
                ["anuncio_borme.anuncio" => ['$regex' => "Transformación de sociedad"]]
            ]*/
        //"anuncio_borme.anuncio" => ['$regex' => "Cambio de denominación social"]//,
        //"anuncio_borme.anuncio" => ['$regex' => "Transformación de sociedad"]
    //];
    //$filter = ["nombre_comercial" => ['$regex' => "SERGIO ESCRIG SOCIEDAD LIMITADA."] ];
    //$conexion = new Conexion();
    /*$connection = new MongoDB\Client('mongodb://localhost:27017');
    $database = $connection->alertaempresas;
    //$database = $conexion->Conectar();
    $collection = $database->empresas;
*/
$collection = NULL;
$collection2 = NULL;
$NombreActual_Empresa;
$IDActual_Empresa;
//$Tipo_Anuncio; // 1 = Transformación de sociedad, 2 = Cambio de denominación social
$DocumentoEntero;
$Array_Nivel1; //Guardará el parrafo seccionado como Array

///UnirCambiosDeNombre(2);
function UnirCambiosDeNombre($tipo){
    global $Anuncios;
    global $collection;
    global $collection2;
    global $NombreActual_Empresa;
    global $IDActual_Empresa;

    $conexion = new Conexion();
    $database = $conexion->Conectar();

    //Para que pueda usarse diariamente o cuando se requiera unir todos
    $collection = NULL;
    if($tipo == 1){
        $collection = $database->anuncios_dia;
    }else if($tipo == 2){
        $collection = $database->anuncios;
    }

    $collection2 = $database->cambios_nombres;
    
    $filter = [
        '$or' => 
            [
                ["anuncio" => ['$regex' => "Cambio de denominación social"]],
                ["anuncio" => ['$regex' => "Transformación de sociedad"]]
            ]
    ];

    $Options = [
        'limit' => 200000
        //'projection' => ['anuncio_borme.anuncio' => 1, 'nombre_comercial' => 1]
    ];
    $Result = $collection->find($filter, $Options)->toArray();

    echo count($Result) . "<br><br>";

    if($Result != NULL){
        foreach($Result as $res){
            //$anuncio_borme = $res["anuncio_borme"];
            //$anuncio_borme = $res;
            //foreach($anuncio_borme as $anuncio){
                //$Texto_anuncio_borme = $res["anuncio_borme"][0]["anuncio"];
                $Texto_anuncio_borme = $res["anuncio"];
                //$Tipo_Anuncio = 1;
                /*if(!(strpos($Texto_anuncio_borme, "Cambio de denominación social") === false)){
                    //$Tipo_Anuncio = 2;
                    $Marca = strpos($Texto_anuncio_borme, "Cambio de denominación social");
                }else{
                    $Marca = strpos($Texto_anuncio_borme, "Transformación de sociedad");
                }*/

                //$Texto_anuncio_borme = substr($Texto_anuncio_borme, $Marca);
                $Lista = explode(" Datos registrales. ", $Texto_anuncio_borme);
                $Texto_anuncio_borme = $Lista[0];

                if(!(strpos($Texto_anuncio_borme, "Cambio de denominación social") === false) || 
                !(strpos($Texto_anuncio_borme, "Transformación de sociedad") === false) ){
                    //$ID_EmpresaEmisora = $res["_id"];
                    $NombreActual_Empresa = $res["nombre_comercial"];
                    $IDActual_Empresa = $res["_id"];
                    ProcesarNivel($Texto_anuncio_borme, $Anuncios, 1, NULL);
                    OrientarResultados();
                }
            //}
        }
    }
    //GuardarDatos();
}



function ProcesarNivel($Texto, $ArrayAnuncios, $Nivel, $PalabraNivel1){
    $cadena1 = NULL;
    $cadena2 = NULL;
    $Nivel1_String = NULL; //Guarda la palabra clave Nivel 1 encontrada
    $Niverl2_Array = NULL; //Guarda el array Nivel 2 de la palabra clave encontrada

    $ArrayBusquedas = NULL;
    if($Nivel == 1){
        foreach($ArrayAnuncios as $Arrayanuncio=>$valor){
            if(!(strpos($Texto, $Arrayanuncio) === false)){
                $ArrayBusquedas[$Arrayanuncio] = $valor;
            }
        }
    }else{
        $ArrayBusquedas =  $ArrayAnuncios;
    }

    if($ArrayBusquedas != NULL){
        for($i=0; $i<strlen($Texto); $i++){//Declaración de unipersonalidad. //
            $cadena1 = $cadena1 . $Texto[$i];
            if($i+1 < strlen($Texto)){
                if(($Texto[$i-1] == ":" || $Texto[$i-1] == ".") && ($Texto[$i] == " ")){//solo busca cuando halla espacios
                    foreach($ArrayBusquedas as $Arrayanuncio=>$valor){
                        $Comparador = $Arrayanuncio;
                        if(is_numeric($Comparador)){//Si no es un numero
                            $Comparador = $valor;
                        }
                        if(!(strpos($cadena1, $Comparador) === false)){ //Se encontró una coicidencia en Nivel 1
                            $cadena2 = $cadena1;
                            $cadena2 = str_replace($Comparador, "", $cadena2);
                            $cadena1 = "";
                            if($Nivel1_String != NULL){
                                if($Nivel1_String == "Cambio de denominación social. " || $Nivel1_String == "Transformación de sociedad. "){
                                    if($Nivel == 1 && $Niverl2_Array != NULL){
                                        ProcesarNivel($cadena2, $Niverl2_Array, 2, $Nivel1_String);
                                    }else{
                                        CrearArray_Nivel2($Nivel1_String, $cadena2, $PalabraNivel1);
                                    }  
                                } 
                                $Nivel1_String = $Comparador;
                                $cadena2 = NULL;
                                $cadena1 = NULL;
                                //$i = strlen($Texto);
                                break;
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
                    CrearArray_Nivel2($Nivel1_String, $cadena2, $PalabraNivel1);
                }
                $Nivel1_String = NULL;
                $cadena2 = NULL;
                $cadena1 = NULL; 
            }
        }
    }
}

function CrearArray_Nivel2($Titulo, $Texto, $PalabraNivel1){
    global $Array_Nivel1;
    if($Titulo == NULL) $Titulo = $PalabraNivel1;
    if($PalabraNivel1 == NULL) $PalabraNivel1 = $Titulo;
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

function OrientarResultados(){
    global $Array_Nivel1;

    if($Array_Nivel1 != NULL){
        foreach($Array_Nivel1 as $Clave => $Valor){
            switch($Clave){
                case "Cambio de denominacion social":
                    CambioDenominacionSocial($Valor, "Cambio de denominación social");
                    break;
                case "Transformacion de sociedad":
                    CambioDenominacionSocial($Valor, "Denominación y forma adoptada");
                    break;
                default:
                    break;
            }
        }
    }

    $Array_Nivel1 = NULL;
}

$ArrayDeInsercion;
function CambioDenominacionSocial($Valor, $Busqueda){
    //global $ID_EmpresaEmisora;
    global $NombreActual_Empresa; 
    global $collection2;
    global $IDActual_Empresa;
    /*global $Tipo_Anuncio;
    global $ArrayDeInsercion;*/
    $NombreNuevo_Empresa = $Valor[$Busqueda];
    $NombreNuevo_Empresa = trim($NombreNuevo_Empresa, " ");
    $NombreNuevo_Empresa = trim($NombreNuevo_Empresa, ".");

    $NombreActual_Empresa = trim($NombreActual_Empresa, " ");
    $NombreActual_Empresa = trim($NombreActual_Empresa, ".");
    /*$NombreNuevo_Empresa = str_replace(".", "", $NombreNuevo_Empresa);
    $NombreNuevo_Empresa = str_replace(",", "", $NombreNuevo_Empresa);
    $NombreNuevo_Empresa = str_replace(" ", "", $NombreNuevo_Empresa);*/

    $filtro = ["otros_nombres" => $NombreActual_Empresa];
    $Result = $collection2->findOne($filtro);



    if($Result == NULL){
        //No hay ningun registros
        $id_nombre_comercialActual = Id_DeNombre($NombreActual_Empresa);
        $id_nombre_comercialNuevo = Id_DeNombre($NombreNuevo_Empresa);

        $Documento = [
            'nombre_original' => $NombreActual_Empresa,
            'idempresa_original' =>  new MongoDB\BSON\ObjectID($IDActual_Empresa),
            'otros_nombres' => [
                $NombreActual_Empresa,
                $NombreNuevo_Empresa
            ],
            'id_otros_nombres' => [
                $id_nombre_comercialActual,
                $id_nombre_comercialNuevo
            ]
        ];
        $Result = $collection2->insertOne($Documento);
    }else{
        //Ya existe un registro solo le agregaremos
        $id_nombre_comercialNuevo = Id_DeNombre($NombreNuevo_Empresa);

        $actualizar = 
        [
            '$addToSet' => [ 
                "otros_nombres" => $NombreNuevo_Empresa,
                "id_otros_nombres" => $id_nombre_comercialNuevo
            ]
        ];
        $Result = $collection2->updateOne($filtro, $actualizar);
    }


    /*$ArrayDeInsercion[] = [
        'empresa_receptora' => $NombreNuevo_Empresa,
        'empresa_emisora' => $NombreActual_Empresa,
        'id_empresasa_emisora' => $ID_EmpresaEmisora,
        'tipo_anuncio' => $Tipo_Anuncio
    ];
    $Documento = [
        'nombre_original' => $NombreActual_Empresa,
        'otros_nombres' => [
            $NombreActual_Empresa,
            $NombreNuevo_Empresa
        ]
    ];*/

    //$filtro = ["otros_nombres" => $NombreNuevo_Empresa];
    //$filtro = ["otros_nombres" => ['$elemMatch' => ['$eq' => $NombreNuevo_Empresa]]];

    /*$actualizar = 
    [
        '$set' => ["nombre_original" => $NombreActual_Empresa],
        '$addToSet' => [ "otros_nombres" => [$NombreNuevo_Empresa,$NombreActual_Empresa]]
    ];

    global $database;
    $collection = $database->cambios_nombres2;
    $Result1 = $collection->findOneAndUpdate($filtro, $actualizar, ['upsert' => true]);*/

    /*global $database;
    $collection = $database->cambios_nombres2;
    $Result = $collection->insertOne($Documento);*/
}

/*
function GuardarDatos(){
    global $ArrayDeInsercion;
    global $database;
    $collection = $database->cambios_nombres2;
    $Result = $collection->insertMany($ArrayDeInsercion);
    var_dump($Result);
}*/



function Trimear($Texto){
    if($Texto != NUll){
        $Texto = trim($Texto, ' ');
        $Texto = trim($Texto, '.');
        $Texto = trim($Texto, ':');
    }
    return $Texto;
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


$tiempo_fin = microtime(true);
$tiempo = $tiempo_fin - $tiempo_inicio;
echo "<br><br>";
echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);


?>