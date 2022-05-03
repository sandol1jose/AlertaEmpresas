
<?php include '../templates/encabezado.php'; ?>

<?php
$root = str_replace('\\', '/', dirname(__DIR__));
$ArchivosFooter = obtenerListadoDeArchivos2($root . "/footer/");
/*
    if($Archivo["Nombre"] != "texto8984564876"){
        $NombreCompleto =  $Archivo["Nombre"] . "." . $Archivo["ext"];
        $sitemap->addItem("footer/" . $NombreCompleto, "0.4", "yearly", "Today");
    }
}*/

function obtenerListadoDeArchivos2($directorio){
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



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo $Servidor; ?>footer/css/General_footer.css">

    <style>
        ul,ol,dl,p {
            font-size: 1.2rem;
            list-style-type: none;
            list-style-position: inside;
        }
    </style>
</head>
<body>
<div class="divBackGround">
    <div class="Marco">
        <h1>Mapa del sitio</h1>

        <h3>General</h3>
        <div>
            <ul>
                <li><a href="../" target="_blank">Inicio</a></li>
            </ul>
        </div>

        <h3>Cuenta</h3>
        <div>
            <ul>
                <li><a href="../Cuenta/" target="_blank">Inicio</a></li>
            </ul>
        </div>

        <h3>Footer</h3>
        <div>
            <ul>
<?php
            foreach($ArchivosFooter as $Archivo){
                $Nombre = str_replace("-", " ", $Archivo["Nombre"]);
                if($Archivo["Nombre"] != "texto8984564876"){
?>
                <li><a href="<?php echo $Archivo["Nombre"] ?>" target="_blank"><?php echo $Nombre; ?></a></li>
<?php
                }
            }
?>

            </ul>
        </div>

        <h3>Login</h3>
        <div>
            <ul>
                <li><a href="../Login/" target="_blank">Inicio</a></li>
                <li><a href="../Login/RecuperarPass" target="_blank">RecuperarPass</a></li>
            </ul>
        </div>

        <a href="../sitemap.xml">Ver XML</a>

    </div>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>