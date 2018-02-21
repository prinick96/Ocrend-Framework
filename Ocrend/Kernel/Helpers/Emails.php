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
   * Ruta en la que están guardados los templates
   * 
   * @var string
   * 
   */
  private static $template_route = API_INTERFACE . 'app/templates/email_templates/';

  /**
   * Lista de plantillas
   * 
   * @var array
   * 
   */
  const TEMPLATES = [
    'tpl-btn.html',
    'tpl-no-btn.html'
  ];

  //------------------------------------------------

  /**
    * Envía un correo electrónico utilizando PHPMailer
    *
    * @param array $dest: Arreglo con la forma array(
    *                                           'email destinatario 1' => 'nombre destinatario 1',
    *                                           'email destinatario 2' => 'nombre destinatario 2'
    *                                            )
    * @param array $content: sArreglo con el contenido por seciones con la forma
    *                                             array(
    *                                               '{{title}}' => 'Titulo',
    *                                               '{{content}}' => '<p>Contenido</p><p>Etc...</p>'
    *                                             )
    * @param int $template: Template elegido, por defecto es el primero (0)
    * @param array $adj: Arreglo con direccion local de los adjuntos a enviar, con la forma array(
    *                                                                                       'ruta archivo 1',
    *                                                                                       'ruta archivo 2'
    *                                                                                       )
    * @throws \RuntimeException en caso de algúnproblema
    * @return string|bool true si fue enviado correctamente, false si no
  */
  final public static function send(array $dest, array $content, int $template = 0, array $adj = array()) {
    global $config;
    
    # Transporte
    $transport = (new \Swift_SmtpTransport($config['mailer']['host'], $config['mailer']['port'], 'tls'))
    ->setUsername($config['mailer']['user'])
    ->setPassword($config['mailer']['pass'])
    ->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false)));

    # Mailer
    $mailer = new \Swift_Mailer($transport);
    
    # El mensaje
    $message = new \Swift_Message();
    $message->setSubject(array_key_exists('{{title}}', $content) ? $content['{{title}}'] : $config['site']['name']);
    $message->setBody(self::loadTemplate($content, $template), 'text/html');
    $message->setFrom([ $config['mailer']['user'] => $config['site']['name'] ]);
    $message->setTo($dest);

    if (sizeof($adj)) {
      foreach ($adj as $ruta) {
        $message->attach( \Swift_Attachment::fromPath($ruta) );
      }
    }

    if ($mailer->send($message)) {
      return true;
    }

    return false;

    
  }

  //------------------------------------------------
  /**
   * Carga un archivo para ser enviado por email
   * 
   * @param array $content: Contenido del archivo
   * @param int $template: template a enviar
   * 
   * @return string con el template a enviar
   */
  final private function loadTemplate(array $content, int $template) : string {
    # Verificar que existe la plantilla
    if (!array_key_exists($template, self::TEMPLATES)) {
      throw new \RuntimeException('La plantilla seleccionada no se encuentra.');
    }

    # Cargar contenido
    $tpl = Files::read_file(self::$template_route . self::TEMPLATES[$template]);

    # Remplazar el contenido
    foreach ($content as $index => $html) {
      $tpl = str_replace($index, $html, $tpl);
    }
    return $tpl;
  }

  //------------------------------------------------
  /**
   * Cambia la ruta de donde deben tomarse los templates
   * 
   * @param string $new_route: Nueva ruta
   * 
   * @return void
   */
  final public static function setTemplateRoute(string $new_route) {
    # Verificar que exista el nuevo directorio
    if (!is_dir($new_route)) {
      throw new \RuntimeException('la ruta '.$new_route .' no existe');
    }

    # Cambiamos el directorio de los template
    self::$template_route = $new_route;
  }

}
