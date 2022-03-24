<?php
session_start();//inicio de sesion
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');

$id_Empresa = $_GET["id"];
//$id_Empresa = "6231592e6ec53be0065866a6";

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;

$numero_borme;
$fecha_borme;
$Directivos = null;
$DocumentoEntero;
$Anuncios_Borme;

Consultar();

function Consultar(){
    global $collection;
    global $Directivos;
    global $DocumentoEntero;
    global $Anuncios_Borme;
    global $id_Empresa;

    $filter = [ "_id" => new MongoDB\BSON\ObjectID($id_Empresa) ];
    $Result = $collection->find($filter, ["anuncio_borme" => 0, 'typeMap' => ['array' => 'array']])->toArray();
    //$Result = $collection->find($filter, ["anuncio_borme" => 0])->toArray();

    foreach($Result as $res){
        if(isset($res->Directivos)){
            $Directivos = $res->Directivos;
            $Directivos = OrdenarArray($Directivos);
        }
        
        $DocumentoEntero = json_decode(json_encode($res), true);
        $Anuncios_Borme = array_reverse(json_decode(json_encode($res->anuncio_borme), true));
    }

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

	<script src="js/general.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <title>Document</title>

    <style>
        table, tr, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>

    <h3>Empresa</h3>
    <h1><?php if(isset($DocumentoEntero["nombre_comercial"])) 
    echo $DocumentoEntero["nombre_comercial"]; ?></h1><br>

    <span>Datos de la empresa</span><br><br><br>
    
    <span>Nombre Comercial: </span>
    <span><?php if(isset($DocumentoEntero["nombre_comercial"])) 
    echo  $DocumentoEntero["nombre_comercial"]?></span><br><br>

    <span>Objeto social: </span>
    <span><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Objeto social"])) 
    echo  $DocumentoEntero["Constitucion"]["datos"]["Objeto social"]?></span><br><br>
    
    <span>Capital social: </span>
    <span><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Capital"])) 
    echo  $DocumentoEntero["Constitucion"]["datos"]["Capital"]?></span><br><br>
    
    <span>Dirección: </span>
    <span><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Domicilio"])) 
    echo  $DocumentoEntero["Constitucion"]["datos"]["Domicilio"]?></span><br><br>

    <span>Fecha constitución: </span>
    <span><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Comienzo de operaciones"])) 
    echo  $DocumentoEntero["Constitucion"]["datos"]["Comienzo de operaciones"]?></span><br><br>


<span name="btn_alertas" id="btn_alertas"></span>

<?php

function VerificarSeguimiento(){
    global $database;
    global $id_Empresa;

    if(isset($_SESSION["Cliente"])){
        $collection = $database->Clientes;
        $filter = ["_id" => new MongoDB\BSON\ObjectID($_SESSION["Cliente"]["IDCliente"])];
        $Result2 = $collection->findOne($filter);

        $position = -1;
        $encontrado = false;
        if(isset($Result2["alertas"])){
            $alertas = $Result2["alertas"];
            foreach($alertas as $alerta){
                $position++;
                if($alerta["id_empresa"] == $id_Empresa){
                    if($alerta["estado"] == true){
?>
                        <script>
                            //No Seguir empresa
                            document.getElementById("btn_alertas").innerHTML = "<button onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo $position; ?>',2);\">Desactivar Notificacion</button>";
                        </script>
<?php
                    }else{
?>
                        <script>
                            //seguir empresa
                            document.getElementById("btn_alertas").innerHTML = "<button onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo $position; ?>',1);\">Activar Notificacion</button>";
                        </script>
<?php
                    }  
                    $encontrado = true;
                    break;
                }
            }

            if($encontrado == false){
?>   
                <script>
                    //seguir empresa
                    document.getElementById("btn_alertas").innerHTML = "<button onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo -1; ?>', 1);\">Activar Notificacion</button>";
                </script>
<?php
            }

        }else{
?>
            <script>
                //seguir empresa
                document.getElementById("btn_alertas").innerHTML = "<button onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo $position; ?>', 1);\">Activar Notificacion</button>";
            </script>
<?php
        }
?>
        
<?php 
    } 
}

VerificarSeguimiento();
?>


    <br><br>
    <table>
        <tr>
            <th>Entidad</th>
            <th>Relacion</th>
            <th>Desde</th>
            <th>Hasta</th>
        </tr>

    <?php 
        if($Directivos != null){
            foreach($Directivos as $directivo){ 
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
    <?php 
            } 
        }
    ?>

    </table>



    <h3>Anuncion en Boletín Oficial (BORME)</h3>

    <table>
    <?php foreach($Anuncios_Borme as $anuncio){ ?>
        
            <tr>
                <td><?php 
                $fecha = $anuncio["fecha"]['$date']['$numberLong'];
                echo date("Y-m-d", strval($fecha)/1000);
                ?></td>
                <td><?php echo $anuncio["anuncio"]; ?></td>
            </tr>
        
    <?php } ?>
    </table>
</body>
</html>



<script>
    function SeguirEmpresa(idEmpresa, Correo, position, tipo){
        //tipo = 1 seguir; 2 dejar de seguir
        $.ajax({
			type: "POST",
			url: "app/SeguirEmpresa.php",
			data: {'idEmpresa': idEmpresa, 'Correo': Correo, 'position': position, 'tipo': tipo},
			dataType: "html",
			beforeSend: function(){
                //console.log("Estamos procesando los datos... ");
			},
			error: function(){
				console.log("error petición ajax");
			},
			success: function(data){
                if(data != "0"){
                    document.getElementById("btn_alertas").innerHTML = data;
                    if(tipo == 1){
                        alertsweetalert2('Se han activado las notificaciones para ésta empresa', '', 'success');
                    }else{
                        alertsweetalert2('Se han desactivado las notificaciones para ésta empresa', '', 'info');
                    }
                }else{
                    alertsweetalert2('No se pudieron activar las notificaciones', '', 'error');
                }
			}
		});
    }
</script>