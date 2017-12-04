<?php

class Controller_Main extends Controller
{

	function action_index()
	{	
		$this->view->generate('main_view.php', 'template_view.php');
	}
        function action_orders()
	{
            $doc_id = (isset($_GET["doc_id"])?$_GET["doc_id"]:null);
            header('Content-type: text/html; charset=utf-8');
            $this->view->generate('kluvonos_view.php', 'kluvonos_template_view.php');         
	}
}