<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class lostpassController extends Controllers {

	public function __construct() {
		parent::__construct(false,true);		
		switch ($this->method) {
			case 'cambiar':
				if($this->isset_id) {
					$l = new Lostpass;
					echo $this->template->render('lostpass/lostpass',array('pass' => $l->UpdatePass()));
				} else {
					Func::redir();
				}
			break;
			default:
				Func::redir();
			break;
		}
	}
}

?>
