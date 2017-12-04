<?php
class Label {

    public $label_code;
    public $alt_code;
    public $color_name;
    public $size_name;

    function __construct($label_code, $alt_code, $color_name, $size_name) {
        $this->label_code = $label_code;
        $this->alt_code = $alt_code;
        $this->color_name = $color_name;
        $this->size_name = $size_name;
    }

}

class Controller_Labels extends Controller
{
    function __construct($registry = null) {
        parent::__construct($registry);
        $this->model = new Model_Labels();
        $this->view = new View();
    }
    function initData(){
        return array('exception'=>null,'message'=>null);
    }

    function action_index() {
        global $_SESSION;
        $this->load->model('documents');
        //$data = $this->model->get_data($doc_id,false);
        $data = $this->initData();
        $doc_id = filter_input(INPUT_GET, 'doc_id', FILTER_SANITIZE_STRING);
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        $getToken = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
        $labelList = array();
        $PRINTLABELS = false;
        $alt_code = '';
        $data['draft'] = $doc_id==null; 
        try{
        switch ($token) {
            case 'create_document':
                $document=array();
                $document['doc_number'] = filter_input(INPUT_POST, 'document_doc_number', FILTER_SANITIZE_STRING);
                $document['doc_date'] = filter_input(INPUT_POST, 'document_doc_date', FILTER_SANITIZE_STRING);
                if(empty($document['doc_number'])){
                    throw new Exception("Не указан номер документа");
                }
                if(empty($document['doc_date'])){
                    $document['doc_date']= date ('Y-m-d');
                }
                $this->model_documents->createFromDraft($document);
                break;
            case 'clear_draft':
                $this->model_documents->clearDraft();
                $data['message']="Черновик очищен. При необходимости восстановить обратитесь к разработчику";
                break;
            default : break;
        }
        switch ($getToken) {
            case 'delete_document':
                $this->model_documents->deleteDocument($doc_id);
                $data['message']="Удален документ [id:$doc_id]";
                break;
            case 'restore_draft':
                $this->model_documents->restoreDraft();
                $data['message']="Черновик восстановлен";
                break;
            default : break;
        }
        switch ($action) {
            case 'ADD_ARTICUL':
                $quantity = filter_input(INPUT_POST, 'filter_quantity', FILTER_SANITIZE_STRING);
                $size = filter_input(INPUT_POST, 'filter_size', FILTER_SANITIZE_STRING);
                $alt_code = filter_input(INPUT_POST, 'filter_alt_code', FILTER_SANITIZE_STRING);
                $label = array(
                    "quantity" => $quantity,
                    "size" => $size,
                    "alt_code" => $alt_code
                );
                if (isset($_SESSION["label_list"])) {
                    $labelList = $_SESSION["label_list"];
                }
                $labelList[] = $label;
                $_SESSION["label_list"] = $labelList;
                $aLabels = $this->model->getLabels($alt_code, $size, $quantity);
                $labels = array();
                if (isset($_SESSION["barcodes"])) {
                    $labels = $_SESSION["barcodes"];
                }
                $labels = array_merge($labels, $aLabels);
                $_SESSION["barcodes"] = $labels;
                break;
            case 'PRINT_LABELS':
                $PRINTLABELS = true;
                $labels = $_SESSION["barcodes"];
                break;
            case null:
                unset($_SESSION["label_list"]);
                unset($_SESSION["barcodes"]);
                break;
            default : break;
        }
        }
        catch(Exception $ex){
            $data['exception']=true;
            $data['message']=$ex->getMessage();
        }        
        $data['active_doc'] = &$doc_id;
        $data['supplyDocList'] = $this->model_documents->getDocuments();
        $data['documentItems'] = ($doc_id == null ?
                $this->model_documents->getDraftItems() :
                $this->model_documents->getDocumentItems($doc_id)
                );
        $data['totalQty'] = &$this->model_documents->totalQty;
        $data['totalAmount'] = &$this->model_documents->totalAmount;
        $data['totalAmountMin'] = &$this->model_documents->totalAmountMin;
        $data['sizeList'] = array_merge(
                $this->model_documents->getCategories('SIZE'), 
                $this->model_documents->getCategories('SIZE_CH'),
                $this->model_documents->getCategories('SIZE_HEA')
        );
        $data['colorList'] = $this->model_documents->getCategories('COLOR');
        $data['DEF_ID'] = 0;
        $data['PRINTLABELS'] = $PRINTLABELS;
        $data['labelList'] = $labelList;
        $data['val_alt_code'] = $alt_code;
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('labels_view.php', 'template_view.php', $data);
    }

