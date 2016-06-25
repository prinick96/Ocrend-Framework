<?php

final class Lostpass extends Models implements OCREND {

	public function __construct() {
		parent::__construct();
	}

	final public function RepairPass(array $data) : array {

		$mail = $this->db->scape($data['email']);
		$user = $this->db->select('id,nombre','users',"email='$mail'",'LIMIT 1');

		if(false == $user) {
			$success = 0;
			$message = 'El <b>email</b> introducido no existe.';
		} else {
			$id = $user[0]['id'];
			$u = uniqid();
			$keypass = time();

			$HTML = '
			<html>
			<head>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
			</head>
			<body style="font-family: Verdana;">
				<section>
					Hola <b>'. $user[0]['nombre'] .'</b>, has solicitado recuperar tu contraseña perdida, si no has realizado esta acción no necesitas hacer nada.
					<br />
					<br />
					Para cambiar tu contraseña has <a href="'. URL .'lostpass/cambiar/'.$keypass.'" target="_blank">clic aquí</a>.
				</section>
			</body>
			</html>';

			$email = Func::send_mail($mail,$user[0]['nombre'],$HTML,'Recuperar contraseña perdida');
			if(true === $email) {
				$e = array(
					'keypass' => $keypass,
					'keypass_tmp' => $u
				);
				$this->db->update('users',$e,"id='$id'");
				$success = 1;
				$message = 'Hemos enviado un email a <b>' . $mail . '</b> para recuperar su contraseña.';
			} else {
				$success = 0;
				$message = $email;
			}

		}

		return array('success' => $success, 'message' => $message);
	}

	final public function UpdatePass() {
		$u = $this->db->select('id,keypass_tmp','users',"keypass='$this->id' AND keypass <> '0'",'LIMIT 1');

		if(false != $u) {

			Helper::load('strings');

			$id = $u[0]['id'];
			$pass = $u[0]['keypass_tmp'];
			$hash = Strings::hash($pass);

			$e = array(
				'keypass' => 0,
				'keypass_tmp' => '',
				'pass' => $hash
			);
			$this->db->update('users',$e,"keypass='$this->id' AND keypass <> '0' AND id='$id'");

			return $pass;
		}

		return false;
	}

	public function __destruct() {
		parent::__destruct();
	}
}

?>
