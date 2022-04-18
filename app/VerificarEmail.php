<?php
	//VERIFICAR EMAIL
	session_start();
	//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
	$root = str_replace('\\', '/', dirname(__DIR__));
	require_once($root . '/Archivos de Ayuda PHP/conexion.php');
	//require '../Archivos de Ayuda PHP/conexion.php';

	$Codigo = strtoupper($_POST["codigo"]);
    $Correo = $_POST["email"];
	$Correo = strtolower($Correo);//Convirtiendo todo el correo a minusculas
	
	//VERIFICAMOS QUE EL CODIGO EXISTA
	$conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->Clientes;
	$filter = ["email" => $Correo];
	$Result = $collection->findOne($filter);

	$encontrado = false;
	if($Result["verificacion"] != NULL){
		foreach($Result["verificacion"] as $cod){
			$clave = $cod["clave"];
			if($clave == $Codigo){
				$encontrado = true;
				break;
			}
		}
	}

	if($encontrado == true){//El CODIGO EXISTE

		$filter = ["_id" => $Result["_id"]];
		$indice = indexOfArray($Codigo, "verificacion.clave");
        $document = [
            '$set' => [
				"verificacion.".$indice.".verificado" => true,
				"verificado" => true
				]
        ];
        $Result = $collection->updateOne($filter, $document);
			
		if($Result->getModifiedCount() == 1){
			//SE AGREGO CORRECTAMENTE
			$_SESSION["VERIFICACIONEXITO"] = 1;
			header('Location: ../Login/VerificacionExito.php'); 
		}else{
			echo "ocurrio un error";
		}
	}else{
		//El codigo no existe en la base de datos
		$_SESSION["Alerta"] = "CodigoIncorrecto";
		header('Location: ../Login/Verificacion.php?email='.$Correo);
	}


	function indexOfArray($Busqueda, $Ruta){
		//Busca el índice donde se encuentra un array
		global $collection;
		global $filter;

		$ArrayBusqueda = [
			[ '$project' => 
				[ 'index' => 
					[ '$indexOfArray' => [ '$' . $Ruta, $Busqueda] ],
				]
			],
			[
				'$match' => $filter
			]
		];
		$Result2 = $collection->aggregate($ArrayBusqueda)->ToArray();
		return $Result2[0]['index'];
	}
?>