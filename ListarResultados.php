<?php

$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');

if(!isset($_POST["buscador"]) || $_POST["buscador"] == ""){
    header('Location: index.php');
}

$NombreEmpresa = strtoupper($_POST["buscador"]);
//$NombreEmpresa = "AGROGENESIS";

/*$filter = [
    ['$match' => ['$text' => [ '$search' => "\"$NombreEmpresa\"" ]]],
    ['$group' => ['_id' => '$nombre_comercial']]
];*/

$filter = [ '$text' => [ '$search' => "\"$NombreEmpresa\"" ] ];
$options = ["nombre_comercial" => 1, 'limit' => 100];
/*$filter = [ '$or' => [
    ["nombre_comercial" => ['$regex' => $NombreEmpresa ]],
    ["denominaciones_sociales" => ['$regex' => $NombreEmpresa ]]
    ],
    "activo" => ['$ne' => 0 ] 
];
*/

//$filter = ["nombre_comercial" => ['$regex' => $NombreEmpresa ]];

$conexion = new Conexion();
$database = $conexion->Conectar();
//$collection = $database->empresas;
$collection = $database->anuncios;
$Result = $collection->find($filter, $options)->toArray();

if(count($Result) != 0){
    $ArraAgrupado = NULL;
    foreach($Result as $res){
        if(isset($res["sucursal"])){
            $nombre = $res["nombre_comercial"] . " (" . $res["sucursal"] . ")";
            $ArraAgrupado[$nombre] = $res;
        }else{
            $ArraAgrupado[$res["nombre_comercial"]] = $res;
        }
    }

    echo count($ArraAgrupado) . " resultados...<br><br>";

    foreach($ArraAgrupado as $key=>$res){
        $id = strval($res["_id"]);
        $nombre_comercial = $key;
        echo $id . "<br>";
        if(isset($res["sucursal"])){
            $sucursal = $res["sucursal"];
            echo "<a href='MostrarEmpresa.php?id=".$id."&sucursal=".$sucursal."'>" . $nombre_comercial . "</a>" . "<br>";
        }else{
            echo "<a href='MostrarEmpresa.php?id=".$id."'>" . $nombre_comercial . "</a>" . "<br>";
        }
        echo "<br>";
    }
}else{
    $filter = ["otros_nombres" => ['$regex' => $NombreEmpresa ]];
    $Result = $database->cambios_nombres->find($filter)->toArray();
    echo count($Result) . " resultados...<br><br>";
    if(count($Result) != 0){
        foreach($Result as $res){
            $id = strval($res["idempresa_original"]);
            $nombre_comercial = strval($res["otros_nombres"][count($res["otros_nombres"])-1]);
            echo $id . "<br>";
            echo "<a href='MostrarEmpresa.php?id=".$id."'>" . $nombre_comercial . "</a>" . "<br>";
            
            echo "Anteriormente llamada: <br>";
            foreach($res["otros_nombres"] as $nombre){
                if($nombre != $nombre_comercial){
                    echo $nombre . '<br>';
                }
            }
            echo "<br>";
        }
    }
}
?>