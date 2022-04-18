<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

	function EnviarEmail($Email, $collection){
	 
		$Codigo = BuscarCodigo($collection, $Email);
		//$Codigo = 2153;
		$message = "
		<html>
		<head>
		<title>Confirmación de correo electrónico</title>
		</head>
		<body>
		<h2>Debes confirmar tu correo electrónico</h2>
		<p>Ingresa el siguiente codigo para verificar tu correo electrónico</p>
		<H1>".$Codigo."</H1>
		<p>Alertaempresas.com</p>
		</body>
		</html>";
		 
		require '../Librerias/PHPMailer-master/src/Exception.php';
		require '../Librerias/PHPMailer-master/src/PHPMailer.php';
		require '../Librerias/PHPMailer-master/src/SMTP.php';
		
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
			$mail->Subject = 'Confirmacion de correo electronico';
			$mail->Body    = $message;
			//$mail->AltBody = 'Enviado desde 000webhost.com';
		
			$mail->send();
			//echo 'Message has been sent';
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