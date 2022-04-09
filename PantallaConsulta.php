<?php
session_start();//inicio de sesion
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');

$id_Empresa = $_GET["id"];
//$id_Empresa = "62509df865260000c00a685e";

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;

$numero_borme;
$fecha_borme;
$Directivos = null;
$DocumentoEntero;
$Anuncios_Borme;

try{
    $filter = [ "_id" => new MongoDB\BSON\ObjectID($id_Empresa) ];
}catch(Exception $e){
    header('Location: index.php');
}

//$Result = $collection->find($filter, ["anuncio_borme" => 0, 'typeMap' => ['array' => 'array']])->toArray();
$Result = $collection->findOne($filter, ["anuncio_borme" => 0]);

if($Result == NULL){
    header('Location: index.php');
}
$Result = iterator_to_array($Result);
if(isset($Result['Directivos'])){
    $Directivos = $Result['Directivos'];
    $Directivos = OrdenarArray($Directivos);
}

$DocumentoEntero = $Result;
$Sucursal = "";
if(isset($DocumentoEntero["sucursal"])){
    $Sucursal = " (" . $DocumentoEntero["sucursal"] . ")";
}

//$Anuncios_Borme = array_reverse(json_decode(json_encode($res->anuncio_borme), true));
$Anuncios_Borme = NULL;
if($_SESSION['AnuncioBorme']["id"] == $id_Empresa){
    $Anuncios_Borme = $_SESSION['AnuncioBorme']["anuncio_borme"];
}
//$Anuncios_Borme = array_reverse(iterator_to_array($Result['anuncio_borme']));
$Anuncios_Borme = array_reverse($Anuncios_Borme);


//Funcion que ordena el array por fecha
function OrdenarArray($Array){
    $Array = iterator_to_array($Array);
    foreach ($Array as $key => $row) {
        $aux[$key] = intval(strval($row->fecha));
    }
    array_multisort($aux, SORT_ASC, $Array);
    return $Array;
}
?>

<?php include 'templates/encabezado.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="css/PantallaConsulta.css">

    <title>Document</title>


</head>
<body>

<div class="DivTituloGrande">
    <div class="TituloEmpresa">
        <img src="imagenes/edificio.png" alt="" width="18px">
        <span style="color: white">Empresa</span><br>
        <span class="Titulo2"><?php echo $DocumentoEntero["nombre_comercial"] . $Sucursal; ?></span>
    </div>
</div>


<div class="grid-container">
    <div>
        <div class="divDatosEmpresa">
            <div class="divSubtitulo2">
                <span class="Subtitulo2">Datos de la empresa</span><br>
            </div>
            
            <span class="txtTitulo"><b>Nombre Comercial: </b></span>
            <span class="txtparrafo"><?php if(isset($DocumentoEntero["nombre_comercial"])) 
            echo  $DocumentoEntero["nombre_comercial"] . $Sucursal?></span><br><br>

            <span class="txtTitulo"><b>Objeto social: </b></span>
            <span class="txtparrafo"><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Objeto social"])) 
            echo  "<br>" . $DocumentoEntero["Constitucion"]["datos"]["Objeto social"];?></span><br><br>
            
            <span class="txtTitulo"><b>Capital social: </b></span>
            <span class="txtparrafo"><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Capital"])) 
            echo  $DocumentoEntero["Constitucion"]["datos"]["Capital"]?></span><br><br>
            
            <span class="txtTitulo"><b>Dirección: </b></span>
            <span class="txtparrafo"><?php if(isset($DocumentoEntero["Constitucion"]["datos"]["Domicilio"])) 
            echo  $DocumentoEntero["Constitucion"]["datos"]["Domicilio"]?></span><br><br>

            <span class="txtTitulo"><b>Provincia: </b></span>
            <span class="txtparrafo"><?php if(isset($DocumentoEntero["provincia"])) 
            echo  $DocumentoEntero["provincia"]?></span><br><br>

            <span class="txtTitulo"><b>Fecha constitución: </b></span>
            <span class="txtparrafo">
                <?php 
                    if(isset($DocumentoEntero["Constitucion"]["datos"]["Comienzo de operaciones"])){
                        $Fecha = $DocumentoEntero["Constitucion"]["datos"]["Comienzo de operaciones"];
                        if($Fecha != "AUTORIZACION POLICIAL"){//Si no AUTORIZACION POLICIAL
                            //$Fecha = $DocumentoEntero["Constitucion"]["datos"]["Comienzo de operaciones"];
                            $Fecha = DateTime::createFromFormat('d.m.y', $Fecha)->format('d/m/Y');
                            echo $Fecha;
                        }else{
                            $Fecha = $DocumentoEntero["Constitucion"]["fecha_incripcion"];
                            $Fecha = date("d/m/Y", strval($Fecha)/1000);
                            //$Fecha = DateTime::createFromFormat('d.m.y', $Fecha)->format('d/m/Y');
                            echo $Fecha;
                        }
                    }
                ?>
            </span><br><br>

            <span name="btn_alertas" id="btn_alertas">
                <?php VerificarSeguimiento(); ?>
            </span>

        </div>
    </div>

    <div class="divDirectivos">
        <div class="divSubtitulo2">
            <span class="Subtitulo2">Cargos Directivos</span><br>
        </div>

        <center>
        <table class="tablaDirectivos">
            <tr>
                <th style="text-align: left;">Entidad</th>
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
                    <td class="tdDirect"><?php echo ucwords(strtolower($datos->entidad)); ?></td>
                    <td class="tdDirect tdcenter"><?php echo $datos->relacion; ?></td>
                    <?php 
                        $fecha_formateada = "";
                        if(isset($datos->desde) && $datos->desde != ""){
                            $fecha_formateada = date("d/m/Y", strval($datos->desde)/1000); 
                        }
                        ?>
                    <td class="tdDirect tdcenter"><?php echo $fecha_formateada; ?></td>
                    <?php
                        $fecha_formateada = "";
                        if(isset($datos->hasta) && $datos->hasta != ""){
                            $fecha_formateada = date("d/m/Y", strval($datos->hasta)/1000);
                        }
                    ?>
                    <td class="tdDirect tdcenter"><?php echo $fecha_formateada; ?></td>
                </tr>
        <?php 
                } 
            }
        ?>

        </table>
        </center>
    </div>
