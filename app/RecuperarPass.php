<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//require '../Archivos de Ayuda PHP/conexion.php';
//require_once($_SERVER['DOCUMENT_ROOT'] . '/AlertaEmpresas/Archivos de Ayuda PHP/conexion.php');
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

$Email = $_POST["Email"];
//$Email = "sandol1jose@gmail.com";

$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->Clientes;

EnviarEmail($Email, $collection);

	function EnviarEmail($Email, $collection){
		 
		$Pass = generarCodigo(5);
		$message = "
		<html>
		<head>
		<title>Cambio de clave</title>
		</head>
		<body>
		<h2>Este es tu codigo:</h2>
		<p>Ha solicitado restablecer su contraseña para la cuenta de AlertaEmpresas asociada con esta 
		dirección de correo electrónico (".$Email."). Para obtener el código de 
		restablecimiento de contraseña, por favor copie este código y péguelo en la aplicación:</p>
		<H1>".$Pass."</H1><br><br>
		<p>Si no realizó la solicitud, puede ignorar este correo electrónico y no hacer nada. 
		Otro usuario probablemente ingresó su dirección de correo electrónico por error al 
		intentar restablecer una contraseña.</p><br>
		<p>Alerta Empresas</p>
		<p>https://www.alertaempresas.com/</p>
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
			$mail->Port = 587;
			//Recipients
			$mail->setFrom('soporte@jumpgt.com', 'Jump GT');
			$mail->addAddress($Email);                 					//Add a recipient
		
			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Instrucciones para cambiar su clave de Alerta Empresas';
			$mail->Body    = $message;
			//$mail->AltBody = 'Enviado desde 000webhost.com';
			
			if(VerificarEmail($collection, $Email) == 1){ //Si existe el email
				if(GuardarCodigo($Pass, $collection, $Email) == 1){
					$_SESSION["Alerta"] = "CodpassSend"; //Codigo de pass enviado
					$_SESSION["Correo"] = $Email;
					$mail->send();
					header('Location: ../Login/CambiarPass.php');
				}
			}else{
				//El correo no existe
				$_SESSION["Alerta"] = "MailNoExist";
				header('Location: ../Login/RecuperarPass.php');
			}
			//echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Lo sentimos, ha ocurrido un error";
			echo $e;
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

	function GuardarCodigo($Codigo, $collection, $Email){
		//guarda el codigo para cambiar contraseña

        $filter = ["email" => $Email];
        $FechaMilis = new MongoDB\BSON\UTCDatetime(strtotime(date("Y-m-d h:i:s"))*1000);
        $document = [
            '$addToSet' => [
                "codigo_update_pass" => [
                    "codigo" => $Codigo,
                    "fecha" => $FechaMilis,
                    "confirmacion" => false
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

	function VerificarEmail($collection, $Email){

		$filter = ["email" => $Email];
		$Result = $collection->findOne($filter);

		if($Result != NULL){
			//SI EXISTE EL CORREO
			return 1;
		}else{
			//NO EXISTE EL CORREO
			return 0;
		}
	}

	function generarCodigo($longitud) {
		$key = '';
		//$pattern = '1234567890';s
		//$pattern = '1234567890ABCDEFGHIJKLMNPOQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+#@$%&=';
		$pattern = '1234567890ABCDEFGHIJKLMNPOQRSTUVWXYZ';
		$max = strlen($pattern)-1;
		for($i=0;$i < $longitud;$i++) $key .= $pattern[mt_rand(0,$max)];
		return $key;
	}
?>