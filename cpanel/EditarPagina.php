<?php
session_start();//inicio de sesion
if(!isset($_SESSION["ADMIN_CPANEL"])){
    header('Location: login'); //Agregamos el producto
}

//$_GET["pagina"] = "acercadenosotros.php";

if(!isset($_GET["pagina"])){
    header('Location: agregarpaginas'); //Agregamos el producto
}
$Pagina = $_GET["pagina"];

$NombrePaginaSola = str_replace(".php", "", $Pagina);


//Leyendo el documento
$file = fopen('../footer/' . $Pagina, 'a+'); // Abrir el archivo en modo lectura.
$Texto = "";
$Leer = false;
while(!feof($file)) {
    $Linea = fgets($file);

    if(!(stristr(strtoupper($Linea), "fin_document") === false)){
        $Leer = false;
    }

    if($Leer == true){
        $Texto .= $Linea;
    }

    if(!(stristr(strtoupper($Linea), "inicio_document") === false)){
        $Leer = true;
    }

}
$Texto = trim($Texto, "\n");
fclose($file); // Cierras el archivo
?>

<?php include '../templates/encabezadoBlack.php'; ?>
<?php include 'encabezadocpanel.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pagina</title>

    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>

    <style>
        .BotonGeneral2{
            width: 80px;
        }

        .botonVer{
            height: 40px;
            width: 80px;
            font-size: 14px;
        }

        .divTextArea{
            text-align: center;
        }

        .txtArea{
            /*font-size: 17px;*/
            width: 100%;
            /*font-family: 'Roboto';*/
        }
    </style>
</head>
<body>
<?php
    if(isset($_SESSION["Actualizacion_Exito"])){
        unset($_SESSION["Actualizacion_Exito"]);
        echo "<script> alertsweetalert2('Actualizado correctamente', '', 'success'); </script>";
    }
?>



<div class="divBackGround">
    <div class="divContenedor">
    <span> 
        <a href="index.php">Cpanel</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">  
        <a href="agregarpaginas.php">Agregar Páginas</a> <img src="../imagenes/cpanel/next.png" alt="" width="10px">  
        <b>Editar Página</b>
    </span>

    <h1>Edita el cuerpo de la página "<?php echo $NombrePaginaSola ?>"</h1>
    <span>Puedes agregar etiquetas html</span><br><br>

    
    <form action="app/editararchivo.php" method="POST">
        <button class="BotonGeneral2" class="btnGeneral2" type="submit">Guardar</button>
        <button type="button" class="botonVer" onclick="window.open('../footer/<?php echo $Pagina ?>', '_blank')">Ver</button>
        <br><br>
        <div class="divTextArea">
            <TextArea class="txtArea" name="textoHTML" id="textoHTML" rows="100"><?php echo $Texto; ?></TextArea>
        </div>
        <input type="hidden" name="pagina" value="<?php echo $Pagina; ?>">
    </form>

    </div>
</div>
</body>
</html>


<script>
        ClassicEditor
                .create( document.querySelector( '#textoHTML' ) )
                .then( editor => {
                        console.log( editor );
                } )
                .catch( error => {
                        console.error( error );
                } );
</script>
