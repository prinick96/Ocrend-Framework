<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class {{model}} extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  # Control de errores
  final private function errores(array $data) {
    try {

      {{errores_php}}

      return false;
    } catch(Exception $e) {
      return array('success' => 0, 'message' => $e->getMessage());
    }
  }

  # Crear un elemento
  final public function crear(array $data) : array {
    $error = $this->errores($data);
    if(false !== $error) {
      return $error;
    }

    {{crear_php}}

    return array('success' => 1, 'message' => '<b>Creado</b> con éxito.');
  }

  # Editar un elemento
  final public function editar(array $data) : array {

    $this->id = $this->db->scape($data['id']);

    $error = $this->errores($data);
    if(false !== $error) {
      return $error;
    }

    {{editar_php}}

    return array('success' => 1, 'message' => '<b>Editado</b> con éxito.');
  }

  # Borrar un elemento
  final public function borrar() {
    $this->db->delete('{{table_name}}',"id='$this->id'");
    Func::redir(URL . '{{view}}/?success=true');
  }

  # Leer uno o todos los elementos
  final public function leer(bool $multi = true) {
    if($multi) {
      return $this->db->select('*','{{table_name}}');
    }

    return $this->db->select('*','{{table_name}}',"id='$this->id'",'LIMIT 1');
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
