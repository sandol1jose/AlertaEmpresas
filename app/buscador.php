<?php
    session_start();
    if(!isset($_POST["buscador"])){
        header('Location: ../');
    }

    $_SESSION["buscador"] = $_POST["buscador"];

    header('Location: ../ListarResultados');
?>