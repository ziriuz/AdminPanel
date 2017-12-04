<?php

class Controller {
	
	public $model;
	public $view;
	protected $registry;
	public function __construct($registry = null) {
            $this->view = new View();
            $this->registry = $registry;
	}	
	// действие (action), вызываемое по умолчанию
	function action_index()
	{
            // todo	
	}
	public function __get($key) {
            return $this->registry->get($key);
	}
	public function __set($key, $value) {
            $this->registry->set($key, $value);
	}       
}
