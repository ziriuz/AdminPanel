<?php

class Controller_wborder extends Controller
{

	function __construct()
	{
		$this->model = new Model_WbOrder();
		$this->view = new View();
	}
	
	function action_index()
	{
            $articul = (isset($_GET["articul"])?$_GET["articul"]:null);
            $sizeId = (isset($_GET["sizeId"])?$_GET["sizeId"]:null);
            $size = (isset($_GET["size"])?$_GET["size"]:null);
            $qty = (isset($_GET["qty"])?$_GET["qty"]:null);
            $data = $this->model->get_data($articul,$sizeId,$size,$qty);
            $this->view->generate('wborder_view.php', 'empty_view.php', $data);
	}
}