<?php
$json = ['load' => true];
$params = [
	'name' => FILTER_SANITIZE_STRING,
	'correo' => FILTER_VALIDATE_EMAIL,
	'direccion' => FILTER_SANITIZE_STRING,
	'distrito' => FILTER_SANITIZE_STRING,
	'telefono' => FILTER_SANITIZE_STRING,
	'fecha' => FILTER_SANITIZE_STRING,
	'recojo' => FILTER_SANITIZE_STRING,
	'entrega' => FILTER_SANITIZE_STRING,
	'descripcion' => FILTER_SANITIZE_STRING,
	'name_deliver' => FILTER_SANITIZE_STRING,
	'direccion_deliver' => FILTER_SANITIZE_STRING,
	'distrito_deliver' => FILTER_SANITIZE_STRING,
	'telefono_deliver' => FILTER_SANITIZE_STRING,
	'terminos' => FILTER_SANITIZE_STRING,
	'tipo-servicio' => FILTER_SANITIZE_STRING,
	'emails' => FILTER_SANITIZE_STRING,
];

$values = filter_input_array(INPUT_POST, $params);

$values['tipo-servicio'] = trim($values['tipo-servicio']);

if (empty($values['name']))
{
	$json = ['load' => false, 'error_message' => 'El nombre y apellido es requerido'];
}
else if (!$values['correo'])
{
	$json = ['load' => false, 'error_message' => 'Debe poner un correo válido'];
}
else if (empty($values['distrito']))
{
	$json = ['load' => false, 'error_message' => 'Elija un distrito'];
}
else if (empty($values['telefono']))
{
	$json = ['load' => false, 'error_message' => 'El teléfono es requerido'];
}
else if (empty($values['name_deliver']))
{
	$json = ['load' => false, 'error_message' => 'El nombre y apellido de la persona que recibirá la encomienda'];
}
else if (empty($values['direccion_deliver']))
{
	$json = ['load' => false, 'error_message' => 'Escriba la dirección donde entregará la encomienda'];
}
else if (empty($values['distrito_deliver']))
{
	$json = ['load' => false, 'error_message' => 'Elija el distrito donde entregará a encomienda'];
}
else if (empty($values['telefono_deliver']))
{
	$json = ['load' => false, 'error_message' => 'Escriba le teléfono de la persona que recibirá la encomienda'];
}
else if (!isset($values['recojo']) && !isset($values['entrega']))
{
	$json = ['load' => false, 'error_message' => 'Debe elegir si Paga "en el punto de recojo" ó "en el punto de entrega"'];
}
else if (empty($values['tipo-servicio']) || ($values['tipo-servicio'] != 'LC' && $values['tipo-servicio'] != 'NAL' && $values['tipo-servicio'] != 'INAL'))
{
	$json = ['load' => false, 'error_message' => 'Debe elegir el tipo de servicio'];
}
else if (!isset($values['terminos']))
{
	$json = ['load' => false, 'error_message' => 'Debe de aceptar los Terminos y Condiciones de Uso"'];
}
else
{
	$emails = explode(',', $values['emails']);
	$valid_emails = [];

	foreach ($emails as $key) {
		if (filter_var($key, FILTER_VALIDATE_EMAIL)) {
			$valid_emails[] = $key;
		}
	}

	require 'mailer/PHPMailerAutoload.php';

	$mail = new PHPMailer(true);

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	try {
		// $mail->isSMTP();
		// $mail->SMTPAuth = true;
		// $mail->Host = 'smtp.mandrillapp.com';
		// $mail->SMTPSecure = 'tls';
		// $mail->CharSet = 'UTF-8';
		// $mail->Username = 'larriega@gmail.com';
		// $mail->Password = '';
		// $mail->Port = 587;

		// $mail->From = $values['correo'];
		// $mail->FromName = $values['name'];
		// $mail->addAddress('courier@limacourier.pe', 'Lima Courier');
		// $mail->addAddress('contacto@limacourier.pe', 'Lima Courier - Colombia');
		// $mail->addAddress('rafaelmolina@limacourier.pe', 'Rafael Molina');
		// $mail->addAddress('alexmay@limacourier.pe', 'Alexandre May');
		// // $mail->addAddress('al.soriano.thais@gmail.com', 'Prueba de Calidad');
		// $mail->addAddress($values['correo'], $values['name']);
		// $mail->addReplyTo($values['correo'], $values['name']);
		foreach ($valid_emails as $key) {
			$mail->addAddress($key);
		}

		$filename = 'cache.txt';
		$rs = fopen($filename, 'r+');
		$number = fread($rs, filesize($filename));
		fclose($rs);
		unset($rs);
		$rs = fopen($filename, 'r+', 1);
		fwrite($rs, $number + 1);

		$codigo = $values['tipo-servicio'] . '-' . str_pad($number, 7, '0', STR_PAD_LEFT);
		$message = '<br>'
			.'<h3 style="color:#1989AC">Lugar de recojo del producto</h3>'
			.'<strong>FECHA: '.$values['fecha'].'</strong><br>'
			.'<b>Nombre</b>: '.$values['name'].'<br>'
			.'<b>Correo</b>: '.$values['correo'].'<br>'
			.'<b>Dirección</b>: '.$values['direccion'].'<br>'
			.'<b>Distrito</b>: '.$values['distrito'].'<br>'
			.'<b>Teléfono</b>: '.$values['telefono'].'<br>'
			.'<b>Mensaje:</b><br>'.nl2br($values['descripcion']).'<br><br>'
			.'<h3 style="color:#1989AC">Lugar de entrega del producto</h3>'
			.'<b>Nombre</b>: '.$values['name_deliver'].'<br>'
			.'<b>Dirección</b>: '.$values['direccion_deliver'].'<br>'
			.'<b>Distrito</b>: '.$values['distrito_deliver'].'<br>'
			.'<b>Teléfono</b>: '.$values['telefono_deliver'].'<br>'
			.'<b>Va enviar</b>: '.($values['tipo-paquete'] == 1 ? 'Documentos' : 'Paquetes').'<br>'
			.'<b>Tipo de Envío</b>: '.$values['tipo-servicio'].'<br><br>'
			.'<p style="color:red"><b>* Cobrar en el punto '.(isset($values['recojo']) ? 'de recojo' : 'de entrega').'</b></p>'
			.'Codigo de Servicio: ' . $codigo;

		// $mail->isHTML(true);
		// $mail->Subject = 'Petición de envío o recojo - Web Lima Courier';
		// $mail->Body    = $message;

		// para pruebas nomás..
		if (true) { //$mail->send()
			$json['success_message'] = 'La siguiente solicitud (o solicitudes) está siendo procesada, ' . $codigo;
		} else {
			$json = ['load' => true, 'error_message' => 'El mensaje no pudo ser enviado, intentelo de nuevo, error: '.$mail->ErrorInfo];
		}

	} catch (phpmailerException $pex) {
		$json = ['load' => false, 'error_message' => $pex->getMessage()];
	}
}


echo json_encode($json);