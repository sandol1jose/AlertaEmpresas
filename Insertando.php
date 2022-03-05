<?php

require 'Archivos de Ayuda PHP/conexion.php';

$buscador = ["nombre_comercial" => "ALBATERAPIA CUERPO Y MENTE SOCIEDAD LIMITADA"];

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
];

$document2 = 
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

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;
$Result = $collection->find($buscador)->toArray();


if(count($Result) == 1){
    $Result = $collection->updateOne($buscador, $document2);
    if($Result){
        echo "El archivo se modifico correctamente";
    }else{
        echo "El archivo NO se modifico correctamente";
    }
}else{
    echo "No se encontro ningun archivo";
}

?>