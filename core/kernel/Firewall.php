<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

# Basado en http://www.php-firewall.info/
final class Firewall {

  //------------------------------------------------

  const FCONF = array(
    'WEBMASTER_EMAIL' => 'test@ocrend.com',
    'PUSH_MAIL' => false,
    'LOG_FILE' => '__logs__',
    'PROTECTION_UNSET_GLOBALS' => true,
    'PROTECTION_RANGE_IP_DENY' => false,
    'PROTECTION_RANGE_IP_SPAM' => false,
    'PROTECTION_URL' => true,
    'PROTECTION_REQUEST_SERVER' => true,
    'PROTECTION_BOTS' => true,
    'PROTECTION_REQUEST_METHOD' => true,
    # el primer true corresponde al uso de esta protección para la API REST
    # el segundo true corresponde al uso de esta protección para forma de acceso común
    'PROTECTION_DOS' => IS_API ? true : true,
    'PROTECTION_UNION_SQL' => true,
    'PROTECTION_CLICK_ATTACK' => true,
    'PROTECTION_XSS_ATTACK' => true,
    'PROTECTION_COOKIES' => true,
    'PROTECTION_COOKIES_LOGS' => true,
    'PROTECTION_POST' => true,
    'PROTECTION_POST_LOGS' => true,
    'PROTECTION_GET' => true,
    'PROTECTION_GET_LOGS' => true,
    'PROTECTION_SERVER_OVH' => true,
    'PROTECTION_SERVER_KIMSUFI' => true,
    'PROTECTION_SERVER_DEDIBOX' => true,
    'PROTECTION_SERVER_DIGICUBE' => true,
    'PROTECTION_SERVER_OVH_BY_IP' => true,
    'PROTECTION_SERVER_KIMSUFI_BY_IP' => true,
    'PROTECTION_SERVER_DEDIBOX_BY_IP' => true,
    'PROTECTION_SERVER_DIGICUBE_BY_IP' => true,
    'PROTECTION_ROUTER_STRICT' => false
   );

  /*
    Lista de IP's bloqueadas. Si la parte de la IP de tu servidor, se encuentra aquí, debes quitarla.
    Para saber la IP de tu servidor, basta con hacer desde cualquier terminal
    ping -a tupagina.com
  */
  const IPLIST = array(
    # Si los dos primeros dígitos de tu IP, coinciden con alguno de estos, elimínalo.
    'SERVER_OVH_BY_IP' => ['87.98','91.121','94.23','213.186','213.251'],
    'DEDIBOX_BY_IP' => '88.191',
    'DIGICUBE_BY_IP' => '95.130',
    # Si el primer dígito de tu IP, coinciden con alguno de estos, elimínalo.
    'RANGE_IP_DENY' => ['0', '1', '2', '5', '10', '14', '23', '27', '31', '36', '37', '39', '42', '46',
    '49', '50', '100', '101', '102', '103', '104', '105', '106', '107', '114', '172', '176', '177', '179',
    '181', '185', '192', '223', '224'],
    'RANGE_IP_SPAM' => ['24', '186', '189', '190', '200', '201', '202', '209', '212', '213', '217', '222']
  );

   //------------------------------------------------

  /**
    * Elimina todas las variables globales no permitidas
    *
    * @return void
  */
  private function unset_globals() {
    if(ini_get('register_globals')) {
        $allow = array(
        '_ENV' => 1,
        '_GET' => 1,
        '_POST' => 1,
        '_COOKIE' => 1,
        '_FILES' => 1,
        '_SERVER' => 1,
        '_REQUEST' => 1,
        'GLOBALS' => 1
      );
      foreach ($GLOBALS as $i => $val) {
        if(!isset($allow[$i])) {
          unset($GLOBALS[$i]);
        }
      }
    }
  }

  //------------------------------------------------

