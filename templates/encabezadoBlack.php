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
    <div class="Cabecera_Arriba" style="background-color: #343435;">
        <div class="DivTitulo">
            <span class="TituloPrincipal" style="color: white;">Alertaempresas.com</span>
        </div>
    </div>
</div>