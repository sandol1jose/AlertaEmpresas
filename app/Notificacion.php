<?php
/*Archivo que enviará las notificaciones a los correos de las empresas que estan siendo seguidas
por algunos Clientes*/
function Notificar(){
	$root = str_replace('\\', '/', dirname(__DIR__));
	require_once($root . '/Archivos de Ayuda PHP/conexion.php');
	//require_once('../Archivos de Ayuda PHP/conexion.php');

	$conexion = new Conexion();
	$database = $conexion->Conectar();
	$collection = $database->NuevasEmpresasDia;
	$Result = $collection->find()->toArray();

	foreach($Result as $res){
		$id_Empresa = $res["id_empresa"];
		$bormes_agregados = $res["bormes_agregados"];
		$nombre_empresa = $res["nombre_empresa"];

		$collection = $database->empresas_diario;
		$filter = ["_id" => new MongoDB\BSON\ObjectID($id_Empresa)];
		$Result2 = $collection->findOne($filter);

		$BormesEmpresa = $Result2["anuncio_borme"];
		$anuncios = NULL;
		foreach($bormes_agregados as $newBorme){
			foreach($BormesEmpresa as $borme_empresa){
				if($borme_empresa["numero"] == $newBorme){
					$anuncios = $anuncios . $borme_empresa["tipo"] . "<br>";
					$anuncios = $anuncios . $borme_empresa["anuncio"] . "<br><br>";
				}
			}
		}

		$Correos = NULL;
		foreach($Result2["alertas"] as $alerta){
			if($alerta["estado"] == true){
				//Si la alerta esta activa
				$Correos[] = $alerta["correo_cliente"];
			}
		}
		var_dump($Correos);
		EnviarEmail($Correos, $anuncios, $nombre_empresa);
	}
}
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

	function EnviarEmail($Correos, $Anuncios_BORME, $Nombre_Empresa){
        
        $message = "
		<html>
		<head>
		<title>Alerta de Cambios en Empresa</title>
		</head>
		<body>
        <h2>".$Nombre_Empresa."</h2>
		<h3>Ha tendio algunos cambios</h3>
		<p>".$Anuncios_BORME."</p>
		<p>Alertaempresas.com</p>
		</body>
		</html>";
		 
		require '../Librerias/PHPMailer-master/src/Exception.php';
		require '../Librerias/PHPMailer-master/src/PHPMailer.php';
		require '../Librerias/PHPMailer-master/src/SMTP.php';
		
		/*require $_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Librerias/PHPMailer-master/src/Exception.php';
		require $_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Librerias/PHPMailer-master/src/PHPMailer.php';
		require $_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Librerias/PHPMailer-master/src/SMTP.php';*/
		
		//Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		
		try {
			//Server settings
			$mail->SMTPDebug = 0;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
			//$mail->Host       = 'smtp.hostinger.com';                       //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = 'jumpchiquimula@gmail.com';             //SMTP username
			$mail->Password   = 'jumpchiquimula9899';                  //SMTP password
			//$mail->Username   = 'soporte@jumpgt.com';             //SMTP username
			//$mail->Password   = '$6y9KUtAs2sVWF';                  		//SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			//$mail->Port       = 465;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->Port       = 587;
			//Recipients
			$mail->setFrom('jumpchiquimula@gmail.com', 'Jump GT');
			
			//Agregando a todos los correos
			foreach($Correos as $correo){
				$mail->addAddress($correo);                 					//Add a recipient
				echo $correo . '<br>';
			}
			/*$mail->addAddress('ellen@example.com');                   //Name is optional
			$mail->addReplyTo('info@example.com', 'Information');
			$mail->addCC('cc@example.com');
			$mail->addBCC('bcc@example.com');*/
		
			//Attachments
			/*
			$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
			$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name*/
		
			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Alerta de Cambios en Empresa';
			$mail->Body    = $message;
			//$mail->AltBody = 'Enviado desde 000webhost.com';
		
			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}
?>