<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

/**
 * Helper con funciones útiles para trabajar con evío de correos mediante PHPMailer.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

final class Emails {

  /**
    * FUNCIÓN NO ACCESIBLE, USO ESTRICTO PARA UNA FUNCIÓN INTERNA DEL HELPER
    * Inicializa la clase PHPMailer y las configuraciones necesarias
    * Método privado utilizado en todo el Helper
    *
    * @param bool $is_smtp: Define si se hará la conexión a través de SMTP o no
    *
    * @return \PHPMailer un objeto de la clase PHPMailer
  */
  final private static function init(bool $is_smtp = true) : \PHPMailer {
    global $config;

    $mail = new \PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "quoted-printable";

    if ($is_smtp) {
      $mail->isSMTP();
      $mail->SMTPAuth = true;
      $mail->Host = $config['phpmailer']['host'];
      $mail->Username = $config['phpmailer']['user'];
      $mail->Password = $config['phpmailer']['pass'];
      $mail->Port = $config['phpmailer']['port'];
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPOptions = array(
          'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
          )
      );
    } else {
      $mail->isSendMail();
    }

    return $mail;
  }

  //------------------------------------------------

  /**
    * Envía un correo electrónico utilizando PHPMailer
    *
    * @param array $dest: Arreglo con la forma array(
    *                                           'email destinatario 1' => 'nombre destinatario 1',
    *                                           'email destinatario 2' => 'nombre destinatario 2'
    *                                            )
    * @param string $HTML: Contenido en HTML del email
    * @param string $titulo: Asunto del email
    * @param bool $is_smtp: Define si se hará la conexión a través de SMTP o no
    * @param array $adj: Arreglo con direccion local de los adjuntos a enviar, con la forma array(
    *                                                                                       'ruta archivo 1',
    *                                                                                       'ruta archivo 2'
    *                                                                                       )
    *
    * @return string|bool true si fue enviado correctamente, string con el Error descrito por PHPMailer
  */
  final public static function send_mail(array $dest, string $HTML, string $titulo, bool $is_smtp = true, array $adj = []) {
    global $config;

    $mail = self::init($is_smtp);
    $mail->setFrom($config['phpmailer']['user'], $config['site']['name']);
    foreach ($dest as $email => $name) {
        $mail->addAddress($email, $name);
    }
    $mail->isHTML(true);
    $mail->Subject = $titulo;
    $mail->Body    = $HTML;

    if (sizeof($adj)) {
      foreach ($adj as $ruta) {
        $mail->AddAttachment($ruta);
      }
    }

    if (!$mail->send()) {
      return $mail->ErrorInfo;
    }

    return true;
  }

  //------------------------------------------------

  /**
   * Plantilla estándar que muestra de forma amigable el texto, utiliza bootstrap
   *
   * @param string $content: Cadena de texto en HTML, puede ser en bootstrap
   *
   * @return string con el HTML para enviar
   */
  final public static function plantilla(string $content) : string {
    return '
    <html>
    <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    </head>
    <body style="font-family: Verdana;">
      <section>
        '.$content . '
      </section>
    </body>
    </html>';
  }

}
