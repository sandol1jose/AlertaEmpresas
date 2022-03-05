<?php
set_time_limit(5000);

include "Librerias/pdfparser-master/alt_autoload.php-dist"; //Clase para pasar pdf a texto plano
/*
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});*/
/*
set_error_handler("warning_handler", E_WARNING);

function warning_handler($errno, $errstr) { 
    throw new ErrorException($errstr, 0, $errno);
}

$parseador = new \Smalot\PdfParser\Parser();
$nombreDocumento = 'https://www.boe.es/borme/dias/2022/02/04/pdfs/BORME-A-2022-24-02.pdf';
try {
    //$contents = file_get_contents($nombreDocumento);
    $documento = $parseador->parseFile($nombreDocumento);
    $texto = $documento->getText();
    //$pages = $documento->getPages();
    echo "<pre>" . $texto . "<pre>";
} catch (Exception $e) {
    echo "No se encuentra el archivo pdf<br>";
    echo $e->getMessage();
}

restore_error_handler();*/

/*
//Recorrer fechas
$fechaInicio=strtotime("01-01-2009");
$fechaFin=strtotime("04-02-2022");
for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
    echo date("Y/m/d", $i)."<br>";
}*/

//Recorriendo xml
$xml = ("https://www.boe.es/diario_borme/xml.php?id=BORME-S-20220204");
$documento = simplexml_load_file($xml);
$diario = $documento->diario->seccion[0]->emisor->item;
//echo count($diario);

foreach($diario as $dia){
    echo $dia["id"] . "<br>";
    echo $dia->titulo . "<br>";
    echo $dia->urlPdf . "<br><br>";
}

?>