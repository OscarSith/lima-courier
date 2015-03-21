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
	'descripcion' => FILTER_SANITIZE_STRING,
	'name_deliver' => FILTER_SANITIZE_STRING,
	'direccion_deliver' => FILTER_SANITIZE_STRING,
	'distrito_deliver' => FILTER_SANITIZE_STRING,
	'telefono_deliver' => FILTER_SANITIZE_STRING,
	'entrega' => FILTER_SANITIZE_STRING
];

$values = filter_input_array(INPUT_POST, $params);

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
else
{
	require 'mailer/PHPMailerAutoload.php';

	$mail = new PHPMailer(true);

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$message = '<br>'
				.'<h3 style="color:#1989AC">Lugar de recojo del producto</h3>'
				.'<strong>FECHA: '.$value['fecha'].'</strong><br>'
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
				.'<b>Teléfono</b>: '.$values['telefono_deliver'].'<br><br>'
				.'<p style="color:red"><b>* Cobrar en el punto '.(isset($values['recojo']) ? 'de recojo' : 'de entrega').'</b></p>';

	try {
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = '';
		$mail->SMTPSecure = 'tls';
		$mail->CharSet = 'UTF-8';
		$mail->Username = '';
		$mail->Password = '';
		$mail->Port = 587;

		$mail->From = $values['correo'];
		$mail->FromName = $values['name'];
		$mail->addAddress('courier@limacourier.pe', 'Lima Courier');
		$mail->addAddress('rafaelmolina@limacourier.pe', 'Rafael Molina');
		$mail->addAddress('alexmay@limacourier.pe', 'Alexandre May');
		$mail->addReplyTo('no-reply@limacourier.com', 'Lima Courier');

		$mail->isHTML(true);

		$mail->Subject = 'Petición de envío o recojo - Web Lima Courier';
		$mail->Body    = $message;

		if($mail->send()) {
			$json = ['load' => true, 'error_message' => 'El mensaje no pudo ser enviado, intentelo de nuevo, error: '.$mail->ErrorInfo];
		} else {
		    $json['success_message'] = 'Su Solicitud está haciendo Procesada';
		}
	} catch (phpmailerException $pex) {
		$json = ['load' => false, 'error_message' => $pex->getMessage()];
	}
}


echo json_encode($json);