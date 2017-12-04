<?php
class Measoft
{
    /**
     * Логин
     */
    public $login;
    /**
     * Пароль
     */
    public $password;
    /**
     * Код клиента
     */
    public $extra;
    /**
     * Проверка заказа
     */
    public $errors = array();

    /**
     * Конструктор
     */
    public function __construct($login = null, $password = null, $extra = null)
    {
        if ($login && $password && $extra) {
            $this->login = $login;
            $this->password = $password;
            $this->extra = $extra;
        } else {
            die($login.', '.$password.', '.$code.'Проверьте аутентификационные данные');
        }
    }

    /**
     * Проверка заказа
     */
    public function orderValidate($order = null, $items = null)
    {
        if (!isset($order['phone'])) {
            $this->errors[] = 'Не заполнен телефон получателя';
        }
        if (!isset($order['town']) || !$order['town']) {
            $this->errors[] = 'Не заполнен город получателя';
        }
        if (!isset($order['address']) || !$order['address']) {
            $this->errors[] = 'Не заполнен адрес получателя';
        }
        if (!CheckDateTime($order['date'].' 00:00:00', 'DD.MM.YYYY HH:MI:SS')) {
            $this->errors[] = 'Неверный формат даты доставки';
        }
        if (!CheckDateTime(date('d.m.Y ').$order['time_min'].':00', 'DD.MM.YYYY HH:MI:SS')) {
            $this->errors[] = $order['time_min'].'Неверный формат минимального времени доставки';
        }
        if (!CheckDateTime(date('d.m.Y ').$order['time_max'].':00', 'DD.MM.YYYY HH:MI:SS')) {
            $this->errors[] = 'Неверный формат максимального времени доставки';
        }
        if ($order['time_min'] >= $order['time_max']) {
            $this->errors[] = 'Конечное время доставки должно быть больше начального';
        }

        if ($this->errors) {
            $this->errors = implode(';<br>', $this->errors);
            return false;
        }

        return true;
    }

    /**
     * Отправка запроса стоимости
     */
    public function calculatorRequest($order = null)
    {
        $errorsText = [
            0=>'OK',
            1=>'Неверный xml',
            2=>'Широта не указана',
            3=>'Долгота не указана',
            4=>'Дата и время запроса не указаны',
            5=>'Точность не указана',
            6=>'Идентификатор телефона не указан',
            7=>'Идентификатор телефона не найден',
            8=>'Неверная широта',
            9=>'Неверная долгота',
            10=>'Неверная точность',
            11=>'Заказы не найдены',
            12=>'Неверные дата и время запроса',
            13=>'Ошибка mysql',
            14=>'Неизвестная функция',

            15=>'Тариф не найден',
            18=>'Город отправления не указан',
            19=>'Город назначения не указан',
            20=>'Неверная масса',
            21=>'Город отправления не найден',
            22=>'Город назначения не найден',
            23=>'Масса не указана',
            24=>'Логин не указан',
            25=>'Ошибка авторизации',
            26=>'Логин уже существует',
            27=>'Клиент уже существует',
            28=>'Адрес не указан',
            29=>'Более не поддерживается',
            30=>'Настройка sip не выполнена',
            31=>'Телефон не указан',
            32=>'Телефон курьера не указан',
            33=>'Ошибка соединения',
            34=>'Неверный номер',
            35=>'Неверный номер',
            36=>'Ошибка определения тарифа',
            37=>'Ошибка определения тарифа',
            38=>'Тариф не найден',
            39=>'Тариф не найден',
        ];

        if (!$order) {
            $this->errors = 'Не указаны параметры заказа';
            return false;
        }

        $level = 0;
        $xml = $this->startXML();

        $xml .= $this->makeXMLNode('calculator', '', $level, '', 1);

        $level++;
        $xml .= $this->makeXMLNode('auth', '', $level, 'extra="'.$this->extra.'" login="'.$this->login.'" pass="'.$this->password.'"');
        $xml .= $this->makeXMLNode('calc', '', $level, 'townfrom="'.$order['townfrom'].'" townto="'.$order['townto'].'" mass="0.1" mode="1"');
        $level--;

        $xml .= $this->makeXMLNode('calculator', '', $level, '', 2);

        $result = simplexml_load_string($this->sendRequest($xml));

        if (!$result || !isset($result)) {
            $this->errors[] = 'Ошибка сервиса';
            return false;
        }

        if ($attributes = $result->attributes()) {
            if (isset($attributes['error']) && $attributes['error'] > 0) {
                $this->errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string) $response;
            }
        }

