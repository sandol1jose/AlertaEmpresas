<?php
/*Archivo que enviará las notificaciones a los correos de las empresas que estan siendo seguidas
por algunos Clientes*/
//Notificar();
function Notificar(){
	$root = str_replace('\\', '/', dirname(__DIR__));
	require_once($root . '/Archivos de Ayuda PHP/conexion.php');
	echo "Estamos por enviar los correos\n\n";

	$conexion = new Conexion();
	$database = $conexion->Conectar();
	$collection = $database->anuncios_dia;
	$Anuncios = $collection->find()->toArray();

	//Agrupando los anuncios por empresa
	if($Anuncios != NULL){
		$Anuncios_Agrupados = NULL;
		foreach($Anuncios as $anuncio){
			$id_nombre_comercial = $anuncio["id_nombre_comercial"];
			$Anuncios_Agrupados[$id_nombre_comercial][] = $anuncio;
		}

		foreach($Anuncios_Agrupados as $res){
			//Buscamos a la empresa para ver sus alertas
			$id_nombre_comercial = $res[0]["id_nombre_comercial"];
			$filter = [ '$or' => [
				["id_nombre_comercial" => $id_nombre_comercial ],
				["id_denominaciones_sociales" => $id_nombre_comercial ]
				]
			];
			$collection2 = $database->empresas;
			$Empresa = $collection2->findOne($filter);

			if($Empresa != NULL && isset($Empresa["alertas"])){
				$anuncios = NULL;
				foreach($res as $newBorme){
					$anuncios = $anuncios . $newBorme["tipo"] . "<br>";
					$anuncios = $anuncios . $newBorme["anuncio"] . "<br><br>";
				}
		
				$Correos = NULL;
				foreach($Empresa["alertas"] as $alerta){
					if($alerta["estado"] == true){
						//Si la alerta esta activa
						$Correos[] = $alerta["correo_cliente"];
					}
				}
				if($Correos != NULL){
					EnviarEmail($Correos, $anuncios, $Empresa["nombre_comercial"]);
					//echo $anuncios;
				}
			}
		}
	}
}

/*function Notificar(){
	$root = str_replace('\\', '/', dirname(__DIR__));
	require_once($root . '/Archivos de Ayuda PHP/conexion.php');

	$conexion = new Conexion();
	$database = $conexion->Conectar();
	$collection = $database->anuncios_dia;
	$Result = $collection->find()->toArray();

	foreach($Result as $res){
		$id_Empresa = $res["id_empresa"];
		$bormes_agregados = $res["bormes_agregados"];
		$nombre_empresa = $res["nombre_empresa"];

		//$collection = $database->empresas_diario;
		$collection = $database->empresas;
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
}*/
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

	function EnviarEmail($Correos, $Anuncios_BORME, $Nombre_Empresa){
        
        $message = "
		<div style='font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; width: 80%; text-align: center;'>
		<div style='color:#3F388D;font-size:19px;line-height:32px;
					border-bottom: 1px solid #d1d1d1;'>
			Alerta de cambios
		</div>
		<br>
	
		<div style='font-size:16px;line-height:21px;color:#141823; padding-top: 5px;'>
		La empresa <b> ".$Nombre_Empresa." </b> ha tenido algunos cambios:</div>
	
		<div style='font-size:16px;line-height:21px;color:#141823; padding-top: 30px;
        padding-bottom: 30px;'>
		".$Anuncios_BORME."</div>
	
		<div style='sans-serif;font-size:11px;color:#aaaaaa;line-height:16px;
		border-top: 1px solid #d1d1d1;'>
		A fin de proteger tu cuenta, no reenvíes este correo electrónico.
		</div>
		</div>";
		 
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
			$mail->CharSet = 'UTF-8';  
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