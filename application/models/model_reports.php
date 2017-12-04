<?php

class ModelReports extends Model
{
    function getSales($ps,$pe){
        $where = ($ps!=NULL&&$pe!=NULL?"where order_date between '%s' and '%s' ":'');
        $where = sprintf($where,$this->db->escape($ps),$this->db->escape($pe));
        $lsStatment = //sprintf(
               "select ifnull(n.nmcl_name,o.nmcl_name) name, n.*, sum(qty) orders_num 
from 
 (select order_id, order_date,nmcl_id,nmcl_name, sum(quantity) qty 
    from order_details join orders o using (order_id) 
   where o.status between 2 and 5 
   group by order_id,order_date,nmcl_id,nmcl_name) o 
left join nomenclatures n using (nmcl_id) 
$where
group by nmcl_id, ifnull(n.nmcl_name,o.nmcl_name) 
order by orders_num desc"
                //, $this->db->escape($ctgId)//$this->formatStringValue($details['alt_code']),
        //);
;        
        $query = $this->db->query($lsStatment,$mode='object');
        if($query->num_rows<1){
            throw new NoDataFoundException("Empty result");
        }
        return $query->rows;
    }
    function getOrders($ps,$pe,$deliveryType,$paymentStatus){
        $where = ($ps!=NULL&&$pe!=NULL?"and order_date between '%s' and '%s' ":'');
        $where = sprintf($where,$this->db->escape($ps),$this->db->escape($pe));
        if ($deliveryType!==null){
        $delivFilter = '1=0';
        if(in_array('GE', $deliveryType)){$delivFilter .= " OR lower(deliv_type) like '%курьер%'";}
        if(in_array('SDEK', $deliveryType)){$delivFilter .= " OR lower(deliv_type) like '%сдэк%'";}
        if(in_array('POST', $deliveryType)){$delivFilter .= " OR lower(deliv_type) like '%почта%'";}
        if(in_array('ALL', $deliveryType)){$delivFilter .= " OR 1=1";}
        $where .= ' AND ('.$delivFilter.')';
        }
        if ($paymentStatus!==null){
        $payFilter = '1=0';
        if(in_array('Y', $paymentStatus)){$payFilter .= " OR ifnull(kn.payment_status,'NO')!='NO'";}
        if(in_array('N', $paymentStatus)){$payFilter .= " OR ifnull(kn.payment_status,'NO')='NO'";}
        $where .= ' AND ('.$payFilter.')';
        }
        $lsStatment = //sprintf(
"select o.order_id, o.order_date, o.sid as order_number, n.alt_code, od.nmcl_id, ifnull(n.nmcl_name,od.nmcl_name) as name,
       sum(od.quantity) as quantity, od.price, sum(od.amount) as order_amount,
       sum(case when ifnull(kn.payment_status,'NO')!='NO' or o.status = 5 then od.amount else null end)
       + ifnull(case min(od.item_id) when od_first.item_id then ifnull(kn.delivery_total,o.deliv_price) else 0 end ,0) payment_amount,
       gateway as payment_type, 
       case kn.payment_status 
       when 'DELIVERY' then 'Наложенный платеж'
       when 'CASH'     then 'Наличными'
       when 'PAYPAL'   then 'Paypal'
       when 'CARD'     then 'На карту'
       else null end payment_status, 
       o.status,
       case min(od.item_id) when od_first.item_id then ifnull(kn.delivery_total,o.deliv_price) else null end  delivery, 
       case min(od.item_id) when od_first.item_id then o.deliv_type else null end deliv_type
  from orders o
  join order_details od using (order_id)
  join (select order_id,min(item_id) item_id from order_details where deleted = 0 group by order_id) od_first using(order_id)
  left join nomenclatures n using (nmcl_id)
  left join kn_orders kn on kn.shopify_id = o.order_id
 where o.status between 2 and 5 and od.deleted=0 $where
 group by order_id, order_date, o.sid, n.alt_code, od.nmcl_id, ifnull(n.nmcl_name,od.nmcl_name),od.price,gateway, o.status,kn.payment_status,kn.delivery_total,o.deliv_price,o.deliv_type,od_first.item_id
 order by order_date desc,o.sid,o.order_id,min(od.item_id),n.alt_code"
                //, $this->db->escape($ctgId)//$this->formatStringValue($details['alt_code']),
        //);
;        
        $query = $this->db->query($lsStatment,$mode='object');
        if($query->num_rows<1){
            throw new NoDataFoundException("Empty result");
        }
        return $query->rows;
    }    
}