</div>

<?php
    /*function VerificarSeguimiento(){
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
                                document.getElementById("btn_alertas").innerHTML = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo $position; ?>',2);\">Desactivar Notificacion</button>";
                            </script>
    <?php
                        }else{
    ?>
                            <script>
                                //seguir empresa
                                document.getElementById("btn_alertas").innerHTML = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo $position; ?>',1);\">Activar Notificacion</button>";
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
                        document.getElementById("btn_alertas").innerHTML = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo -1; ?>', 1);\">Activar Notificacion</button>";
                    </script>
    <?php
                }

            }else{
    ?>
                <script>
                    //seguir empresa
                    document.getElementById("btn_alertas").innerHTML = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('<?php echo $id_Empresa; ?>', '<?php echo $_SESSION["Cliente"]["Correo"]; ?>', '<?php echo $position; ?>', 1);\">Activar Notificacion</button>";
                </script>
    <?php
            }
    ?>
            
    <?php 
        } 
    }
    */
?>


<div class="divAnuncios">
    <div class="divSubtitulo2">
        <span class="Subtitulo2">Anuncios en Boletín Oficial (BORME)</span><br>
    </div>

    <table>
    <?php foreach($Anuncios_Borme as $anuncio){ ?>
            <tr>
                <td class="tdFecha">
                    <?php 
                    setlocale(LC_ALL, 'es_ES');
                    //$fecha = $anuncio["fecha"]['$date']['$numberLong'];
                    $fecha = $anuncio["fecha"];
                    echo date("d/m/Y", strval($fecha)/1000);
                    ?>
                </td>
                <td class="tdImagen"><img src="imagenes/Ellipse 1.png" alt="" width="18px"></td>
                <td>
                    <div class="divAnuncio">
                        <span class="txtTipo"><?php echo $anuncio["tipo"] . '<br><br>'; ?></span>
                        <?php 
                            echo $anuncio["anuncio"] . '<br><br>';
                            $link = "https://www.boe.es/borme/dias/" . date("Y/m/d", strval($fecha)/1000) . "/pdfs/" . $anuncio["borme"] . ".pdf";
                        ?>
                        <a class="aVerDocumento" href="<?php echo $link; ?>" target="_blank">Ver documento
                         <b> (<?php echo $anuncio["numero"]; ?>) </b></a>
                    </div>
                </td>
            </tr>
        
    <?php } ?>
    </table>

</div>



<?php include 'templates/footer.php'; ?>


</body>
</html>

<?php

function VerificarSeguimiento(){
    global $database;
    global $id_Empresa;
    $Retorno = NULL;

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
                        $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('" . $id_Empresa . "','" . $_SESSION["Cliente"]["Correo"] . "','" . $position . "', 2);\">Desactivar Notificacion</button>";
                    }else{
                        $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('" . $id_Empresa . "','" . $_SESSION["Cliente"]["Correo"] . "','" . $position . "', 1);\">Activar Notificacion</button>";
                    }
                    $encontrado = true;
                    break;
                }
            }

            if($encontrado == false){
                $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('" . $id_Empresa . "','" . $_SESSION["Cliente"]["Correo"] . "','" . -1 . "', 1);\">Activar Notificacion</button>";
            }

        }else{
            $Retorno = "<button class='BotonGeneral' onClick=\"SeguirEmpresa('". $id_Empresa ."','". $_SESSION["Cliente"]["Correo"] ."','". $position ."', 1);\">Activar Notificacion</button>";
        }
    }
    
    echo $Retorno;
}


?>





<script>
    function SeguirEmpresa(idEmpresa, Correo, position, tipo){
        //tipo = 1 seguir; 2 dejar de seguir
        console.log(idEmpresa);
        console.log(Correo);
        console.log(position);
        console.log(tipo);
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