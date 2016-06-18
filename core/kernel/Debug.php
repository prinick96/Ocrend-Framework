<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

final class Debug {

  const HEAD = '<br /><div id="debug">';
  const FOOT = '</div>';

  final private function convert(int $size)  {
      $unit = array('bytes','kb','mb','gb','tb','pb');
      return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

  final private function showinfo($var) : string {
    $var = print_r($var, true);
    $var = str_replace('=>','<span class="b">=></span>',$var);
    $var = str_replace(' [', ' <br />&nbsp;[', $var);

    return $var;
  }

  final private function listVar(array $VAR, string $variable) {
    echo '<strong class="cab">',$variable,':</strong> <br />';
    echo '<ul>';
    foreach ($VAR as $key => $value) {
      if($_GET and $key == 'view') {
        echo '<li><strong class="cab">Controller:</strong> <span class="variable">', $variable ,'</span><span class="b">[\'</span>', $key ,'<span class="b">\']</span> = ', $this->showinfo($value) ,'</li>';
      } else if($key == '___TWIG_DEBUG___' or $key == '___QUERY_DEBUG___') {
        null;
      } else {
        echo '<li><span class="variable">', $variable ,'</span><span class="b">[\'</span>', $key ,'<span class="b">\']</span> = ', $this->showinfo($value) ,'</li>';
      }
    }
    echo '</ul>';
  }

  final public function __construct(int $startime) {

    $endtime = microtime();
    $endtime = explode(" ",$endtime);
    $endtime = $endtime[0] + $endtime[1];
    $memory = $this->convert(memory_get_usage());

    echo self::HEAD;

    echo '<b class="cab">Archivo:</b> "' , $_SERVER['PHP_SELF'], '"<br />';
    echo '<b class="cab">PHP:</b> ', phpversion(), '<br /><br />';

    if(isset($_SESSION)) {
      $this->listVar($_SESSION,'$_SESSION');
    } else {
      echo 'Sin variables <span class="variable">$_SESSION</span><br />';
    }

    if($_POST) {
      $this->listVar($_POST,'$_POST');
    } else {
      echo 'Sin variables <span class="variable">$_POST</span><br />';
    }

    if($_GET) {
      $this->listVar($_GET,'$_GET');
    } else {
      echo 'Sin variables <span class="variable">$_GET</span><br />';
    }

    if($_FILES) {
      $this->listVar($_FILES,'$_FILES');
    } else {
      echo 'Sin variables <span class="variable">$_FILES</span><br />';
    }

    if(isset($_SESSION['___QUERY_DEBUG___']) and sizeof($_SESSION['___QUERY_DEBUG___']) > 0) {
      echo '<br /><strong class="cab">QUERYS:</strong><br />';
      echo '<ul style="list-style:none;padding:0;">';
      foreach ($_SESSION['___QUERY_DEBUG___'] as $query) {
        echo '<li><ul><li><span class="variable">query: </span>',$query[0],'</li><li><span class="variable">memoria: </span>',$this->convert($query[1]),'</li></ul></li>';
      }
      echo '</ul>';
    }

    if(isset($_SESSION['___TWIG_DEBUG___']) and null != $_SESSION['___TWIG_DEBUG___']) {
      echo '<strong class="cab">TWIG TEMPLATE:</strong> ', $_SESSION['___TWIG_DEBUG___'], ' <br />';
    }

    echo '<br /><b class="cab">DB_HOST:</b> ', DB_HOST;
    echo '<br /><b class="cab">DB_NAME:</b> ', DB_NAME,'<br />';
    echo '<br /><b class="cab">Firewall:</b> ', FIREWALL ? 'True' : 'False';
    echo '<br /><b class="cab">Tiempo de ejecución total:</b> ',$endtime - $startime,' segundos ';
    echo '<br /><b class="cab">RAM consumida por cada usuario:</b> ',$memory;

  }

  final public function __destruct() {
    echo self::FOOT;
  }

}

?>