  /**
    * Sana las variables gloables retirando PHP y HTML de su contenido
    *
    * @param string $s: index de la variable a sanar
    *
    * @return retorna $r sanada
  */
  private function getEnv(string $s) {

    if(isset($_SERVER[$s])) {
      return strip_tags($_SERVER[$s]);
    } else if(isset($_ENV[$s])) {
      return strip_tags($_ENV[$s]);
    } else if(getenv($s)) {
      return strip_tags(getenv($s));
    } else if(function_exists('apache_getenv') and apache_getenv($s, true)) {
      return strip_tags(apache_getenv($s, true));
    }

    return '';
  }

  //------------------------------------------------

  /**
    * Obtiene dirección de la página que emplea el agente de usuario para la pagina actual
    *
    * @return devuelve $_SERVER['HTTP_REFERER'] sanado
  */
  private function getReferer() {
    return $this->getEnv('HTTP_REFERER');
	}

  //------------------------------------------------

  /**
    * Obtiene ip
    *
    * @return devuelve la IP
  */
  private function getIp() {
  	if ($this->getEnv('HTTP_X_FORWARDED_FOR') ) {
  		return $this->getEnv('HTTP_X_FORWARDED_FOR');
  	} elseif ( $this->getEnv('HTTP_CLIENT_IP') ) {
  		return $this->getEnv('HTTP_CLIENT_IP');
  	}

    return $this->getEnv('REMOTE_ADDR');
  }

  //------------------------------------------------

  /**
    * Obtiene agente de usuario
    *
    * @return devuelve el agente de usuario
  */
  private function getUserAgent() {
		if($this->getEnv('HTTP_USER_AGENT')) {
      return $this->getEnv('HTTP_USER_AGENT');
    }
		return '-';
	}

  //------------------------------------------------

  /**
    * Obtiene la query de la petición de la página
    *
    * @return devuelve la cadena de la consulta de la petición de la página.
  */
  private function getQueryString() {
    if(self::FCONF['PROTECTION_ROUTER_STRICT']) {
      return str_replace('%09', '%20', $_SERVER['REQUEST_URI']);
    }

    if($this->getEnv('QUERY_STRING')) {
      return str_replace('%09', '%20', $this->getEnv('QUERY_STRING'));
    }

    return '';
	}

  //------------------------------------------------

  /**
    * Obtiene 'GET', 'HEAD', 'POST', 'PUT'.
    *
    * @return devuelve el método de petición actual.
  */
  private function getRequestMethod() {
    return $this->getEnv('REQUEST_METHOD');
  }

  //------------------------------------------------

  /**
    * Obtiene nombre del host de Internet
    *
    * @return devuelve el host de Internet según la IP actual
  */
  private function getHostByAddr() {
    if(self::FCONF['PROTECTION_SERVER_OVH'] or self::FCONF['PROTECTION_SERVER_KIMSUFI'] or self::FCONF['PROTECTION_SERVER_DEDIBOX'] or self::FCONF['PROTECTION_SERVER_DIGICUBE']) {
      if(!isset($_SESSION['app_firewall_gethostbyaddr']) or empty($_SESSION['app_firewall_gethostbyaddr'])) {
        $_SESSION['app_firewall_gethostbyaddr'] = gethostbyaddr($this->getIp());
      }
      return strip_tags($_SESSION['app_firewall_gethostbyaddr']);
    }
    return '';
  }

  //------------------------------------------------

