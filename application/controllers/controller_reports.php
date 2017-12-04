<?php
class Controller_Reports extends Controller
{
    function __construct($registry = null) {
        parent::__construct($registry);
        $this->view = new View();
    }
    function initData(){
        return array('exception'=>null,'message'=>null);
    }

    function action_index() {
        global $_SESSION;
        $this->load->model('reports');
        $data = $this->initData();
        $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
        $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);        
        $data['REPTITLE']='Продажи';
        $data['report']='SALES';
        $data['show_foto'] = true;        
        $data['start_date'] = $startDate==NULL?date('Y-').(date('m')-1).'-01':$startDate;
        $data['end_date'] = $endDate==NULL?date('Y-m-01'):$endDate;
        $data['g_status'] = array(null,null,'checked','checked','checked','checked',null);
        try{      
           $data['REPORTROWS'] = $this->model_reports->getSales($data['start_date'],$data['end_date']);
        } catch (Exception $ex){
           $data['exception']=true;
           $data['message']=$ex->getMessage();
        }
        $columns=array(
            'nmcl_id' => array('label' => 'ID', 'type' => 'link' , 'href' => 'index.php?fitem_id=#VALUE#'),
            'alt_code' => array('label' => 'Артикул', 'type' => 'value'),
            'name' => array('label' => 'Наименование', 'type' => 'value'),
            'foto_name' => array('label' => 'Фото', 'type' => 'image'),
            'orders_num' => array('label' => 'Количество заказов', 'type' => 'value')
        );
        $data['columns'] = &$columns;
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('sales_report.php', 'template_view.php', $data);
    }
    function action_orders() {
        global $_SESSION;
        $this->load->model('reports');
        $data = $this->initData();
        $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
        $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
        $deliveryType = filter_input(INPUT_POST, 'delivery_type', FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
        $paymentStatus = filter_input(INPUT_POST, 'payment_status', FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
        $data['REPTITLE']='Заказы';
        $data['report']='ORDERS';
        $data['start_date'] = $startDate==NULL?date('Y-').(date('m')-1).'-01':$startDate;
        $data['end_date'] = $endDate==NULL?date('Y-m-01'):$endDate;
        $data['delivery_type']=array('GE'=>null,'SDEK'=>null,'POST'=>null,'ALL'=>null);
        if($deliveryType!==null)foreach($deliveryType as $i=>$key){$data['delivery_type'][$key]='checked';}
        $data['payment_status']=array('Y'=>null,'N'=>null);
        if($paymentStatus!==null)foreach($paymentStatus as $i=>$key){$data['payment_status'][$key]='checked';}
        
        $data['g_status'] = array(null,null,'checked','checked','checked','checked',null);
        try{      
           $data['REPORTROWS'] = $this->model_reports->getOrders($data['start_date'],$data['end_date'],$deliveryType,$paymentStatus);
        } catch (Exception $ex){
           $data['exception']=true;
           $data['message']=$ex->getMessage();
        }
        $columns=array(
            'order_date' => array('label' => 'Дата заказа', 'type' => 'value' ),
            'order_number' => array('label' => '№ заказа', 'type' => 'value'),
            'alt_code' => array('label' => 'Артикул', 'type' => 'value'),
            'name' => array('label' => 'Наименование', 'type' => 'value'),
            'price' => array('label' => 'Цена', 'type' => 'value'),
            'order_amount' => array('label' => 'Сумма', 'type' => 'value'),
            'delivery' => array('label' => 'Доставка', 'type' => 'value'),
            'deliv_type' => array('label' => 'Способ доставка', 'type' => 'value'),
            'payment_type' => array('label' => 'Оплата, заказ', 'type' => 'value'),
            'payment_status' => array('label' => 'Оплата, способ', 'type' => 'value'),
            'payment_amount' => array('label' => 'Оплата, получение', 'type' => 'value')
        );
        $data['columns'] = &$columns;
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('sales_report.php', 'template_view.php', $data);
    }    
}