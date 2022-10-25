<?php
set_time_limit(50000);
ini_set('memory_limit', '2048M');

$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

GenerarSiteMap(1);
function GenerarSiteMap($tipo){
    $conexion = new Conexion();
    $database = $conexion->Conectar();
    
    //$tipo = $_POST["tipo"]; //1 = anuncios   2 = anuncios_dia
    $collection2 = $database->listaempresas;
    $options["projection"] = ['id_nombre_comercial' => 1, 'nombre_comercial' => 1, '_id' => 0];
    
    if($tipo == 1){
        $collection = $database->anuncios;
    
        $Fecha1 = new MongoDB\BSON\UTCDatetime($_POST["fechaInicio"]);
        $Fecha2 = new MongoDB\BSON\UTCDatetime($_POST["fechaFin"]);
        
        $Busqueda = [
            '$and' => [
                ["fecha" => ['$gte' => $Fecha1]],
                ["fecha" => ['$lte' => $Fecha2]]
            ]
        ];
        $Result = $collection->find($Busqueda, $options)->toArray();
    }else{
        $collection = $database->anuncios_dia;
        $Result = $collection->find([], $options)->toArray();
    }
    
    if($Result != NULL){

        //Consultando cuantos registros hay en el sitemap actual.
        $collection = $database->config;
        $Busqueda = ['tipo' => 'sitemap'];
        $Result_Config = $collection->findOne($Busqueda);

        $archivo_actual = $Result_Config["archivo_actual"];
        $Cant_Empresas = $Result_Config["cantidad_empresas"];
        $archivo_no = $Result_Config["archivo_no"];
        $archivo_actual = $archivo_actual;

        $EspaciosDisponibles = 40000 - $Cant_Empresas;
        $Array_Archivos[] = $archivo_actual;

        if(!file_exists($archivo_actual . ".txt")){
            $Linea = '<?xml version="1.0" encoding="UTF-8"?> <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        }else{
            $Linea = "";
        }
        
        $file = fopen($archivo_actual . ".txt", "a");
        
        if($Linea != ""){
            fputs($file, $Linea);
        }


        $contador = 0;
        foreach($Result as $Anuncio){
            $Result2 = $collection2->findOne(['id_nombre_comercial' => $Anuncio["id_nombre_comercial"]]);
            if($Result2 == NULL){
                if($contador < $EspaciosDisponibles){
                    AgregarRegistro($Anuncio, $file, $collection2);
                    $contador++;
                }else{
                    fclose($file);
                    $archivo_actual = "sitemap_".($archivo_no + 1);

                    $file = fopen($archivo_actual . ".txt", "a");
                    $Linea = '<?xml version="1.0" encoding="UTF-8"?> <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
                    fputs($file, $Linea);

                    $Busqueda = ['tipo' => 'sitemap'];
                    $Actualizacion = [
                        '$set' => [
                            "archivo_actual" => $archivo_actual,
                            "archivo_no" => ($archivo_no + 1),
                            "cantidad_empresas" => 0
                        ]
                    ];
                    $Result = $collection->updateOne($Busqueda, $Actualizacion);

                    $Cant_Empresas = 0;
                    $archivo_no = ($archivo_no + 1);

                    AgregarRegistro($Anuncio, $file, $collection2);

                    $EspaciosDisponibles = 40000 - $Cant_Empresas;
                    $Array_Archivos[] = $archivo_actual;
                    $contador = 1;
                }
            }
        }

        $Busqueda = ['tipo' => 'sitemap'];
        $Actualizacion = [
            '$set' => [
                "archivo_actual" => $archivo_actual,
                "archivo_no" => $archivo_no,
                "cantidad_empresas" => $Cant_Empresas + $contador
            ]
        ];
        $Result = $collection->updateOne($Busqueda, $Actualizacion);

        fclose($file);
        
        $cont2 = 0;
        foreach($Array_Archivos as $archivo_actual){
            if(file_exists($archivo_actual . ".xml")) unlink($archivo_actual . '.xml');//Eliminamos el archivo
            copy($archivo_actual . '.txt', $archivo_actual . '.xml');
        
            $file2 = fopen($archivo_actual . ".xml", "a");
            fputs($file2, '</urlset>');
            fclose($file2);

            $cont2++;

            if($cont2 < count($Array_Archivos)){
                unlink($archivo_actual . '.txt');//Eliminamos el archivo
            }
        }

        CrearSiteMapIndice($archivo_no);//Creamos o actualizamos el sitemap Indice
    }
}