  /**
    * Envía un email de alerta por un ataque, SIN utilizar phpmailer
    *
    * @param string $subject: Asunto
    * @param string $msg: Mensaje
    *
    * @return void
  */
  private function pushEmail(string $subject, string $msg) {
    $headers = "From: Ocrend Framework Firewall: ". self::FCONF['WEBMASTER_EMAIL'] ." <".self::FCONF['WEBMASTER_EMAIL'].">\r\n"
			."Reply-To: ".self::FCONF['WEBMASTER_EMAIL']."\r\n"
			."Priority: urgent\r\n"
			."Importance: High\r\n"
			."Precedence: special-delivery\r\n"
			."Organization: Ocrend Framework\r\n"
			."MIME-Version: 1.0\r\n"
			."Content-Type: text/plain\r\n"
			."Content-Transfer-Encoding: 8bit\r\n"
			."X-Priority: 1\r\n"
			."X-MSMail-Priority: High\r\n"
			."X-Mailer: PHP/" . phpversion() ."\r\n"
			."X-Firewall: 1.0 by Ocrend Framework\r\n"
			."Date:" . date("D, d M Y H:s:i") . " +0100\n";
    if(self::FCONF['WEBMASTER_EMAIL'] != '') {
      mail(self::FCONF['WEBMASTER_EMAIL'], $subject, $msg, $headers);
    }
  }

  //------------------------------------------------

  /**
    * Crea un historial de Log por un ataque actual, además envía un email si está activa la acción
    *
    * @param string $type: Tipo de ataque
    * @param string $ip: Ip atacante
    * @param string $user_agent: Agente de usuario atacante
    * @param string $referer: Referer
    *
    * @return void
  */
  private function Logs( string $type, string $ip, string $user_agent, string $referer) {
    $f = fopen((IS_API ? '../' : './') . self::FCONF['LOG_FILE'] .'.log', 'a');
    $msg = date('j-m-Y H:i:s') .' | ' . $type . ' | IP: '. $ip .' ] | DNS: '. gethostbyaddr($ip).' | Agent: '. $user_agent . PHP_EOL;
    fwrite($f, $msg);
    fclose($f);
    if (self::FCONF['PUSH_MAIL']) {
      $this->pushEmail('Alert Ocrend Framework Firewall '.strip_tags( $_SERVER['SERVER_NAME'] ) , " Firewall logs of ".strip_tags( $_SERVER['SERVER_NAME'] )."\n".str_replace('|', "\n", $msg ) );
    }
  }

  //------------------------------------------------

  const MSG_PROTECTION_OVH = 'Protection OVH Server active, this IP range is not allowed.';
  const MSG_PROTECTION_KIMSUFI = 'Protection KIMSUFI Server active, this IP range is not allowed.';
  const MSG_PROTECTION_DEDIBOX = 'Protection DEDIBOX Server active, this IP range is not allowed.';
  const MSG_PROTECTION_DEDIBOX_IP = 'Protection DEDIBOX Server active, this IP is not allowed.';
  const MSG_PROTECTION_DIGICUBE = 'Protection DIGICUBE Server active, this IP range is not allowed.';
  const MSG_PROTECTION_DIGICUBE_IP = 'Protection DIGICUBE Server active, this IP is not allowed.';
  const MSG_PROTECTION_BOTS = 'Bot attack detected.';
  const MSG_PROTECTION_CLICK = 'Click attack detected.';
  const MSG_PROTECTION_DOS = 'Invalid user agent.';
  const MSG_PROTECTION_OTHER_SERVER = 'Posting from another server not allowed.';
  const MSG_PROTECTION_REQUEST = 'Invalid request method check.';
  const MSG_PROTECTION_SPAM = 'Protection SPAM IPs active, this IP range is not allowed.';
  const MSG_PROTECTION_SPAM_IP = 'Protection died IPs active, this IP range is not allowed.';
  const MSG_PROTECTION_UNION = 'Union attack detected.';
  const MSG_PROTECTION_URL = 'Protection url active, string not allowed.';
  const MSG_PROTECTION_XSS = 'XSS attack detected.';

  //------------------------------------------------

  const CT_RULES = ['applet', 'base', 'bgsound', 'blink', 'embed', 'expression',
  'frame', 'javascript', 'layer', 'link', 'meta', 'object', 'onabort', 'onactivate',
  'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut',
  'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint',
  'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange',
  'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect',
  'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete',
  'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave',
  'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
  'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout',
  'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
  'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave',
  'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup',
  'onmousewheel', 'onmove', 'onmoveend', 'onmovestart',
  'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset',
  'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit',
  'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange',
  'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload',
  'script', 'style', 'title', 'vbscript', 'xml'];

