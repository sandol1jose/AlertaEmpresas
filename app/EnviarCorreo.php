<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

	function EnviarEmail($Email, $collection){
	 
		$Codigo = BuscarCodigo($collection, $Email);
		//$Codigo = 2153;
		$message = "
		<div style='font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; width: 80%; text-align: center;'>
		<div style='color:#3F388D;font-size:19px;line-height:32px;
					border-bottom: 1px solid #d1d1d1;'>
			Acción requerida: confirma tu cuenta de Alerta Empresas
		</div>
		<br>
	
		<div style='font-size:16px;line-height:21px;color:#141823; padding-top: 30px;'>
					!Hola!</div>
	
		<div style='font-size:16px;line-height:21px;color:#141823; padding-top: 5px;'>
		Te registraste recientemente en Alerta Empresas. <br>
		Para completar tu registro en Alerta Empresas, confirma tu cuenta.</div>
	
		<div style='font-size:16px;line-height:21px;color:#141823; padding-top: 30px;'>
		Ingresa el siguiente código, para verificar tu correo electrónico.</div>
	
		<div style='font-size:30px;line-height:50px;color:#141823; 
		padding-top: 30px; font-weight: bold; padding-bottom: 50px;'>
		".$Codigo."</div>
	
		<div style='sans-serif;font-size:11px;color:#aaaaaa;line-height:16px;
		border-top: 1px solid #d1d1d1;'>
		Se envió este mensaje a
		<a href='mailto:".$Email."'
		style='color:#3b5998;text-decoration:none'
		target='_blank'>".$Email."</a> 
		por pedido tuyo. <br>
		A fin de proteger tu cuenta, no reenvíes este correo electrónico.
		</div>
		</div>";
		 
		require '../Librerias/PHPMailer-master/src/Exception.php';
		require '../Librerias/PHPMailer-master/src/PHPMailer.php';
		require '../Librerias/PHPMailer-master/src/SMTP.php';
		
		//Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		
		try {
			//Server settings
			$mail->SMTPDebug = 0;                      //Enable verbose debug output
			//$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'alertaempresas.com';                       //Set the SMTP server to send through
			//$mail->Host       = 'smtp.hostinger.com';                       //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = 'support@alertaempresas.com';             //SMTP username
			$mail->Password   = 'Suport7859';                  //SMTP password

			/*$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);*/
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			//$mail->Port       = 465;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->Port       = 587;
			$mail->AddCustomHeader("List-Unsubscribe:<mailto:suppport@alertaempresas.com>,
			<https://alertaempresas.com/app/unsubscribe.php?identifier=".$Email.">"); 
			//Recipients
			$mail->setFrom('support@alertaempresas.com', 'Alerta Empresas');
			$mail->addAddress($Email);                 					//Add a recipient
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
			$mail->Subject = 'Confirmación de correo electrónico';
			$mail->Body    = $message;
			//$mail->AltBody = 'Enviado desde 000webhost.com';
		
			$mail->send();
			//echo 'Message has been sent<br>';
			//echo var_dump($mail);
		} catch (Exception $e) {
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

    function BuscarCodigo($collection, $Email){
		$Codigo = generarCodigo(5);//Generamos un codigo nuevo

		//Lo buscamos en la base de datos
		$filter = ["email" => $Email];
		$Result = $collection->findOne($filter);

        $encontrado = false;
        if($Result["verificacion"] != NULL){
            foreach($Result["verificacion"] as $codigo){
                $clave = $codigo["clave"];
                if($clave == $Codigo){
                    $encontrado = true;
                    break;
                }
            }
        }

        if($encontrado == true){
            BuscarCodigo($collection, $Email);
        }else{
            $id_Cliente = $Result["_id"];
            if(GrabarCodigo($Codigo, $collection, $id_Cliente) == 1){
				return $Codigo;
			}else{
				BuscarCodigo($collection, $Email);
			}
        }
	}

    
	function GrabarCodigo($Codigo, $collection, $id_Cliente){
		
        $filter = ["_id" => $id_Cliente];
        $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime(date("Y-m-d h:i:s"))*1000);
        $document = [
            '$addToSet' => [
                "verificacion" => [
                    "clave" => $Codigo,
                    "fecha" => $FechaMilis,
                    "verificado" => false
                ]
            ]
        ];
        $Result = $collection->updateOne($filter, $document);
			
		if($Result->getModifiedCount() == 1){
			//SE AGREGO CORRECTAMENTE AL CLIENTE
			return 1;
		}else{
			return 0;
		}
	}

	function generarCodigo($longitud) {
		$key = '';
		//$pattern = '1234567890';
		$pattern = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
		$max = strlen($pattern)-1;
		for($i=0;$i < $longitud;$i++){
            $key .= $pattern[mt_rand(0,$max)];
        }
		return $key;
	}
?>