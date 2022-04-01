<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();//inicio de sesion
    }
    
    $Servidor = 'http://localhost/AlertaEmpresas/';
?>

<script src="<?php echo $Servidor; ?>js/general.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $Servidor; ?>css/general.css">

<div class="Cabecera">
        <div class="Cabecera_Arriba">
            <div class="DivTitulo">
                <span class="TituloPrincipal">Alertaempresas.com</span>
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
                <a class="BotonesVinculos" href="<?php echo $Servidor ?>">Inicio</a>
                <a class="BotonesVinculos" href="">Sobre Nosotros</a>
                <a class="BotonesVinculos" href="">Listado</a>
            </div>
        </div>
    </div>