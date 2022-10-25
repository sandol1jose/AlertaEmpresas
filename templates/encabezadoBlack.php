<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();//inicio de sesion
    }
    
    $ip = "http://localhost";
    //$ip = "https://alertaempresas.com/";
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
if($ListaCadena[0] == ""){
    $ListaCadena[0] = "AlertaEmpresas";
}else{
    unset($ListaCadena[0]);
}

if(count($ListaCadena) != 2){
    foreach($ListaCadena as $key=>$Clave){
        if($Clave == "AlertaEmpresas"){
            unset($ListaCadena[$key]);
        }
    }
}
$ListaCadena = array_values($ListaCadena);
?>
<script src="<?php echo $Servidor; ?>js/general.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">

<link rel="stylesheet" type="text/css" href="<?php echo $Servidor; ?>css/General.css">
<link rel="stylesheet" type="text/css" href="<?php echo $Servidor; ?>css/footer.css">

<?php if(isset($_SESSION["TITULOS_PAGINAS"][$ListaCadena[0]][$ListaCadena[1]])){ ?>
    <title><?php echo $_SESSION["TITULOS_PAGINAS"][$ListaCadena[0]][$ListaCadena[1]]; ?></title>
<?php }else{ ?>
<title>Sin titulo</title>
<?php } ?>

<div class="Cabecera" style="background-color: #343435;">
    <div clas="divBackGround">
        <div class="Cabecera_Arriba" style="background-color: #343435;">
            <div class="DivTitulo">
                <a href="<?php echo $Servidor; ?>" class="TituloPrincipal" style="color: white;">Alertaempresas.com</a>
            </div>
        </div>
    </div>
</div>