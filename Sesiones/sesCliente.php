<?php
//Archivo para crear la seseión de nuestro cliente
function CrearSesion($IDCliente, $Nombres, $Apellidos, $Correo, $Empresa){
	if (session_status() == PHP_SESSION_NONE) {
		session_start();//inicio de sesion
	}
	
	//Color de cuenta
    $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];

	/*if($IDCliente == -1){
		$IDCliente = BuscarIDCliente($Correo);
	}*/
	$arrayCliente = array(
			'IDCliente'=>$IDCliente,
			'Nombres'=>$Nombres,
			'Apellidos'=>$Apellidos,
			'Correo'=>$Correo,
			'Empresa'=>$Empresa,
			'ColorCuenta'=>$color
	);
	$_SESSION['Cliente'] = $arrayCliente;
}
?>

<?php
/*
function BuscarIDCliente($email){
	include '../conexion.php';
	$id = NULL;
	$sql = "SELECT c.id FROM usuario u JOIN cliente c ON u.id = c.f_usuario WHERE u.email = '".$email."'";
	$sentencia = $base_de_datos->prepare($sql);
	$sentencia->execute(); 
	$registros = $sentencia->fetchAll(PDO::FETCH_ASSOC);
	foreach ($registros as $registro) {
		$id = $registro["id"];
	}
	return $id;
}*/
?>