        if (!$this->errors) {
            if (isset($result->calc)) {
                if ($attributes = $result->calc->attributes()) {
                    if (isset($attributes['price'])) {
                        return $attributes['price'];
                    }
                }
            } else {
                $this->errors[] = 'Ошибка передачи данных';
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Отправка заказа
     */
    public function orderRequest($order = null, $items = null)
    {
        if (!$order || !$items) {
            $this->errors = 'Пустой массив заказа';
            return false;
        }

        $response = simplexml_load_string($this->sendRequest($this->createXML($order, $items)));
        if ($this->getRequestErrors($response)) {
            if (isset($response->createorder[0]['orderno'])) {
                return (string) $response->createorder[0]['orderno'];
            }
        } else {
            return false;
        }
    }

    /**
     * Статус заказа
     */
    public function statusRequest($orderNumber = null)
    {
        $statuses = [
            'NEW'=>'Новый',
            'ACCEPTED'=>'Получен складом',
            'DELIVERY'=>'Доставляется',
            'COURIERRETURN'=>'Возвращено курьером',
            'COMPLETE'=>'Доставлен',
            'CANCELED'=>'Не доставлен',
            'PARTIALLY'=>'Доставлен частично'
        ];

        if (!$orderNumber) {
            $this->errors = 'Не указан номер заказа';
            return false;
        }

        $level = 0;
        $xml = $this->startxml();
        $xml .= $this->makexmlnode('statusreq', '', $level, '', 1);

        $level++;
        $xml .= $this->makeXMLNode('auth', '', $level, 'extra="'.$this->extra.'" login="'.$this->login.'" pass="'.$this->password.'"');
        $xml .= $this->makexmlnode('orderno', $orderNumber, $level, '');
        $level--;

        $xml .= $this->makexmlnode('statusreq', '', $level, '', 2);

        $response = simplexml_load_string($this->sendRequest($xml));
        if ($this->getRequestErrors($response)) {
            $status = trim((string) $response->order[0]->status);
            if (isset($statuses[$status])) {
                return $statuses[$status];
            }
        } else {
            return false;
        }
    }

    /**
     * Выполнение POST запроса
     */
    public function sendRequest($content)
    {
        $opts = array(
            'http'=>array(
                'method'  => 'POST',
                'header'  => 'Content-type: text/xml',
                'charset' => 'utf-8',
                'content' => $content
            )
        );

        $context = stream_context_create($opts);
       // if (!$contents = @file_get_contents('https://home.courierexe.ru/api/', false, $context)) {
            if (!$curl = curl_init()) {
                $this->errors = 'Возможно не поддерживается передача по HTTPS. Проверьте наличие open_ssl';
                return false;
            }
            curl_setopt($curl, CURLOPT_URL, 'https://home.courierexe.ru/api/');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $contents = curl_exec($curl);
            curl_close($curl);
        //}
echo $contents;
        if (!$contents) {
            $this->errors = 'Ошибка сервиса';
            return false;
        }
        return $contents;
    }

    /**
     * Проверяем ошибки возвращаемые АПИ
     */
    public function getRequestErrors($response)
    {
        $errorsText = [
            'Ошибок нет',
            'Ошибка авторизации',
            'Отправлен пустой запрос',
            'Некорректно указана сумма заказа',
            'Некорректный общий вес заказа',
            'Не найден город получатель',
            'Не найден город отправитель',
            'Не заполнен адрес получателя',
            'Не заполнен телефон получателя',
            'Не заполнено контактное имя получателя',
            'Не заполнено название компании получателя',
            'Некорректная сумма объявленной ценности',
            'Артикул не найден',
            'Не заполнено название компании отправителя',
            'Не заполнено контактное имя отправителя',
            'Не заполнен телефон отправителя',
            'Не заполнен адрес отправителя',
            'Заказ с таким номером уже существует'
        ];
        $this->errors = '';

        if (!$response || !isset($response)) {
            return false;
        }

        if ($attributes = $response->attributes()) {
            if (isset($attributes['count']) && $attributes['count'] == 0) {
                $this->errors[] = 'Заказ с таким номером не найден';
            }
            if (isset($attributes['error']) && $attributes['error'] > 0) {
                $this->errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string) $response;
            }
        }

        if (isset($response->createorder)) {
            foreach($response->createorder as $order) {
                if ($attributes = $order->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $this->errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : $attributes['errormsg'];
                    }
                }
            }
        }

        if (isset($response->error)) {
            foreach($response->error as $error) {
                if ($attributes = $error->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $this->errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : $attributes['errormsg'];
                    }
                } else {
                    $this->errors[] = 'Ошибка синтаксиса XML: '.(string) $error;
                }
            }
        }

