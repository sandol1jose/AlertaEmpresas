<?php

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