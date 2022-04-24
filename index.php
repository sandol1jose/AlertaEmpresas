

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css" href="css/index.css">

    <title>Inicio</title>
</head>
<body>
<?php include 'templates/encabezado.php'; ?>

<?php
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');    

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->top5;
$Top5 = $collection->find([])->toArray();
?>

<?php
    if(isset($_SESSION['Alerta'])){
        $Alerta = $_SESSION['Alerta'];
        if (strcmp($Alerta, "Logout") === 0){
            echo "<script> alertsweetalert2('Has salido de tu cuenta', '', 'success'); </script>";
        }
        unset($_SESSION["Alerta"]);
    }
?>




<div>
    <center>
    <img src="imagenes/Escudo.png" alt="" width="77px" style="padding-top: 9px"><br>
    <span class="parrafo">Busca gratis toda la información de empresas <br> nacionales</span>

    <form action="ListarResultados.php" method="GET">
        <div class="divBuscador">
            <input class="buscador" type="text" name="buscador" autocomplete="off" placeholder="Busca por nombre de empresa">
            <!--<input class="BotonBusqueda" type="submit" value="Buscar">-->
            <button class="BotonBusqueda">
                <img id="imgBoton" src="imagenes/Lupa2.png" alt="" width="30px">
            </button>
        </div>
    </form>
    </center>
</div>


<div class="Espacio">

    <div class="grid-container">
		<div class="item1">
            <br><br>
            <img src="imagenes/bi_person-fill.png" alt="" width="55px"><br><br>
            <span class="subtitulo">Información de personas</span><br><br>
            <span class="parrafo1">Averigua quién está detrás de cualquier empresa española y los 
                movimientos que hace dentro de ella.</span>
        </div>

        <div class="item2">
            <br><br>
            <img src="imagenes/clarity_building-line.png" alt="" width="55px"><br><br>
            <span class="subtitulo">Información de empresas</span><br><br>
            <span class="parrafo1">Obten información de tus <b>clientes, proveedores y competencia</b> a través de 
                nuestros Informes Comerciales de Empresas basados en fuentes oficiales.</span>  
        </div>

        <div class="item3">
            <br><br>
            <img src="imagenes/fluent_alert-on-20-filled.png" alt="" width="55px"><br><br>
            <span class="subtitulo">Recibe alertas</span><br><br>
            <span class="parrafo1">Recibe alertas en tiempo real de los cambios o modificaciones de las empresas que te 
                interesan. Mantente informado de todas las modificaciones que hagan tus competidores. </span>          
        </div>
    </div>
</div>


<div class="divTop5">
    <center>
    <span class="parrafo">Top 5 de empresas nuevas por provincia</span>
    </center>

    <div class="grid-container2">
        <?php foreach($Top5 as $Empresas){ ?>
            <div class="item12">
                <br>
                <center>
                <span class="TituloProvincia"><?php echo mb_convert_case($Empresas["provincia"], MB_CASE_TITLE, "UTF-8"); ?></span><br>
                </center>

            <?php 
                $emp =  $Empresas["empresas"];

                foreach($emp as $Nombreempresa){?>
                <br><a href="ListarResultados.php?buscador=<?php echo $Nombreempresa ?>" class="parrafo1">
                    <?php echo mb_convert_case($Nombreempresa, MB_CASE_TITLE, "UTF-8"); ?>
                </a><br>

            <?php } ?>

            </div>
        <?php } ?>

    </div>
</div>


<?php include 'templates/footer.php'; ?>

</body>
</html>