        if ($this->errors) {
            $this->errors = implode(';<br>', $this->errors);
            return false;
        }

        return true;
    }

    /**
     * Подготавливаем данные для запроса
     */
    public function createXML($order, $items)
    {
        $level = 0;
        $result = $this->startXML();

        $result .= $this->makeXMLNode('neworder', '', $level, '', 1);

        $level++;
        $result .= $this->makeXMLNode('auth', '', $level, 'extra="'.$this->extra.'" login="'.$this->login.'" pass="'.$this->password.'"');
        $result .= $this->makeXMLNode('order', '', $level, 'orderno="'.$order['orderno'].'"', 1);

        $level++;
        $result .= $this->makeXMLNode('barcode', $order['barcode'], $level);

        $result .= $this->makeXMLNode('receiver', '', $level, '', 1);

        $level++;
        $result .= $this->makeXMLNode('company', $this->stripTagsHTML(isset($order['company']) ? $order['company'] : $order['person']), $level);
        $result .= $this->makeXMLNode('phone', $this->stripTagsHTML($order['phone']), $level);
        $result .= $this->makeXMLNode('town', $order['town'], $level);
        $result .= $this->makeXMLNode('address', $this->stripTagsHTML($order['address']), $level);
        $result .= $this->makeXMLNode('date', $order['date'], $level);
        $result .= $this->makeXMLNode('time_min', $order['time_min'], $level);
        $result .= $this->makeXMLNode('time_max', $order['time_max'], $level);
        $level--;

        $result .= $this->makeXMLNode('receiver', '', $level, '', 2);

        $result .= $this->makeXMLNode('weight', $order['weight'], $level);
        $result .= $this->makeXMLNode('quantity', $order['quantity'], $level);
        $result .= $this->makeXMLNode('paytype', 'CASH', $level);
        $result .= $this->makeXMLNode('service', '1', $level);
        $result .= $this->makeXMLNode('price', $order['price'], $level);
        $result .= $this->makeXMLNode('inshprice', $order['inshprice'], $level);
        $result .= $this->makeXMLNode('enclosure', $order['enclosure'], $level);
        $result .= $this->makeXMLNode('instruction', $order['instruction'], $level);

        //Наличие вложений
        if (isset($items) && $items) {
            $result .= $this->makeXMLNode('items', '', $level, '', 1);
            $level++;
            foreach ($items as $item) {
                $result .= $this->makeXMLNode('item', $this->stripTagsHTML($item['name']), $level, 'quantity="'.$item['quantity'].'" mass="'.$item['mass'].'" retprice="'.$item['retprice'].'"');
            }
            $level--;
            $result .= $this->makeXMLNode('items', '', $level, '', 2);
        }
        $level--;
        $result .= $this->makeXMLNode('order', '', $level, '', 2);

        $level--;
        $result .= $this->makeXMLNode('neworder', '', $level, '', 2);

        return $result;
    }

    public function startXML()
    {
        return ('<?xml version="1.0" encoding="UTF-8"?>');
    }

    public function stripTagsHTML($s)
    {
        $s = str_replace('&', '&amp;', $s);
        $s = str_replace("'", '&apos;', $s);
        $s = str_replace('<', '&lt;', $s);
        $s = str_replace('>', '&gt;', $s);
        $s = str_replace('"', '&quot;', $s);

        return $s;
    }

    public function makeXMLNode($nodename, $nodetext, $level = 0, $attr = '', $justopen = 0)
    {
        $result = "\r\n";
        for ($i = 0; $i < $level; $i++) $result .= '    ';

        $emptytag = ($nodetext === '') && ($justopen == 0);
        $nodetext = $this->stripTagsHTML($nodetext);

        if ($justopen < 2) $result .= '<'.$nodename.($attr ? $attr = ' '.$attr : '').($emptytag ? ' /' : '').'>'.$nodetext;
        if ((($justopen == 0) && !$emptytag) || ($justopen == 2)) $result .= '</'.$nodename.'>';

        return ($result);
    }
}

