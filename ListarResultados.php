<?php
session_start();
/*$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');*/
include_once("app/conf.php");
require_once($root . 'Archivos de Ayuda PHP/conexion.php');


//$_SESSION["buscador"] = "BODEGAS VALLEGARCIA, SOCIEDAD LIMITADA";
if(!isset($_SESSION["buscador"]) || $_SESSION["buscador"] == ""){
    header('Location: index.php');
}

$NombreEmpresa = trim(mb_strtoupper($_SESSION["buscador"]), " ");
$id_nombre_empresa = Id_DeNombre($NombreEmpresa);

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->anuncios;

$QuienEncontro = NULL;
$Encontrado = 0;

//$filter = ["otros_nombres" => ['$regex' => $NombreEmpresa ]];
$filter = ["id_otros_nombres" => $id_nombre_empresa, 'eliminado' => ['$ne' => 1]];
$options = NULL;
//$options["nombre_comercial"] = 1;
$options["limit"] = 80;
$Result = $database->cambios_nombres->find($filter, $options)->toArray();


if(count($Result) != 0){
    $QuienEncontro = "OtrosNombres";
    $Encontrado = 1;
}else{
    $filter = ['id_nombre_comercial' =>  $id_nombre_empresa, 'eliminado' => ['$ne' => 1]];
    $options["limit"] = 80;
    //$options["projection"] = ['nombre_comercial' => 1];
    //$options = ["nombre_comercial" => 1, 'limit' => 50];
    $Result = $collection->find($filter, $options)->toArray();
    if(count($Result) != 0){
        $QuienEncontro = "NombreComercial";
        $Encontrado = 1;
    }else{
        //$filter = [ '$text' => [ '$search' => "\"$NombreEmpresa\"" ] ];
        $filter = [ '$text' => ['$search' => $NombreEmpresa ], 'eliminado' => ['$ne' => 1]];
        //$options["nombre_comercial"] = 1;
        $options["limit"] = 3000;
        $options["projection"] = [
            'nombre_comercial' => 1,
            'id_nombre_comercial' => 1
            //'score' => ['$meta' => "textScore"]
        ];
        //$options["projection"] = ['score' => ['$meta' => "textScore"]];
        //$options["sort"] = ["score" => ['$meta' => "textScore"]];
        $Result = $collection->find($filter, $options)->toArray();
        if(count($Result) != 0){

            $ArrayResult = NULL;
            foreach($Result as $res){
                $Nombre = $res["id_nombre_comercial"];
                if(!(strpos($Nombre, $id_nombre_empresa) === false)){
                    $ArrayResult[] = $res;
                }
            }
            if($ArrayResult != NULL){
                $Result = $ArrayResult;
            }
            
            $QuienEncontro = "NombreComercial";
            $Encontrado = 1;
        }
    }
}

function Id_DeNombre($Nombre){
    $IdNombre = str_replace(".", "", $Nombre);
    $IdNombre = str_replace(",", "", $IdNombre);
    $IdNombre = str_replace(" ", "", $IdNombre);
    return $IdNombre;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta id="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/ListarResultados.css">
</head>
<body>
<?php include 'templates/encabezado.php'; ?>

<div class="divBackGround">

    <div class="DivBusquedas">

    <table>
        <tbody>

    <?php

    if($Encontrado == 1){
        if($QuienEncontro == "OtrosNombres"){
            echo count($Result) . " resultados<br><br>";

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

        if($QuienEncontro == "NombreComercial"){
            if(count($Result) != 0){
                $ArraAgrupado = NULL;
                foreach($Result as $res){
                    if(isset($res["sucursal"])){
                        $nombre = $res["nombre_comercial"] . " (" . $res["sucursal"] . ")";
                        $nombre = Id_DeNombre($nombre);
                        $ArraAgrupado[$nombre] = $res;
                    }else{
                        $nombre = Id_DeNombre($res["nombre_comercial"]);
                        $ArraAgrupado[$nombre] = $res;
                    }
                }
        
                echo count($ArraAgrupado) . " resultados<br><br>";
        
                
        
                foreach($ArraAgrupado as $key=>$res){
                    $id = strval($res["_id"]);
                    $nombre_comercial = $res["nombre_comercial"];
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
            }
        }
    }else{
        echo "No se encontraron resultados<br><br>";
    }
    ?>

        </tbody>
    </table>

    </div>

</div>



<?php include 'templates/footer.php'; ?>
</body>
</html>