<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();//inicio de sesion
}

$root = str_replace('\\', '/', dirname(__DIR__));

if(!isset($_GET["prueba"])){
    $_GET["prueba"] = 0;
}
if(!isset($_SESSION["VINCULOS_FOOTER"]) || $_GET["prueba"] == 1){
    $Archivos = obtenerListadoDeArchivos($root . "/footer");
    $_SESSION["VINCULOS_FOOTER"] = $Archivos;
}else{
    $Archivos = $_SESSION["VINCULOS_FOOTER"];
}

function obtenerListadoDeArchivos($directorio){
    // Array en el que obtendremos los resultados
    $res = array();
  
    // Agregamos la barra invertida al final en caso de que no exista
    if(substr($directorio, -1) != "/") $directorio .= "/";
  
    // Creamos un puntero al directorio y obtenemos el listado de archivos
    $dir = @dir($directorio) or die("getFileList: Error abriendo el directorio $directorio para leerlo");
    while(($archivo = $dir->read()) !== false) {
        // Obviamos los archivos ocultos
        if($archivo[0] == ".") continue;
        if(is_dir($directorio . $archivo)) {

        } else if (is_readable($directorio . $archivo)) {
            $List = explode(".", $archivo);
            $res[] = array(
              "Nombre" => $List[0],
              "ext" => $List[1],
            );
        }
    }
    $dir->close();
    return $res;
}

?>

<div class="Pie">
    <div class="PieArriba">
        
    </div>

    <div class="PieAbajo">
        <div>
<?php

        foreach($Archivos as $Archivo){
            if($Archivo["Nombre"] != "texto8984564876"){
                $Nombre = str_replace("-", " ", $Archivo["Nombre"]);
?>
            
        <a class="aPieFooter" href="<?php echo $Servidor."footer/".$Archivo["Nombre"]; ?>">
            <?php echo $Nombre ?>&nbsp &nbsp &nbsp
        </a>  
            
<?php
            }
        }
?>
        </div>
    </div>
</div>