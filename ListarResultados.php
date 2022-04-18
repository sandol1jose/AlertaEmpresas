<?php
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');


if(!isset($_GET["buscador"]) || $_GET["buscador"] == ""){
    header('Location: index.php');
}
$NombreEmpresa = $_GET["buscador"];
//$NombreEmpresa = "Permant 2008";
//$NombreEmpresa = "ES";

/*$filter = [
    ['$match' => ['$text' => [ '$search' => "\"$NombreEmpresa\"" ]]],
    ['$group' => ['_id' => '$nombre_comercial']]
];*/


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
$collection = $database->anuncios;

$filter = [ 'nombre_comercial' => $NombreEmpresa ];
$options = ["nombre_comercial" => 1, 'limit' => 50];
$Result = $collection->find($filter, $options)->toArray();

if(count($Result) == 0){
    $filter = [ '$text' => [ '$search' => "\"$NombreEmpresa\"" ] ];
    $options = ["nombre_comercial" => 1, 'limit' => 50];
    $Result = $collection->find($filter, $options)->toArray();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Documentos</title>

    <link rel="stylesheet" type="text/css" href="css/ListarResultados.css">
</head>
<body>
<?php include 'templates/encabezado.php'; ?>

<div class="DivBusquedas">

<table>
    <tbody>

<?php
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

    echo count($ArraAgrupado) . " resultados<br><br>";

    

    foreach($ArraAgrupado as $key=>$res){
        $id = strval($res["_id"]);
        $nombre_comercial = $key;
        //echo $id . "<br>";
        echo "<tr>";
        $href = "";
        if(isset($res["sucursal"])){
            $sucursal = $res["sucursal"];
            $href = "MostrarEmpresa.php?id=".$id."&sucursal=".$sucursal."";
            echo "
                <td>
                    <a href='".$href."'>" . mb_convert_case($nombre_comercial, MB_CASE_TITLE, "UTF-8") . "</a>" . "
                </td>";
        }else{
            $href = "MostrarEmpresa.php?id=".$id."";
            echo "
                <td>
                    <a href='".$href."'>" . mb_convert_case($nombre_comercial, MB_CASE_TITLE, "UTF-8") . "</a>" . "
                </td>";
        }
        echo "
            <td class='tdBoton'>
                <button class='botonVer'  onclick='window.location.href=\"".$href."\"'>Ver</button>
            </td>";
        echo "</tr>";
        //echo "<br>";
    }
}else{
    $filter = ["otros_nombres" => ['$regex' => $NombreEmpresa ]];
    $Result = $database->cambios_nombres->find($filter, $options)->toArray();
    echo count($Result) . " resultados<br><br>";
    if(count($Result) != 0){
        foreach($Result as $res){
            echo "<tr>";
            $id = strval($res["idempresa_original"]);
            $nombre_comercial = strval($res["otros_nombres"][count($res["otros_nombres"])-1]);
            //echo $id . "<br>";
            $href = "MostrarEmpresa.php?id=".$id."";
            echo "<td>";
            echo "<a href='".$href."'>" . ucwords(strtolower($nombre_comercial)) . "</a>";
            
            echo "<div class='OtrasDenominaciones'>";
            echo "<b>Anteriormente llamada:</b> <br>";
            foreach($res["otros_nombres"] as $nombre){
                if($nombre != $nombre_comercial){
                    echo ucwords(strtolower($nombre)) . '<br>';
                }
            }
            echo "</div>";
            echo "</td>";
            echo "
            <td class='tdBoton'>
                <button class='botonVer'  onclick='window.location.href=\"".$href."\"'>Ver</button>
            </td>";
        echo "</tr>";
        }
    }
}
?>

    </tbody>
</table>

</div>



<?php include 'templates/footer.php'; ?>
</body>
</html>