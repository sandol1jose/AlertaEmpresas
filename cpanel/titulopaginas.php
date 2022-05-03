<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>

<?php
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->config;
    $filter = ['tipo' => 'titulo paginas'];
    $options["projection"] = ['_id' => 0, 'tipo' => 0];
    $Result = $collection->findOne($filter, $options);
?>

<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titulo de Páginas</title>

    <style>
        table{
            width: auto;
        }

        .botonVer{
            width: 70px;
        }
    </style>
</head>
<body>


<div class="divBackGround">
    <div class="divContenedor">
    <span> 
        <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px"> 
        <b>Titulo de Páginas</b>
    </span>

    <h1>Titulo de páginas</h1>
    <span>Modifica el titulo de las páginas</span><br>
    <span>Por seguridad algunas páginas no pueden ser accedidas desde aqui.</span><br><br><br>

<?php
    if($Result != NULL){
        foreach ($Result as $Carpeta => $value) { 
            $Carpeta2 = $Carpeta;
            if($Carpeta == "AlertaEmpresas") $Carpeta = "";
?>
    <span>Carpeta: <b><?php echo $Carpeta2; ?></b> </span> <br><br>
    <table>
        <thead>
            <tr>
                <td>Página</td>
                <td>Titulo de Página</td>
                <td>Acción</td>
            </tr>
        </thead>
        <tbody>
                
<?php               
    foreach($value as $Pagina => $Titulo){
        if($Carpeta != ""){
            $link = $Carpeta . "/" . $Pagina . ".php?email=ejemplo@ejemplo.com&buscador=Entrerios Apartamentos Turisticos Sociedad Limitada&id=62671ec5ea01000084005d1d&prueba=1";
        }else{
            $link = $Pagina . ".php?email=ejemplo@ejemplo.com&buscador=Entrerios Apartamentos Turisticos Sociedad Limitada&id=62671ec5ea01000084005d1d&prueba=1";
        }
        $link = str_replace('//', '/', $link);
        $link = $Servidor . $link;
        $Target = "target='_blank'";

        if($Pagina == "CambiarPass" || ($Carpeta . $Pagina) == "Cuentaindex"){
            $link = "";
            $Target = "";
        }
?>          
                    
    <tr>
        <td> 
            <a href="<?php echo $link; ?>" <?php echo $Target; ?>>
                <?php echo $Pagina; ?>
            </a> 
        </td>

        <td class="tdInput">
            <input class="InputTablas" type="text" id="Titulo_<?php echo $Carpeta2 . $Pagina; ?>" value="<?php echo $Titulo; ?>">
        </td>

        <td class="center">
            <button class="botonVer" onclick="GuardarTitulo('<?php echo $Carpeta2; ?>', '<?php echo $Pagina; ?>')">
                Guardar
            </button>
        </td>
    </tr>
                    
<?php
    }
?>
    </tbody>
    </table><br><br>
<?php
    }
}
?>

    </div>
</div>
</body>
</html>


<script>
    function GuardarTitulo(Carpeta, Pagina){
        var Titulo = document.getElementById("Titulo_" + Carpeta + Pagina).value;
        $.ajax({
			type: "POST",
			url: "app/GuardarTitulo.php",
			data: {'Carpeta': Carpeta, 'Pagina': Pagina, 'Titulo': Titulo},
			dataType: "html",
			beforeSend: function(){
                //console.log("Estamos procesando los datos... ");
			},
			error: function(){
				console.log("error petición ajax");
			},
			success: function(data){
                if(data == "1"){
                    alertsweetalert2('Se ha guardado el titulo', '', 'success');
                }else{
                    alertsweetalert2('No se pudo guardar el titulo', '', 'error');
                }
			}
		});
    }
</script>