    function action_print() {
        global $_POST;
        $doc_id = (isset($_GET["doc_id"]) ? $_GET["doc_id"] : null);
        $flags = (isset($_POST["flag"]) ? $_POST["flag"] : null);
        $print_qty = (isset($_POST["print_qty"]) ? $_POST["print_qty"] : null);
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        $this->load->model('labels');
        $data = $this->initData();
        try{
            $data['labels'] = $this->model_labels->getPrintData($flags, $print_qty, $doc_id);            
        } catch (Exception $ex) {
            $data['exception']=true;
            $data['message']=$ex->getMessage();
        }
        if ($token != "label" && $token != "detail_label" && $token != "inner_label"){
            $data["template"] = "label";
        }
        else {
            $data["template"] = $token;
        }
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('print_labels_view.php', 'print_template_view.php', $data);
    }

    function action_printbox() {
        $doc = filter_input(INPUT_GET, 'doc', FILTER_SANITIZE_STRING);
        $qty = filter_input(INPUT_GET, 'qty', FILTER_VALIDATE_INT);
        $labels = array(new Label($doc . '01', '-', '-', '-'));
        for ($i = 1; $i <= $qty; $i++) {
            //$label = new ($label_code,$alt_code,$color_name,$size_name);
            $labels[] = new Label($doc . '010' . $i, '-', '-', '-');
        }
        $data = array("template" => 'label', 'labels' => $labels);
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('print_labels_view.php', 'print_template_view.php', $data);
    }

    function action_barcodes() {
        $doc_id = filter_input(INPUT_GET, 'doc_id', FILTER_SANITIZE_STRING);
        $this->load->model('documents');
        $data = $this->initData();
        $data['documentItems'] = ($doc_id == null ?
                $this->model_documents->getDraftItems() :
                $this->model_documents->getDocumentItems($doc_id)
                );
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('print_barcodes_view.php', 'print_template_view.php', $data);
    }

    function action_specification() {
        $doc_id = filter_input(INPUT_GET, 'doc_id', FILTER_SANITIZE_STRING);
        $this->load->model('documents');
        $data = $this->initData();
        $data['documentItems'] = ($doc_id == null ?
                $this->model_documents->getDraftItems() :
                $this->model_documents->getDocumentItems($doc_id)
                );
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('print_specification_view.php', 'print_template_view.php', $data);
    }

    function action_torg12() {
        $doc_id = filter_input(INPUT_GET, 'doc_id', FILTER_SANITIZE_STRING);
        $this->load->model('documents');
        $data = $this->initData();
        $data['documentItems'] = ($doc_id == null ?
                $this->model_documents->getDraftItems() :
                $this->model_documents->getDocumentItems($doc_id)
                );
        header('Content-type: text/xml; charset=windows-1251');
        $this->view->generate('xml_torg12_view.php', 'xml_template_view.php', $data);
    }

    function action_pivot() {
        $doc_id = filter_input(INPUT_GET, 'doc_id', FILTER_SANITIZE_STRING);
        $this->load->model('labels');
        $data = $this->initData();
        $data['documentItems'] = ($doc_id == null ?
                $this->model_labels->getDraftItemsPivot() :
                $this->model_labels->getDocumentItemsPivot($doc_id)
                );       
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('labels_pivot_view.php', 'print_template_view.php', $data);
    }

}
