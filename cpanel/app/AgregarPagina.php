<?php
    //$_POST['NombrePagina'] = "NuevaPagina";
    if(!isset($_POST['NombrePagina'])){
        header('Location: ../agregarpaginas.php');
    }

    $Pagina_Nueva = $_POST["NombrePagina"];

    if(stristr(strtoupper($Pagina_Nueva), ".php") === false){//Si no tiene la extension
        $Pagina_Nueva .= ".php";
    }

    $Pagina_Nueva = str_replace(" ", "-", $Pagina_Nueva);

    $file = fopen("../../footer/texto8984564876.txt", "r");
    $file2 = fopen("../../footer/" . $Pagina_Nueva, "a");

    while(!feof($file)) {
        $Linea = fgets($file);
        fputs($file2, $Linea);
    }
    fclose($file); // Cierras el archivo
    fclose($file2); // Cierras el archivo

    if(file_exists("../../footer/" . $Pagina_Nueva)){
        //Agregandolo a la base de datos config
        $root = str_replace('\\', '/', dirname(__DIR__));
        $root = str_replace('/cpanel', '', $root);
        require_once($root . '/Archivos de Ayuda PHP/conexion.php');

        $PaginaSola = str_replace(".php", "", $Pagina_Nueva);
    
        $conexion = new Conexion();
        $database = $conexion->Conectar();
        $collection = $database->config;
        $filter = ['tipo' => 'titulo paginas'];
        $document = [ '$set' => [
                'footer.'.$PaginaSola => ""
            ]
        ];
        $Result = $collection->updateOne($filter, $document);
        if($Result->getModifiedCount() == 1){
            //echo $Pagina_Nueva;
            $IdTR = str_replace(" ", "-", $PaginaSola);
            echo "
            <tr id='tr_".$IdTR."'> 
                <td><a href='EditarPagina.php?pagina=".$Pagina_Nueva."'>".$PaginaSola."</a></td> 
                <td class=\"center\"><button class='botonVer' onclick=\"window.open('../footer/".$Pagina_Nueva."', '_blank')\">Ver</button></td> 
                <td class=\"center\"><button class='botonAmarillo' onclick=\"location.href='EditarPagina.php?pagina=".$Pagina_Nueva."'\">Editar</button></td> 
                <td class=\"center\"><button class='btnEliminar' onclick=\"EliminarPagina('".$Pagina_Nueva."', '".$IdTR."')\">Eliminar</button></td> 
            </tr>";
        }else{
            unlink('../../footer/' . $Pagina_Nueva);//Eliminamos el archivo
            echo "0";
        }
    }else{
        echo "0";
    }

?>



