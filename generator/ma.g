<?php

final class {{model}} extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  final public function Foo(array $data) : array {
    # ...
    return array('success' => 0, 'message' => 'funcionando');
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
