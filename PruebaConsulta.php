<?php
require 'Archivos de Ayuda PHP/conexion.php';


$id = "6211d15916dcda4b0a52e0c5";
//$id = "62225db427ea67c18a4ee41a";
$filter = [ "_id" => new MongoDB\BSON\ObjectID($id) ];

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas2;
$collection2 = $database->empresas2;
$Result = $collection->find($filter, ["anuncio_borme" => 0, 'typeMap' => ['array' => 'array']])->toArray();
//$Result = $collection->find($filter, ["anuncio_borme" => 0])->toArray();

$numero_borme;
$fecha_borme;
$Directivos;
$DocumentoEntero;
$Anuncios_Borme;
foreach($Result as $res){
    $Directivos = $res->Directivos;
    $Directivos = OrdenarArray($Directivos);
    $DocumentoEntero = json_decode(json_encode($res), true);
    $Anuncios_Borme = array_reverse(json_decode(json_encode($res->anuncio_borme), true));
}

//Funcion que ordena el array por fecha
function OrdenarArray($Array){
    foreach ($Array as $key => $row) {
        $aux[$key] = intval(strval($row->fecha));
    }
    array_multisort($aux, SORT_ASC, $Array);
    return $Array;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        table, tr, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>

    <h3>Empresa</h3>
    <h1><?php echo  $DocumentoEntero["nombre_comercial"]?></h1><br>

    <span>Datos de la empresa</span><br><br><br>
    
    <span>Nombre Comercial: </span>
    <span><?php echo  $DocumentoEntero["nombre_comercial"]?></span><br><br>

    <span>Objeto social: </span>
    <span><?php echo  $DocumentoEntero["Constitucion"]["datos"]["Objeto social"]?></span><br><br>
    
    <span>Capital social: </span>
    <span><?php echo  $DocumentoEntero["Constitucion"]["datos"]["Capital"]?></span><br><br>
    
    <span>Dirección: </span>
    <span><?php echo  $DocumentoEntero["Constitucion"]["datos"]["Domicilio"]?></span><br><br>

    <span>Fecha constitución: </span>
    <span><?php echo  $DocumentoEntero["Constitucion"]["datos"]["Comienzo de operaciones"]?></span><br><br>

    <br><br>
    <table>
        <tr>
            <th>Entidad</th>
            <th>Relacion</th>
            <th>Desde</th>
            <th>Hasta</th>
        </tr>

    <?php foreach($Directivos as $directivo){ 
        $datos = $directivo->datos;
        ?>
        <tr>
            <td><?php echo $datos->entidad; ?></td>
            <td><?php echo $datos->relacion; ?></td>
            <?php 
                $fecha_formateada = "";
                if(isset($datos->desde) && $datos->desde != ""){
                    $fecha_formateada = date("d/m/Y", strval($datos->desde)/1000); 
                }
                ?>
            <td><?php echo $fecha_formateada; ?></td>
            <?php
                $fecha_formateada = "";
                if(isset($datos->hasta) && $datos->hasta != ""){
                    $fecha_formateada = date("d/m/Y", strval($datos->hasta)/1000);
                }
             ?>
            <td><?php echo $fecha_formateada; ?></td>
        </tr>
    <?php } ?>

    </table>



    <h3>Anuncion en Boletín Oficial (BORME)</h3>

    <table>
    <?php foreach($Anuncios_Borme as $anuncio){ ?>
        
            <tr>
                <td><?php 
                $fecha = $anuncio["fecha"]['$date']['$numberLong'];
                echo date("Y-m-d H:i:s", strval($fecha)/1000);
                ?></td>
                <td><?php echo $anuncio["anuncio"]; ?></td>
            </tr>
        
    <?php } ?>
    </table>
</body>
</html>