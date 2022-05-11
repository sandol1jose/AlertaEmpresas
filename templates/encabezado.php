<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();//inicio de sesion
    }
    $ip = "http://localhost";
    //$ip = "https://alertaempresas.com";
    //$ip = gethostname();
    $Servidor = $ip.'/AlertaEmpresas/';
    //$Servidor = $ip;
?>


<?php
//Consultando los titulos de las paginas
if(!isset($_GET["prueba"])){
    $_GET["prueba"] = 0;
}
if(!isset($_SESSION["TITULOS_PAGINAS"]) || $_GET["prueba"] == 1){
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');
    
    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->config;
    $filter = ['tipo' => 'titulo paginas'];
    $options["projection"] = ['_id' => 0, 'tipo' => 0];
    $Titulos = $collection->findOne($filter, $options);

    $ArrayResultante = NULL;
    foreach ($Titulos as $key => $value) {
        $ArrayResultante[$key] = iterator_to_array($value);
    }
    
    $_SESSION["TITULOS_PAGINAS"] = $ArrayResultante;
}

$ListaCadena = explode("/", str_replace('.php', '', $_SERVER['PHP_SELF']));
unset($ListaCadena[0]);
if(count($ListaCadena) != 2){
    unset($ListaCadena[1]);
}
$ListaCadena = array_values($ListaCadena);
?>

<script src="<?php echo $Servidor; ?>js/general.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">

<link rel="stylesheet" type="text/css" href="<?php echo $Servidor; ?>css/General.css">
<link rel="stylesheet" type="text/css" href="<?php echo $Servidor; ?>css/footer.css">


<?php if(isset($_SESSION["TITULOS_PAGINAS"][$ListaCadena[0]][$ListaCadena[1]])){ ?>
    <title><?php echo $_SESSION["TITULOS_PAGINAS"][$ListaCadena[0]][$ListaCadena[1]]; ?></title>
<?php }else{ ?>
<title>Sin titulo</title>
<?php } ?>

<div class="Cabecera" style="background-color: white;">
    <div class="Cabecera_Arriba">
        <div class="DivTitulo">
            <a href="<?php echo $Servidor; ?>" class="TituloPrincipal">Alertaempresas.com</a>
        </div>

        <div class="DivBoton">
            <?php
                if(isset($_SESSION["Cliente"])){
                    $Color = $_SESSION["Cliente"]['ColorCuenta'];
                    //printf("#%06X\n", mt_rand(0, 0x222222));
                    ?>
                    <!--<a href="Cuenta"><?php// echo $_SESSION["Cliente"]["Nombres"]; ?>
                    <a href="Login/LogOut.php">cerrar sesion</a>-->

                    <button class="BotonCuenta" onclick="window.location.href='<?php echo $Servidor ?>Cuenta'" style='background-color:<?php echo $Color ?>' >
                        <?php echo strtoupper($_SESSION["Cliente"]["Nombres"][0]);?>
                    </button>
                    <a href="<?php echo $Servidor ?>Login/LogOut.php"><button class="Boton">Salir</button></a>
            <?php }else{ ?>
                <a href="<?php echo $Servidor ?>Login">
                <button class="Boton">Acceder</button>
                </a>
            <?php }?>
        </div>
    </div>

        <div class="Cabecera_Abajo">
            <div class="Vinculos">
                <!--
                <a class="BotonesVinculos" href="<?php echo $Servidor ?>">Inicio</a>
                <a class="BotonesVinculos" href="">Sobre nosotros</a>
                <a class="BotonesVinculos" href="">Listado</a>
                -->
            </div>
        </div>
    </div>