<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}
?>


<?php

$Archivos = obtenerListadoDeArchivos("../footer");
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
            $res[] = array(
              "Nombre" => $directorio . $archivo . "/",
              "Tamaño" => 0,
              "Modificado" => filemtime($directorio . $archivo)
            );
        } else if (is_readable($directorio . $archivo)) {
            $res[] = array(
              "Nombre" => $directorio . $archivo,
              "Tamaño" => filesize($directorio . $archivo),
              "Modificado" => filemtime($directorio . $archivo)
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
    <title>Agregar Páginas</title>
    <?php include '../templates/encabezadoBlack.php'; ?>
    <?php include 'encabezadocpanel.php'; ?>

    <style>
        table{
            width: auto;
        }

        .InputGeneral{
            width: 350px;
        }

        .BotonGeneral2{
            width: 150px;
        }

        .center{
            width: 50px;
        }

        .botonVer{
            width: 60px;
        }

        .botonAmarillo{
            width: 60px;
        }
    </style>
</head>
<body>

<div class="divBackGround">
    <div class="divContenedor">
    <span> 
        <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px"> 
        <b>Agregar Páginas</b>
    </span>

    <h1>Páginas del pie de página</h1>
    <span>Agrega o modifica el contenido de las páginas del pie de página</span><br><br><br>

    <b>Agrega una página</b><br><br>

    <input class="InputGeneral" type="text" id="name_page" placeholder="Nombre de Pagina">
    <button class="BotonGeneral2" onclick="AgregarPagina()">Agregar Pagina</button>
    <br><br>

    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th colspan="3">Acciones</th>
            </tr>
        </thead>

        <tbody id="tbody">
<?php
        foreach($Archivos as $archivo){
            $NombreSolo = str_replace("../footer/", "", $archivo['Nombre']);
            $NombreSinExt = str_replace(".php", "", $NombreSolo);
            $IdTR = str_replace(" ", "-", $NombreSinExt);
            if($NombreSolo != "css/" && $NombreSolo != "texto8984564876.txt" && $NombreSolo != "Mapa-del-sitio.php"){
?>
        
        
            <tr id="tr_<?php echo $IdTR; ?>">
                <td><a href="EditarPagina.php?pagina=<?php echo $NombreSolo; ?>"><?php echo $NombreSinExt; ?></a></td>
                <td class="center"><button class="botonVer" onclick="window.open('../footer/<?php echo $NombreSolo ?>', '_blank')">Ver</button></td>
                <td class="center"><button class="botonAmarillo" onclick="location.href='EditarPagina.php?pagina=<?php echo $NombreSolo; ?>'">Editar</button></td>
                <td class="center"><button class="btnEliminar" onclick="EliminarPagina('<?php echo $NombreSolo; ?>', '<?php echo $IdTR; ?>')">Eliminar</button></td>
            </tr>
        
<?php
            }
        }


?>

        </tbody>
    </table>

    </div>
</div>
</body>
</html>


<script>
    function AgregarPagina(){
        var NombrePagina = document.getElementById("name_page").value;
        if(NombrePagina != ""){
            if(NombrePagina.indexOf(".php") == -1 ){
                NombrePagina += ".php";
            }
            $.ajax({
                type: "POST",
                url: "app/AgregarPagina.php",
                data: {'NombrePagina': NombrePagina},
                dataType: "html",
                beforeSend: function(){
                    //console.log("Estamos procesando los datos... ");
                },
                error: function(){
                    console.log("error petición ajax");
                },
                success: function(data){
                    if(data != "0"){
                        /*var html = " \
                        <tr id='tr_"+data+"'> \
                            <td><input type='checkbox'></td> \
                            <td><a href='EditarPagina.php?pagina="+data+"'>"+data+"</a></td> \
                            <td><button onclick=\"window.open('../footer/"+data+"', '_blank')\">Ver</button></td> \
                            <td><button onclick=\"location.href='EditarPagina.php?pagina="+data+"'\">Editar</button></td> \
                            <td><button onclick=\"EliminarPagina('"+data+"')\">Eliminar</button></td> \
                        </tr>";*/
                        $("#tbody").append(data);
                        alertsweetalert2('Se agrego la pagina', '', 'success');
                    }
                }
		    });
        }
    }

    function EliminarPagina(NombrePagina, idTr){
        if(NombrePagina != ""){
            if(NombrePagina.indexOf(".php") == -1 ){
                NombrePagina += ".php";
            }

            $.ajax({
                type: "POST",
                url: "app/EliminarPagina.php",
                data: {'NombrePagina': NombrePagina},
                dataType: "html",
                beforeSend: function(){
                    //console.log("Estamos procesando los datos... ");
                },
                error: function(){
                    console.log("error petición ajax");
                },
                success: function(data){
                    if(data == "1"){
                        $("#tr_" + idTr).remove();
                        alertsweetalert2('Se eliminó la página', NombrePagina, 'success');
                    }
                }
		    });
        }
    }
</script>