<?php
include_once "../Librerias/sitemap-php-master/src/SitemapPHP/Sitemap.php";
include_once "funciones.php";

//$host = "http://localhost/";
$host = "https://alertaempresas.com/";

use SitemapPHP\Sitemap;

$sitemap = new Sitemap($host . "AlertaEmpresas/");
$sitemap->setFilename("../sitemap");

$sitemap->addItem("index.php", "1.0", "yearly", "Today");
$sitemap->addItem("ListarResultados.php", "1.0", "yearly", "Today");
$sitemap->addItem("PantallaConsulta.php", "1.0", "yearly", "Today");

$sitemap->addItem("Cuenta/index.php", "0.5", "yearly", "Today");

$Archivos = obtenerListadoDeArchivos("../footer");
foreach($Archivos as $Archivo){
    if($Archivo["Nombre"] != "texto8984564876"){
        $NombreCompleto =  $Archivo["Nombre"] . "." . $Archivo["ext"];
        $sitemap->addItem("footer/" . $NombreCompleto, "0.4", "yearly", "Today");
    }
}

$sitemap->addItem("Login/AnuncioEmailNoVerificado.php", "0.2", "yearly", "Today");
$sitemap->addItem("Login/CambiarPass.php", "0.2", "yearly", "Today");
$sitemap->addItem("Login/index.php", "1.0", "yearly", "Today");
$sitemap->addItem("Login/RecuperarPass.php", "0.2", "yearly", "Today");
$sitemap->addItem("Login/Verificacion.php", "0.2", "yearly", "Today");
$sitemap->addItem("Login/VerificacionExito.php", "0.2", "yearly", "Today");

$sitemap->createSitemapIndex($host . "AlertaEmpresas/", "Today");

?>
