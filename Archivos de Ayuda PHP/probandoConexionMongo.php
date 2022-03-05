<?php

require 'conexion.php';
/*
$document = array( "PrimerNombre" => 'Jose', 
'SegundoNombre' => 'Antonio', 
'Pais' => 'Guatemala', 
'Nacimiento' => 1995, 
'Sexo' => 'Masculino',
'Direccion' => array(
    'Calle' => "5 calle 'I' 2-84 zona 3 colonia Banvi",
    'Ciudad' => 'Chiquimula'
)
);

$document = 
[
    "nombre_comercial" => "ALBATERAPIA CUERPO Y MENTE SOCIEDAD LIMITADA",
    "anuncio_borme" => [
        [
            "numero" => 57448,
            "fecha" => "10/02/2022",
            "tipo" => "Ampliación de capital",
            "anuncio" => "Ampliación de capital.  Capital: 200.133,00 Euros. Resultante Suscrito: 4.561.229,40 Euros.  Datos registrales. T 910, L 674, F 175,S 8, H AB 15743, I/A 10 ( 1.02.22)"
        ]
    ]
];*/


$buscador = ["nombre_comercial" => "ALBATERAPIA CUERPO Y MENTE SOCIEDAD LIMITADA"];

$document = 
[ 
    '$addToSet' => 
    [ "anuncio_borme" =>
        [
            "numero" => 57447,
            "fecha" => "09/02/2022",
            "tipo" => "Ceses/Dimisiones",
            "anuncio" => "Ceses/Dimisiones.  Liquidador M: RODRIGUEZ NAVARRO MARIA JOSE;LEON MARQUEZ MARIA DEL PILAR. Adm. Solid.:RODRIGUEZ NAVARRO MARIA JOSE;LEON MARQUEZ MARIA DEL PILAR.  Nombramientos.  Liquidador M: RODRIGUEZNAVARRO MARIA JOSE;LEON MARQUEZ MARIA DEL PILAR.  Disolución.  Voluntaria.  Extinción.  Datos registrales. T 934, L698, F 109, S 8, H AB 23525, I/A 2 ( 1.02.22)"
        ]  
    ]
];
/*
$conexion = new Conexion();
$database = $conexion->Conectar();
$Result = $conexion->Update($buscador, $document2, "empresas", $database);
*/
/*
$document2 = 
    ["nombre_comercial" => "ALBATERAPIA CUERPO Y MENTE SOCIEDAD LIMITADA"]
;

$opciones =
    [
        "nombre_comercial" => 1, 
        "_id" => 0
    ];*/

    /*

$conexion = new Conexion();
$database = $conexion->Conectar();
//$Result = $conexion->Buscar($document2, "", "usuario", $database);
$Result = $conexion->Update($buscador, $document, "empresas", $database);
var_dump($Result);


//Upsert
$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;
$Result = $collection->replaceOne($buscador, $document, ['multi' => false, 'upsert' => true]);
var_dump($Result);


//Upsert
$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;
$Result = $collection->updateMany($buscador, $document);
var_dump($Result);
*/


$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;
$Result = $collection->find()->toArray();
//var_dump($Result);

foreach($Result as $res){
    $id = strval($res["_id"]);
    $nombre_comercial = strval($res["nombre_comercial"]);
    $anuncio_borme = $res["anuncio_borme"];

    echo "_id: " . $id . "<br>";
    echo "nombre_comercial: " . $nombre_comercial . "<br>";
    foreach($anuncio_borme as $anuncio){
        echo strval($anuncio["numero"]) . "<br>";
        echo strval($anuncio["fecha"]) . "<br>";
        echo strval($anuncio["tipo"]) . "<br>";
        echo strval($anuncio["anuncio"]) . "<br><br>";
    }
}


/*
foreach($Result as $res){
    $id = strval($res["_id"]);
    $Nombre = strval($res["name"]);
    $apellido = strval($res["apellido"]);

    echo $id . "<br>";
    echo $Nombre . "<br>";
    echo $apellido . "<br><br>";
}*/
?>