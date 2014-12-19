<?php
$json = ['load' => true];
$params = [
	'name' => FILTER_SANITIZE_STRING,
	'mail' => FILTER_VALIDATE_EMAIL,
	'direccion' => FILTER_SANITIZE_STRING,
	'distrito' => FILTER_SANITIZE_STRING,
	'telefono' => FILTER_SANITIZE_STRING,
	'name_deliver' => FILTER_SANITIZE_STRING,
	'direccion_deliver' => FILTER_SANITIZE_STRING,
	'distrito_deliver' => FILTER_SANITIZE_STRING,
	'telefono_deliver' => FILTER_SANITIZE_STRING
];

$values = filter_input_array(INPUT_POST, $params);

if (empty($values['name']))
{
	$json = ['load' => true, 'error_message' => 'El nombre y apellido es requerido'];
}
else if (!$values['mail'])
{
	$json = ['load' => true, 'error_message' => 'Debe poner un correo válido'];
}
else if (empty($values['distrito']))
{
	$json = ['load' => true, 'error_message' => 'Elija un distrito'];
}
else if (empty($values['telefono']))
{
	$json = ['load' => true, 'error_message' => 'El teléfono es requerido'];
}
else if (empty($values['name_deliver']))
{
	$json = ['load' => true, 'error_message' => 'El nombre y apellido de la persona que recibirá la encomienda'];
}
else if (empty($values['direccion_deliver']))
{
	$json = ['load' => true, 'error_message' => 'Escriba la dirección donde entregará la encomienda'];
}
else if (empty($values['distrito_deliver']))
{
	$json = ['load' => true, 'error_message' => 'Elija el distrito donde entregará a encomienda'];
}
else if (empty($values['telefono_deliver']))
{
	$json = ['load' => true, 'error_message' => 'Escriba le teléfono de la persona que recibirá la encomienda'];
}
else
{
	require 'mailer/PHPMailerAutoload.php';

	$mail = new PHPMailer(true);

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	try {
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = 'smtp.mandrillapp.com';
		$mail->SMTPSecure = 'tls';
		$mail->Username = '';
		$mail->Password = '';
		$mail->Port = 587;

		$mail->From = $values['email'];
		$mail->FromName = $values['name'];
		$mail->addAddress('blue360peru@gmail.com', 'Blue360');
		$mail->addReplyTo('no-reply@blue360.com', 'Blue360');

		$mail->isHTML(true);

		$mail->Subject = 'Enviado desde la web de Blue360';
		$mail->Body    = nl2br('<br>Teléfono: </b>'.$values['phone'].'<br><br><hr>'.$values['message']);
		$mail->AltBody = $values['message'];

		if(!$mail->send()) {
			$json = ['load' => true, 'error_message' => 'El mensaje no pudo ser enviado, intentelo de nuevo, error: '.$mail->ErrorInfo];
		} else {
		    $json['success_message'] = 'Tu mensaje ha sido enviado';
		}
	} catch (phpmailerException $pex) {
		$json = ['load' => false, 'error_message' => $pex->getMessage()];
	}
}

echo json_encode($json);