<?php

class Controller_Warehouse extends Controller
{

	function __construct()
	{
		$this->model = new Model_Warehouse();
		$this->view = new View();
	}
	
	function action_index()
	{
    $fwrh_id = (isset($_GET['fwrh_id'])?$_GET['fwrh_id']:null);
		$data = $this->model->get_data($fwrh_id);
		header('Content-type: text/html; charset=windows-1251');
		$this->view->generate('warehouse_view.php', 'template_view.php', $data);
	}
	function action_clear_rests()
	{
    global $_POST;
    $flags = (isset($_POST["flag"])?$_POST["flag"]:null);
    $this->model->clear_rests($flags);
    $this->action_index();
    echo $this->model->message;
  }  
}
