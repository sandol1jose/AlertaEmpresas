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

    <script src="../js/general.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Cuenta</title>
</head>
<body>

    <?php
    	if(isset($_SESSION["Cliente"])){?>
        <a href="../Login/LogOut.php">Salir</a><br>

        <a><?php echo $_SESSION["Cliente"]["Nombres"] . " " . $_SESSION["Cliente"]["Apellidos"]; ?></a>
    <?php } ?>
    

    <br><br>
    <span>Empresas que sigues</span><br><br>

<?php
if(isset($Result["alertas"])){
?>
    <table>
        <tr>
            <th>Estado</th>
            <th>Empresa</th>
            <th>Alertas</th>
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

    $check = '';
    if($alerta["estado"] == true){
        $check = 'checked';
    }
?>
        <tr>
            <td><input id="check<?php echo $id_empresa; ?>" type="checkbox" <?php echo $check; ?> 
            onClick="SeguirEmpresa('<?php echo $id_empresa; ?>', '<?php echo $Correo; ?>', '<?php echo $Position; ?>');"></td>
            <td><a href="../MostrarEmpresa.php?id=<?php echo $id_empresa; ?>&name=<?php echo $Nombre_empresa; ?>"><?php echo $Nombre_empresa; ?></a></td>
            <td><input type="number" value="<?php echo $alerta["cantidad"]; ?>" min="0" max="10"></td>
        </tr>
<?php
$Position++;
}
}
?>

</table>

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
</script>