<?php

//require 'Archivos de Ayuda PHP/conexion.php';
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');


$NombreEmpresa = strtoupper($_POST["buscador"]);

//$filter = [ '$text' => [ '$search' => "\"$NombreEmpresa\"" ] ];
$filter = [ '$or' => [
    ["nombre_comercial" => ['$regex' => $NombreEmpresa ]],
    ["denominaciones_sociales" => ['$regex' => $NombreEmpresa ]]
    ],
    "activo" => ['$ne' => 0 ] 
    
];
$options = ["nombre_comercial" => 1, 'limit' => 100];

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;
$Result = $collection->find($filter, $options)->toArray();

echo count($Result) . " resultados...<br><br>";
if(count($Result) != 0){
    foreach($Result as $res){
        $id = strval($res["_id"]);
        $nombre_comercial = strval($res["nombre_comercial"]);
        echo $id . "<br>";
        echo "<a href='MostrarEmpresa.php?id=".$id."&name=".$nombre_comercial."'>" . $nombre_comercial . "</a>" . "<br>";
        
        if(isset($res["denominaciones_sociales"])){
            echo "Anteriormente llamada: ";
            foreach($res["denominaciones_sociales"] as $nombre){
                echo $nombre . '<br>';
            }
        }
        echo "<br>";
    
    
    }
}
?>