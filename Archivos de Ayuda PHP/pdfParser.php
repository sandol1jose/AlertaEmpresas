<?php
$tiempo_inicio = microtime(true);
set_time_limit(5000);

include "../Librerias/pdfparser-master/alt_autoload.php-dist"; //Clase para pasar pdf a texto plano


for($i=0; $i<1; $i++){
    ConvertirATexto('https://www.boe.es/borme/dias/2022/02/17/pdfs/BORME-A-2022-33-02.pdf');
    //echo "Trabajando el archivo " . $i;
    //echo "\n\n\n";
}

function ConvertirATexto($URL){
    $parseador = new \Smalot\PdfParser\Parser();
    $nombreDocumento = $URL;
    $documento = $parseador->parseFile($nombreDocumento);
    $texto = $documento->getText();
    echo "<pre>" . $texto . "</pre>";
}

function separartexto($texto){
    $cadena1 = "";//Guarda la cadena principal
    $cadena2 = "";//Guarda la cadena auxiliar que no se va a guardar
    $Encontrado = -1;//Se encontro los encabezados que se quieren eliminar
    $EsPrimera = 0;//Verifica si es primera vez que aparece el encabezado "BOLETIN OFICIAL..."
    $saltos = 0;//Lleva control de los saltos de linea que se van a omitir
    $NumeroEntrada = 69154;//Contador de los registros en el BORME 1 - PROYECTOS 
    $Empresa = ""; //Nombre de empresa;
    $Entrada = ""; //Texto de la empresa;
    $RolDeBusqueda = "Empresa"; //Guarda si estamos buscando nombre de Empresa o Entrada
    /*for($i=0; $i<strlen($texto); $i++){*/for($i=0; $i<5000; $i++){
        if(strpos($cadena1, $NumeroEntrada . " - ") == false){
            if($EsPrimera == 0){
                if(strpos($cadena1, "BOLETÍN") == true){
                    $Encontrado = $i;
                    $cadena1 = str_replace("BOLETÍN", "", $cadena1);
                }else if(strpos($cadena2, "www.boe.es	\n") == true){
                    $Encontrado = -1;
                    $cadena2 = "";
                    $EsPrimera = 1;
                }else if($Encontrado == -1){
                    $cadena1 = $cadena1 . $texto[$i];
                }else{
                    $cadena2 = $cadena2 . $texto[$i];
                }
            }else{
                if($saltos > 4){
                    $cadena1 = $cadena1 . $texto[$i];
                    $EsPrimera = 0;
                }else{
                    if($texto[$i] == "\n"){
                        $saltos++;
                    }
                }
            }
        }else{
            for($j=$i; $j<strlen($texto); $j++){
                
                //Si encuentra un nuevo registro
                if($texto[$j] != "\n"){
                    if($RolDeBusqueda == "Empresa"){
                        $Empresa = $cadena1;
                    }else if($RolDeBusqueda == "Entrada"){
                        $Entrada = $cadena1;
                    }
                }else{
                    if($RolDeBusqueda == "Empresa"){
                        $cadena1 = "";
                        $RolDeBusqueda = "Entrada";
                        $Verificador = "";
                    }else if($RolDeBusqueda == "Entrada"){
                        for($x = $j; $x<=($j+20); $x++){
                            $Verificador = $Verificador . $texto[$x];
                        }
                        if(strpos($Verificador, "BOLETÍN OFICIAL") == true){
                            $cadena3 = $cadena1;
                            for($s=$j; $s<strlen($texto); $s++){
                                if(strpos($cadena3, "https://www.boe.es") == true){
                                    $cadena3 = "";
                                    $j=$s;
                                    break;
                                }
                                $cadena3 = $cadena3 . $texto[$s];
                            }
                            $Verificador = "";
                        }

                        if(strpos($Verificador, $NumeroEntrada+1) == true){
                            echo "Si es un nuevo registro";
                            $Verificador = "";

                            $cadena1 = "";
                            $RolDeBusqueda = "Empresa";
                            
                            GuardarRegistro($Empresa, $Entrada, $NumeroEntrada);
                            echo "------------------------------------------\n\n\n\n";
                            $Empresa = "";
                            $Entrada = "";
                            $NumeroEntrada++;
                            $i=$j-1;
                            break;
                        }
                        $Verificador = "";
                    }
                }
                $cadena1 = $cadena1 . $texto[$j];

            }           
        }
    }
}


function GuardarRegistro($NombreEmpresa, $Entrada, $NumeroEntrada){
    $NombreEmpresa = str_replace($NumeroEntrada . " - ", "", $NombreEmpresa);
    $NombreEmpresa = preg_replace("/\s+/", " ", trim($NombreEmpresa)); //Quitando espacios de mas
    $Entrada = str_replace("\n", " ", $Entrada); //Quitando espacios de mas
    $Entrada = preg_replace("/\s+/", " ", trim($Entrada)); //Quitando espacios de mas
    echo $NombreEmpresa;
    echo "<br>";
    echo $Entrada;
    echo "<br>";
    echo "Registro No. " . $NumeroEntrada . "<br>";
}


/*function separartexto3($texto){
    $NumeroEntrada = 1;
    $RolDeBusqueda = "Empresa";
    $cadena1 = "";
    $Empresa = "";
    $Entrada = "";
    for($i=0; $i<strlen($texto); $i++){
        $cadena1 = $cadena1 . $texto[$i];
        if(strpos($cadena1, $NumeroEntrada . " - ") == true){
            for($j=$i; $j<strlen($texto); $j++){
                
                //Si encuentra un nuevo registro
                if($texto[$j] != "\n"){
                    if($RolDeBusqueda == "Empresa"){
                        $Empresa = $cadena1;
                    }else if($RolDeBusqueda == "Entrada"){
                        $Entrada = $cadena1;
                    }
                }else{
                    if($RolDeBusqueda == "Empresa"){
                        $cadena1 = "";
                        $RolDeBusqueda = "Entrada";
                    }else if($RolDeBusqueda == "Entrada"){
                        $cadena1 = "";
                        $RolDeBusqueda = "Empresa";
                        
                        GuardarRegistro($Empresa, $Entrada, $NumeroEntrada);
                        echo "------------------------------------------\n\n\n\n";
                        $Empresa = "";
                        $Entrada = "";
                        $NumeroEntrada++;
                        $i=$j-1;
                        break;
                    }
                }
                $cadena1 = $cadena1 . $texto[$j];

            }

        }
    }
}*/

$tiempo_fin = microtime(true);
$tiempo = $tiempo_fin - $tiempo_inicio;
echo "\n\n";
echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

?>