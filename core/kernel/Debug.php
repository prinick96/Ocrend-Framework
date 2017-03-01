<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Debug {

  //------------------------------------------------

  const HEAD = '<style>
  #debug {
    margin-bottom: 18px;
    margin-left: auto;
    margin-right: auto;
    width: 95%;
    background: #f7f7f9;
    border: 1px solid #e1e1e8;
    padding: 8px;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border radius: 4px;
    display: block;
    font-size: 12.05px;
    white-space: pre-wrap;
    word-wrap: break-word;
    color: #333;
    font-family: \'Menlo\',\'Monaco\',\'Consolas\',\'Courier New\',\'monospace\';
  }
  .variable {
    font-weight: bold!important;
    color: #e06a60!important;
  }
  .cab {
    font-weight: bold!important;
    color: #3864c6!important;
  }
  .b {
    color: #9eb2a9!important;
  }
  </style>
  <br /><div id="debug">';
  const FOOT = '</div>';

  //------------------------------------------------

  /**
    * Lista un arreglo, mostrando la información que contiene
    *
    * @param array $VAR: Variable a desglosar
    * @param string $variable: Forma escrita de la variable que se está desglosando
    *
    * @return void
  */
  final private function listVar(array $VAR, string $variable) {
    echo '<strong class="cab">',$variable,':</strong> <br />';
    echo '<ul>';
    foreach ($VAR as $key => $value) {
      if($key == '___QUERY_DEBUG___') {
        null;
      } else {
        echo '<li><span class="variable">', $variable ,'</span><span class="b">[\'</span>', $key ,'<span class="b">\']</span>', d($value) ,'</li>';
      }
    }
    echo '</ul>';
  }

  //------------------------------------------------

  /**
    * Constructor, inicializa el modo Debug
    *
    * @param int $startime: Start-Time, tiempo de inicio de ejecución del código
    *
    * @return void
  */
  final public function __construct(int $startime) {

    global $router;

    # Nombre de templates
    $template_engine = ['PlatesPHP','Twig'];

    //------------------------------------------------
    # Fin de test de velocidad
    $endtime = explode(" ",microtime());
    $endtime = $endtime[0] + $endtime[1];
    $memory = Func::convert(memory_get_usage());
    //------------------------------------------------

    echo self::HEAD;

    //------------------------------------------------
    echo '<b class="cab">Archivo:</b> "' , $_SERVER['PHP_SELF'], '"<br />';
    echo '<b class="cab">PHP:</b> ', phpversion(), '<br />';
    echo '<b class="cab">FRAMEWORK:</b> ', VERSION , '<br />';
    echo '<b class="cab">Motor de Templates:</b> ', $template_engine[(int) USE_TWIG_TEMPLATE_ENGINE] , '<br />';
    echo '<strong class="cab">Controller: </strong> ', $router->getController() ,'<br />';
    //------------------------------------------------

    //------------------------------------------------
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
    //------------------------------------------------

    //------------------------------------------------
    if(isset($_SESSION['___QUERY_DEBUG___']) and sizeof($_SESSION['___QUERY_DEBUG___']) > 0) {
      echo '<br /><strong class="cab">QUERYS:</strong><br />';
      echo '<ul style="list-style:none;padding:0;">';
      foreach ($_SESSION['___QUERY_DEBUG___'] as $query) {
        echo '<li><ul><li><span class="variable">query: </span>',$query,'</li></ul></li>';
      }
      echo '</ul>';
    }
    //------------------------------------------------

    //------------------------------------------------
    echo '<br /><b class="cab">DB_HOST:</b> ', DATABASE['host'];
    echo '<br /><b class="cab">DB_NAME:</b> ', DATABASE['name'],'<br />';
    echo '<br /><b class="cab">Firewall:</b> ', FIREWALL ? 'True' : 'False';
    echo '<br /><b class="cab">Tiempo de ejecución total:</b> ',$endtime - $startime,' segundos ';
    echo '<br /><b class="cab">RAM consumida por cada usuario:</b> ', $memory;
    //------------------------------------------------

  }

  //------------------------------------------------

  /**
    * Finaliza el modo Debug
    *
    * @return void
  */
  final public function __destruct() {
    echo self::FOOT;
  }

}

?>
