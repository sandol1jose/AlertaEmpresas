<?php
	/*Clase para hacer web scraping descargada de:
	https://simplehtmldom.sourceforge.io/ */

    //BORME-A-2022-62-51
    include '../Librerias/simplehtmldom_1_9_1/simple_html_dom.php';
   
    //Escrapear("BORME-A-2022-62-51");
    function Escrapear($Borme){
        $url = 'https://librebor.me/borme/borme/'.$Borme.'/';
        $html = file_get_html($url); //Capturamos el html
        $Anuncios = $html->find('p.font-weight-bold', 4)->plaintext;
        $Anuncios = str_replace("Anuncios: ", "", $Anuncios);
        $Anuncios = str_replace(" ", "", $Anuncios);
        return $Anuncios;
    }
?>