function AgregarRegistro($Anuncio, $file, $collection2){
    fputs($file, '<url>');
    $nombre = $Anuncio["nombre_comercial"];
    //$Link = '<loc>https://alertaempresas.com/app/buscarid?buscadorEmpresa='.$nombre.'</loc>';
    $nombre = LimpiarNombre($nombre);
    $Link = '<loc>https://alertaempresas.com/empresa/'.$nombre.'</loc>';
    fputs($file, $Link);
    fputs($file, '<priority>1.0</priority>');
    fputs($file, '<changefreq>yearly</changefreq>');
    fputs($file, '<lastmod>2022-06-25</lastmod>');
    fputs($file, '</url>');
    $Result2 = $collection2->insertOne(['id_nombre_comercial' => $Anuncio["id_nombre_comercial"]]);
}


function CrearSiteMapIndice($archivo_no){
    //Creamos o actualizamos el sitemap Indice
    if(file_exists('sitemap-index.xml')) unlink('sitemap-index.xml');
    $file = fopen("sitemap-index.xml", "a");
    
    fputs($file, '<?xml version="1.0" encoding="UTF-8"?>');
    fputs($file, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

    for($i = 1; $i <= $archivo_no; $i++){
        fputs($file, '<sitemap>');
        fputs($file, '<loc>https://alertaempresas.com/sitesmaps/sitemap_'.$i.'.xml</loc>');
        fputs($file, '<lastmod>2022-06-25</lastmod>');
        fputs($file, '</sitemap>');
    }
    fputs($file, '</sitemapindex>');
}


//Funcion para Seo Friendly
function LimpiarNombre($Nombre){
    $Nombre = str_replace(".", "", $Nombre);
    $Nombre = str_replace(",", "", $Nombre);
    $Nombre = str_replace("(", "-", $Nombre);
    $Nombre = str_replace(")", "", $Nombre);
    $Nombre = str_replace("&", "", $Nombre);
    $Nombre = str_replace("$", "", $Nombre);
    $Nombre = str_replace("%", "", $Nombre);
    $Nombre = mb_strtolower($Nombre, 'UTF-8');
    $Nombre = str_replace(" ", "-", $Nombre);
    return $Nombre;
}


/*
    if($Result != NULL){
        if(!file_exists("sitemap0.txt")){
            $Linea = '<?xml version="1.0" encoding="UTF-8"?> <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        }else{
            $Linea = "";
        }
        
        $file = fopen("sitemap0.txt", "a");
        
        if($Linea != ""){
            fputs($file, $Linea);
        }

        foreach($Result as $Anuncio){
            $Result2 = $collection2->findOne(['id_nombre_comercial' => $Anuncio["id_nombre_comercial"]]);
            if($Result2 == NULL){
                fputs($file, '<url>');
                $nombre = urlencode($Anuncio["id_nombre_comercial"]);
                $Link = '<loc>https://alertaempresas.com/app/buscarid?buscadorEmpresa='.$nombre.'</loc>';
                fputs($file, $Link);
                fputs($file, '<priority>1.0</priority>');
                fputs($file, '<changefreq>yearly</changefreq>');
                fputs($file, '<lastmod>2022-06-03</lastmod>');
                fputs($file, '</url>');
        
                $Result2 = $collection2->insertOne(['id_nombre_comercial' => $Anuncio["id_nombre_comercial"]]);
            }
        }
        fclose($file);
        if(file_exists("sitemap.xml")) unlink('sitemap.xml');//Eliminamos el archivo
        copy('sitemap0.txt', 'sitemap.xml');
        
        $file2 = fopen("sitemap.xml", "a");
        fputs($file2, '</urlset>');
        fclose($file2);
    }
*/
?>