  //------------------------------------------------

  /**
    * Inicializador del Firewall
    *
    * @return void
  */
  public function __construct() {

    //------------------------------------------------

    $GET_QUERY_STRING = strtolower($this->getQueryString());
    $USER_AGENT = $this->getUserAgent();
    $GET_IP = $this->getIp();
    $GET_HOST = $this->getHostByAddr();
    $GET_REFERER = $this->getReferer();
    $GET_REQUEST_METHOD = $this->getRequestMethod();
    $REGEX_UNION = '#\w?\s?union\s\w*?\s?(select|all|distinct|insert|update|drop|delete)#is';

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_OVH'] and stristr($GET_HOST,'ovh') and !stristr($GET_HOST,'dsl.ovh')) {
      $this->Logs('OVH Server list',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_OVH)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_OVH_BY_IP']) {
      $ip = explode('.', $GET_IP);
      if(sizeof($ip) > 1 and in_array($ip[0].'.'.$ip[1],self::IPLIST['SERVER_OVH_BY_IP'])) {
        $this->Logs('OVH Server IP',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_OVH)));
        }

        Func::redir();
        return;
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_KIMSUFI'] and stristr($GET_HOST ,'kimsufi')) {
      $this->Logs('KIMSUFI Server list',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_KIMSUFI)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_DEDIBOX'] and stristr($GET_HOST ,'dedibox')) {
      $this->Logs('DEDIBOX Server list',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_DEDIBOX)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_DEDIBOX_BY_IP']) {
      $ip = explode('.', $GET_IP);
      if(sizeof($ip) > 1 and $ip[0].'.'.$ip[1] == self::IPLIST['DEDIBOX_BY_IP']) {
        $this->Logs('DEDIBOX server IP',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_DEDIBOX_IP)));
        }

        Func::redir();
        return;
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_DIGICUBE'] and stristr($GET_HOST,'digicube')) {
      $this->Logs('DIGICUBE Server list',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_DIGICUBE)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_SERVER_DIGICUBE_BY_IP']) {
      $ip = explode('.',$GET_IP);
      if (sizeof($ip) > 1 and $ip[0].'.'.$ip[1] == self::IPLIST['DIGICUBE_BY_IP']) {
        $this->Logs('DIGICUBE Server IP',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_DIGICUBE_IP)));
        }

        Func::redir();
        return;
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_RANGE_IP_SPAM']) {
      $range_ip = explode('.',$GET_IP);
      if(in_array($range_ip[0],self::IPLIST['RANGE_IP_SPAM'])) {
        $this->Logs('IPs (ip:'.$range_ip[0].') Spam list (Visitar framework.ocrend.com/phpfirewall/)',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_SPAM)));
        }

        die(self::MSG_PROTECTION_SPAM);
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_RANGE_IP_DENY']) {
      $range_ip = explode('.',$GET_IP);
      if(in_array($range_ip[0],self::IPLIST['RANGE_IP_DENY'])) {
        $this->Logs('IPs (ip:'.$range_ip[0].') Reserved list (Visitar framework.ocrend.com/phpfirewall/)',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_SPAM_IP)));
        }

        die(self::MSG_PROTECTION_SPAM_IP);
      }
    }

    //------------------------------------------------

    if (self::FCONF['PROTECTION_COOKIES']) {
      foreach($_COOKIE as $i => $value) {
        if( $value != str_replace(self::CT_RULES, '*', $value)) {
          if(self::FCONF['PROTECTION_COOKIES_LOGS']) {
            $this->Logs('Cookie protect',$GET_IP,$USER_AGENT,$GET_REFERER);
          }
          $_COOKIE[$i] = htmlentities($value);
        }
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_POST'] and $_POST) {
      foreach($_POST as $value ) {
        if( $value != str_replace(self::CT_RULES, '*', $value) ) {
          if(self::FCONF['PROTECTION_POST_LOGS']) {
            $this->Logs('POST protect',$GET_IP,$USER_AGENT,$GET_REFERER);
          }
          unset($value);
        }
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_GET'] and $_GET) {
      foreach($_GET as $i => $value ) {
        if($value != str_replace(self::CT_RULES, '*', $value) ) {
          if(self::FCONF['PROTECTION_GET_LOGS']) {
            $this->Logs('GET protect',$GET_IP,$USER_AGENT,$GET_REFERER);
          }
          $_GET[$i] = htmlentities($value);
        }
      }
    }

    //------------------------------------------------

    if (self::FCONF['PROTECTION_URL']) {
      $ct_rules = ['absolute_path', 'ad_click', 'alert(', 'alert%20', ' and ', 'basepath', 'bash_history', '.bash_history',
      'cgi-', 'chmod(', 'chmod%20', '%20chmod', 'chmod=', 'chown%20', 'chgrp%20',
      'chown(', '/chown', 'chgrp(', 'chr(', 'chr=', 'chr%20', '%20chr', 'chunked',
      'cookie=', 'cmd', 'cmd=', '%20cmd', 'cmd%20', '.conf', 'configdir', 'config.php',
      'cp%20', '%20cp', 'cp(', 'diff%20', 'dat?', 'db_mysql.inc', 'document.location',
      'document.cookie', 'drop%20', 'echr(', '%20echr', 'echr%20', 'echr=',
      '}else{', '.eml', 'esystem(', 'esystem%20', '.exe',  'exploit', 'file\://',
      'fopen', 'fwrite', '~ftp', 'ftp:', 'ftp.exe', 'getenv', '%20getenv', 'getenv%20',
      'getenv(', 'grep%20', '_global', 'global_', 'global[', 'http:', '_globals',
      'globals_', 'globals[', 'grep(', 'g\+\+', 'halt%20', '.history', '?hl=',
      '.htpasswd', 'http_', 'http-equiv', 'http/1.', 'http_php', 'http_user_agent',
      'http_host', '&icq', 'if{', 'if%20{', 'img src', 'img%20src', '.inc.php', '.inc',
      'insert%20into', 'ISO-8859-1', 'ISO-', 'javascript\://', '.jsp', '.js', 'kill%20',
      'kill(', 'killall', '%20like', 'like%20', 'locate%20', 'locate(', 'lsof%20', 'mdir%20',
      '%20mdir', 'mdir(', 'mcd%20', 'motd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd',
      'mcd(', 'mrd(', 'mcd=', 'mod_gzip_status', 'modules/', 'mrd=', 'mv%20', 'nc.exe',
      'new_password', 'nigga(', '%20nigga', 'nigga%20', '~nobody', 'org.apache',
      '+outfile+', '%20outfile%20', '*/outfile/*',' outfile ','outfile',
      'password=', 'passwd%20', '%20passwd', 'passwd(', 'phpadmin',
      'perl%20', '/perl','p0hh',
      'ping%20', '.pl', 'powerdown%20', 'rm(', '%20rm', 'rmdir%20',
      'mv(', 'rmdir(', 'phpinfo()', '<?php', 'reboot%20', '/robot.txt' ,
      '~root', 'root_path', 'rush=', '%20and%20', '%20xorg%20', '%20rush',
      'rush%20', 'secure_site, ok', 'select%20', 'select from', 'select%20from',
      '_server', 'server_', 'server[', 'server-info', 'server-status', 'servlet',
      'sql=', '<script', '<script>', '</script','script>','/script', 'switch{',
      'switch%20{', '.system', 'system(', 'telnet%20', 'traceroute%20', '.txt',
      'union%20', '%20union', 'union(', 'union=', 'vi(', 'vi%20', 'wget', 'wget%20',
      '%20wget', 'wget(', 'window.open', 'wwwacl', ' xor ', 'xp_enumdsn',
      'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', '$_request', '$_get',
      '$request', '$get',  '&aim', '/etc/password','/etc/shadow',
      '/etc/groups', '/etc/gshadow', '/bin/ps', 'uname\x20-a',
      '/usr/bin/id', '/bin/echo', '/bin/kill',
      '/bin/', '/chgrp', '/usr/bin', 'bin/python',
      'bin/tclsh', 'bin/nasm', '/usr/x11r6/bin/xterm',
      '/bin/mail', '/etc/passwd', '/home/ftp', '/home/www', '/servlet/con', '?>', '.txt'];
      $check = str_replace($ct_rules, '*', $GET_QUERY_STRING);
      if($GET_QUERY_STRING != $check) {
        $this->Logs('URL protect',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_URL)));
        }

        Func::redir();
        return;
      }
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_REQUEST_SERVER'] and $GET_REQUEST_METHOD == 'POST'
    and isset($_SERVER['HTTP_REFERER']) and !stripos( $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'], 0 )) {
      $this->Logs('Posting another server',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_OTHER_SERVER)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if (self::FCONF['PROTECTION_BOTS']) {
  		$ct_rules = ['@nonymouse', 'addresses.com', 'ideography.co.uk', 'adsarobot', 'ah-ha',
      'aktuelles', 'alexibot', 'almaden', 'amzn_assoc', 'anarchie', 'art-online',
      'aspseek', 'assort', 'asterias', 'attach', 'atomz', 'atspider', 'autoemailspider',
      'backweb', 'backdoorbot', 'bandit', 'batchftp', 'bdfetch', 'big.brother',
      'black.hole', 'blackwidow', 'blowfish', 'bmclient', 'boston project', 'botalot',
      'bravobrian', 'buddy', 'bullseye', 'bumblebee ', 'builtbottough', 'bunnyslippers',
      'capture', 'cegbfeieh', 'cherrypicker', 'cheesebot', 'chinaclaw', 'cicc', 'civa', 'clipping',
      'collage', 'collector', 'copyrightcheck', 'cosmos', 'crescent', 'custo', 'cyberalert', 'deweb',
      'diagem', 'digger', 'digimarc', 'diibot', 'directupdate', 'disco', 'dittospyder', 'download accelerator',
      'download demon', 'download wonder', 'downloader', 'drip', 'dsurf', 'dts agent', 'dts.agent', 'easydl',
      'ecatch', 'echo extense', 'efp@gmx.net', 'eirgrabber', 'elitesys', 'emailsiphon', 'emailwolf', 'envidiosos',
      'erocrawler', 'esirover', 'express webpictures', 'extrac', 'eyenetie', 'fastlwspider', 'favorg', 'favorites sweeper',
      'fezhead', 'filehound', 'filepack.superbr.org', 'flashget', 'flickbot', 'fluffy', 'frontpage', 'foobot',
      'galaxyBot', 'generic', 'getbot ', 'getleft', 'getright', 'getsmart', 'geturl', 'getweb', 'gigabaz', 'girafabot',
      'go-ahead-got-it', 'go!zilla', 'gornker', 'grabber', 'grabnet', 'grafula', 'green research', 'harvest',
      'havindex', 'hhjhj@yahoo', 'hloader', 'hmview', 'homepagesearch', 'htmlparser', 'hulud', 'http agent',
      'httpconnect', 'httpdown', 'http generic', 'httplib', 'httrack', 'humanlinks', 'ia_archiver', 'iaea', 'ibm_planetwide',
      'image stripper', 'image sucker', 'imagefetch', 'incywincy', 'indy', 'infonavirobot', 'informant', 'interget',
      'internet explore', 'infospiders',  'internet ninja', 'internetlinkagent', 'interneteseer.com', 'ipiumbot',
      'iria', 'irvine', 'jbh', 'jeeves', 'jennybot', 'jetcar', 'joc web spider', 'jpeg hunt', 'justview', 'kapere',
      'kdd explorer', 'kenjin.spider', 'keyword.density', 'kwebget', 'lachesis', 'larbin',  'laurion(dot)com', 'leechftp',
      'lexibot', 'lftp', 'libweb', 'links aromatized', 'linkscan', 'link*sleuth', 'linkwalker', 'libwww', 'lightningdownload',
      'likse', 'lwp','mac finder', 'mag-net', 'magnet', 'marcopolo', 'mass', 'mata.hari', 'mcspider', 'memoweb',
      'microsoft url control', 'microsoft.url', 'midown', 'miixpc', 'minibot', 'mirror', 'missigua', 'mister.pix',
      'mmmtocrawl', 'moget', 'mozilla/2', 'mozilla/3.mozilla/2.01', 'mozilla.*newt', 'multithreaddb', 'munky', 'msproxy',
      'nationaldirectory', 'naverrobot', 'navroad', 'nearsite', 'netants', 'netcarta', 'netcraft', 'netfactual', 'netmechanic',
      'netprospector', 'netresearchserver', 'netspider', 'net vampire', 'newt', 'netzip', 'nicerspro', 'npbot', 'octopus',
      'offline.explorer', 'offline explorer', 'offline navigator', 'opaL', 'openfind', 'opentextsitecrawler', 'orangebot',
      'packrat', 'papa foto', 'pagegrabber', 'pavuk', 'pbwf', 'pcbrowser', 'personapilot', 'pingalink', 'pockey',
      'program shareware', 'propowerbot/2.14', 'prowebwalker', 'proxy', 'psbot', 'psurf', 'puf', 'pushsite', 'pump', 'qrva',
      'quepasacreep', 'queryn.metasearch', 'realdownload', 'reaper', 'recorder', 'reget', 'replacer', 'repomonkey', 'rma',
      'robozilla', 'rover', 'rpt-httpclient', 'rsync', 'rush=', 'searchexpress', 'searchhippo', 'searchterms.it',
      'second street research', 'seeker', 'shai', 'sitecheck', 'sitemapper', 'sitesnagger', 'slysearch', 'smartdownload',
      'snagger', 'spacebison', 'spankbot', 'spanner', 'spegla', 'spiderbot', 'spiderengine', 'sqworm', 'ssearcher100',
      'star downloader', 'stripper', 'sucker', 'superbot', 'surfwalker', 'superhttp', 'surfbot', 'surveybot', 'suzuran',
      'sweeper', 'szukacz/1.4', 'tarspider', 'takeout', 'teleport', 'telesoft', 'templeton', 'the.intraformant', 'thenomad',
      'tighttwatbot', 'titan', 'tocrawl/urldispatcher','toolpak', 'traffixer', 'true_robot', 'turingos', 'turnitinbot',
      'tv33_mercator', 'uiowacrawler', 'urldispatcherlll', 'url_spider_pro', 'urly.warning ', 'utilmind', 'vacuum', 'vagabondo',
      'vayala', 'vci', 'visualcoders', 'visibilitygap', 'vobsub', 'voideye', 'vspider', 'w3mir', 'webauto', 'webbandit',
      'web.by.mail', 'webcapture', 'webcatcher', 'webclipping', 'webcollage', 'webcopier', 'webcopy', 'webcraft@bea',
      'web data extractor', 'webdav', 'webdevil', 'webdownloader', 'webdup', 'webenhancer', 'webfetch', 'webgo', 'webhook',
      'web.image.collector', 'web image collector', 'webinator', 'webleacher', 'webmasters', 'webmasterworldforumbot', 'webminer',
      'webmirror', 'webmole', 'webreaper', 'websauger', 'websaver', 'website.quester', 'website quester', 'websnake', 'websucker',
      'web sucker', 'webster', 'webreaper', 'webstripper', 'webvac', 'webwalk', 'webweasel', 'webzip', 'wget', 'widow', 'wisebot',
      'whizbang', 'whostalking', 'wonder', 'wumpus', 'wweb', 'www-collector-e', 'wwwoffle', 'wysigot', 'xaldon', 'xenu', 'xget',
      'x-tractor', 'zeus'];
      //------------------------------------------------
  		if( strtolower($USER_AGENT) != str_replace($ct_rules, '*', strtolower($USER_AGENT))) {
  			$this->Logs('Bots attack',$GET_IP,$USER_AGENT,$GET_REFERER );
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_BOTS)));
        }

        Func::redir();
        return;
  		}
      //------------------------------------------------
  	}

    //------------------------------------------------

    if(self::FCONF['PROTECTION_REQUEST_METHOD'] and !in_array(strtolower($GET_REQUEST_METHOD),['get','head','post','put','update','delete'])) {
      $this->Logs('Invalid request',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_REQUEST)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_DOS'] and ($USER_AGENT == '' or $USER_AGENT == '-')) {
      $this->Logs('Dos attack',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_DOS)));
      }

      Func::redir();
      return;
    }

    //------------------------------------------------

    if(self::FCONF['PROTECTION_UNION_SQL']) {
      $stop = 0;
      $CT = ['*/from/*', '*/insert/*', '+into+', '%20into%20', '*/into/*', ' into ', 'into', '*/limit/*',
      'not123exists*', '*/radminsuper/*', '*/select/*', '+select+', '%20select%20', ' select ',
      '+union+', '%20union%20', '*/union/*', ' union ', '*/update/*', '*/where/*'];
      $check  = str_replace($CT, '*', $GET_QUERY_STRING );
      !$GET_QUERY_STRING != $check ?: $stop++;
      !preg_match($REGEX_UNION, $GET_QUERY_STRING) ?: $stop++;
      !preg_match('/([OdWo5NIbpuU4V2iJT0n]{5}) /', rawurldecode($GET_QUERY_STRING)) ?: $stop++;
      !strstr(rawurldecode($GET_QUERY_STRING ) ,'*') ?: $stop++;
      if($stop > 0) {
        $this->Logs('Union attack',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_UNION)));
        }

        Func::redir();
        return;
      }
    }

    //------------------------------------------------

    if (self::FCONF['PROTECTION_CLICK_ATTACK'] and $GET_QUERY_STRING != str_replace(['/*', 'c2nyaxb0', '/*'], '*', $GET_QUERY_STRING)) {
      $this->Logs('Click attack',$GET_IP,$USER_AGENT,$GET_REFERER);
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_CLICK)));
      }

      Func::redir();
      return;
  	}

    //------------------------------------------------

    if (self::FCONF['PROTECTION_XSS_ATTACK']) {
  		$ct_rules = ['http\:\/\/', 'https\:\/\/', 'cmd=', '&cmd', 'exec', 'concat', './', '../',
      'http:', 'h%20ttp:', 'ht%20tp:', 'htt%20p:', 'http%20:', 'https:', 'h%20ttps:',
      'ht%20tps:', 'htt%20ps:', 'http%20s:', 'https%20:', 'ftp:', 'f%20tp:', 'ft%20p:',
      'ftp%20:', 'ftps:', 'f%20tps:', 'ft%20ps:', 'ftp%20s:', 'ftps%20:'];
  		if($GET_QUERY_STRING != str_replace($ct_rules, '*', $GET_QUERY_STRING )) {
  			$this->Logs('XSS attack',$GET_IP,$USER_AGENT,$GET_REFERER);
        if(IS_API) {
          die(json_encode(array('success' => 0, 'message' => self::MSG_PROTECTION_XSS)));
        }

        Func::redir();
        return;
  		}
  	}

    //------------------------------------------------

    !self::FCONF['PROTECTION_UNSET_GLOBALS'] ?: $this->unset_globals();

    //------------------------------------------------

  }

}

?>
