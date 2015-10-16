<?php
$json = ['load' => true];
$params = [
	'nombres' => FILTER_SANITIZE_STRING,
	'correo' => FILTER_VALIDATE_EMAIL,
	'telefono' => FILTER_SANITIZE_STRING,
	'estado' => FILTER_SANITIZE_STRING,
	'pais' => FILTER_SANITIZE_STRING,
	'ciudad' => FILTER_SANITIZE_STRING,
];

$values = filter_input_array(INPUT_POST, $params);

if (empty($values['nombres']))
{
	$json = ['load' => false, 'error_message' => 'El nombre y apellido es requerido'];
}
else if (!$values['correo'])
{
	$json = ['load' => false, 'error_message' => 'Debe poner un correo válido'];
}
else if (empty($values['pais']))
{
	$json = ['load' => false, 'error_message' => 'Indique su País'];
}
else if (empty($values['estado']))
{
	$json = ['load' => false, 'error_message' => 'Indique el estado de su País'];
}
else if (empty($values['telefono']))
{
	$json = ['load' => false, 'error_message' => 'El teléfono es requerido'];
}
else
{
	require 'mailer/PHPMailerAutoload.php';

	$mail = new PHPMailer(true);

	try {
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = 'smtp.mandrillapp.com';
		$mail->SMTPSecure = 'tls';
		$mail->CharSet = 'UTF-8';
		$mail->Username = 'larriega@gmail.com';
		$mail->Password = '';
		$mail->Port = 587;

		$mail->From = $values['correo'];
		$mail->FromName = $values['nombres'];
		$mail->addAddress('courier@limacourier.pe', 'Lima Courier');
		$mail->addAddress('contacto@limacourier.pe', 'Lima Courier - Colombia');
		$mail->addAddress('rafaelmolina@limacourier.pe', 'Rafael Molina');
		$mail->addAddress('alexmay@limacourier.pe', 'Alexandre May');
		// $mail->addAddress($values['correo'], $values['nombres']);
		$mail->addReplyTo($values['correo'], $values['nombres']);
		$message = '<br>'
			.'<h3 style="color:#1989AC">Franquicia</h3>'
			.'<b>Nombre</b>: '.$values['nombres'].'<br>'
			.'<b>Correo</b>: '.$values['correo'].'<br>'
			.'<b>Teléfono</b>: '.$values['telefono'].'<br>'
			.'<b>País</b>: '.$values['pais'].'<br>'
			.'<b>Estado</b>: '.$values['estado'].'<br>'
			.'<b>Ciudad</b>: '.$values['ciudad'];

		$mail->isHTML(true);
		$mail->Subject = 'Formulario de Franquicia (Lima Courier)';
		$mail->Body    = $message;

		// para pruebas nomás..
		if (true) { // $mail->send()
			$json['success_message'] = 'Su información ha sido enviado al área correspondiente.';
		} else {
			$json = ['load' => true, 'error_message' => 'El mensaje no pudo ser enviado, intentelo de nuevo, error: '.$mail->ErrorInfo];
		}

	} catch (phpmailerException $pex) {
		$json = ['load' => false, 'error_message' => $pex->getMessage()];
	}
}


echo json_encode($json);