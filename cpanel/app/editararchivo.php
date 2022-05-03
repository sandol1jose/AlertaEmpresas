<?php
session_start();

if(!isset($_POST['textoHTML']) || !isset($_POST['pagina'])){
    header('Location: ../EditarPagina.php');
}

$TextoHTML = $_POST["textoHTML"];
$pagina = $_POST["pagina"];

//$lineas = explode("\n", $TextoHTML);
//echo var_dump($lineas);

$file = fopen('../../footer/' . $pagina, 'a+'); // Abrir el archivo en modo lectura.

$ArrayLinea = NULL;
$Leer = true;
while(!feof($file)) {
    $Linea = fgets($file);

    if($Leer == true){
        $ArrayLinea[] = $Linea;
    }
    
    if(!(stristr(strtoupper($Linea), "inicio_document") === false)){
        $Leer = false;
        $ArrayLinea[] = $TextoHTML . "\n";
    }

    if(!(stristr(strtoupper($Linea), "fin_document") === false)){
        $ArrayLinea[] = $Linea;
        $Leer = true;
    }
}
fclose($file); // Cierras el archivo

// Cuando termines guardas los datos de nuevo en el archivo.
$file = fopen('../../footer/' . $pagina, 'w'); // Abres el archivo en modo escritura.
foreach ($ArrayLinea as $value) {
    fputs($file, $value);
}
fclose($file); // Cierras el archivo.*/

$_SESSION["Actualizacion_Exito"] = 1;
header('Location: ../EditarPagina.php?pagina=' . $pagina); //Agregamos el producto*/
?>