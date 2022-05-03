<?php
session_start();//inicio de sesion
if(!isset($_SESSION["Cliente"])){
    header('Location: ../Login/index.php'); //Agregamos el producto
}
?>

<?php
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->Clientes;

$filter = [ "_id" => new MongoDB\BSON\ObjectID($_SESSION["Cliente"]["IDCliente"]) ];
$Result = $collection->findOne($filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="../css/switch.css">
</head>
<body>

<?php include '../templates/encabezado.php'; ?>

<div class="DivTituloGrande">
    <span class="Titulo2">Bienvenido a tu cuenta</span>
</div>



    <?php
    	if(isset($_SESSION["Cliente"])){?>
        <div class="nombre">
            <span><?php echo $_SESSION["Cliente"]["Nombres"] . " " . $_SESSION["Cliente"]["Apellidos"]; ?></span>
        </div>
<?php } ?>
    


<div class="divSubtitulo3 divSubtitulo2">
    <span class="Subtitulo2">Empresas que sigues</span>
</div>
    
<div class="divTabla">

    <?php
    if(isset($Result["alertas"])){
    ?>

        <table>
            <tr>
                <th>Estado</th>
                <th>Empresa</th>
                <th>Acción</th>
            </tr>

    <?php
    $Position = 0;
    foreach($Result["alertas"] as $alerta){
        $collection = $database->empresas;
        $id_empresa = $alerta["id_empresa"];
        $filter = [ "_id" => new MongoDB\BSON\ObjectID($id_empresa) ];
        $Result = $collection->findOne($filter);
        $Nombre_empresa = $Result["nombre_comercial"];
        $Correo = $_SESSION["Cliente"]["Correo"];

        $sucursal = "";
        $sucursal2 = "";
        if(isset($Result["sucursal"])){
            $sucursal = "&sucursal=" . $Result["sucursal"];
            $sucursal2 = " (" . $Result["sucursal"] . ")";
        }

        $check = '';
        if($alerta["estado"] == true){
            $check = 'checked';
        }
    ?>
            <tr id="tr_<?php echo $id_empresa; ?>">
                <td class="tdCheck">
                    <label class="switch">
                        <input id="check<?php echo $id_empresa; ?>" type="checkbox" <?php echo $check; ?> 
                        onClick="SeguirEmpresa('<?php echo $id_empresa; ?>', '<?php echo $Correo; ?>', '<?php echo $Position; ?>');">
                    <span class="slider round"></span>    
                    </label>
                </td>
                <td class="tdNombreEmpresa">
                    <a href="../MostrarEmpresa.php?id=<?php echo $id_empresa; echo $sucursal; ?>">
                        <?php echo $Nombre_empresa . $sucursal2; ?>
                    </a>
                </td>
                <td class="tdButton">
                    <button class="btnEliminar" onClick="EliminarAlerta('<?php echo $id_empresa; ?>', '<?php echo $Correo; ?>')">Eliminar</button>
                </td>
            </tr>
    <?php
    $Position++;
    }?>

        </table>

        <span class="txtExtra">Éstas son las empresas de las que recibirás alertas en cuanto haya un cambio</span>

    <?php }else{ ?>

        <span>No sigues a ninguna empresa</span><br><br>
        <button class="BotonGeneral" onclick="window.location.href='../index.php'">Buscar Empresas</button>

    <?php } ?>

</div>




<?php include '../templates/footer.php'; ?>



</body>
</html>


<script>
    function SeguirEmpresa(idEmpresa, Correo, position){
        //tipo = 1 seguir; 2 dejar de seguir
        var tipo = $('#check'+ idEmpresa).prop('checked');
        if (tipo) {
            tipo = 1;
        }else{
            tipo = 2;
        }
        $.ajax({
			type: "POST",
			url: "../app/SeguirEmpresa.php",
			data: {'idEmpresa': idEmpresa, 'Correo': Correo, 'position': position, 'tipo': tipo, 'desde': "cuenta"},
			dataType: "html",
			beforeSend: function(){
                //console.log("Estamos procesando los datos... ");
			},
			error: function(){
				console.log("error petición ajax");
			},
			success: function(data){
                if(data != "0"){
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

    function EliminarAlerta(idEmpresa, Correo){
        console.log(idEmpresa);
        console.log(Correo);
        
        $.ajax({
			type: "POST",
			url: "../app/EliminarAlerta.php",
			data: {'idEmpresa': idEmpresa, 'Correo': Correo},
			dataType: "html",
			beforeSend: function(){
                //console.log("Estamos procesando los datos... ");
			},
			error: function(){
				console.log("error petición ajax");
			},
			success: function(data){
                console.log(data);
                if(data == "1"){
                    $("#tr_" + idEmpresa).remove();
                    alertsweetalert2('Eliminaste las alertas de la empresa', '', 'success');
                }
			}
		});
    }
</script>