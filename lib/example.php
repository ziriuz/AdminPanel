<?php
    include('Measoft.php');

    $order = array(
        'orderno'=>'12359',//Номер заказа
        'barcode'=>'1234567890',//Штрих-код
        'company'=>'Название компании',//Компания-получатель. Должно быть заполнено company ИЛИ person!
        'person'=>'Иванов Иван Иванович',//Контактное лицо. Должно быть заполнено company ИЛИ person!
        'phone'=>'89123456789',//Телефон. Можно указывать несколько телефонов
        'town'=>'Москва',//Город
        'address'=>'ул. Уральская, 1-2',//Адрес
        'date'=>'2015-01-01',//Дата доставки в формате "YYYY-MM-DD"
        'time_min'=>'12:00',//Желаемое время доставки в формате "HH:MM"
        'time_max'=>'20:00',//Желаемое время доставки в формате "HH:MM"
        'weight'=>5,//Общий вес заказа
        'quantity'=>1,//Количество мест
        'price'=>100,//Сумма заказа
        'inshprice'=>1000,//Объявленная стоимость
        'enclosure'=>'Это ТЕСТОВЫЙ заказ',//Наименование
        'instruction'=>'Комментарий',//Поручение
    );

    $items = array(
        array(
            'name'=>'Наименование',//Название товара
            'quantity'=>2,//Количество мест
            'mass'=>1,//Масса единицы товара
            'retprice'=>35,//Цена единицы товара
        )
    );

    //Создаем экзепляр класса Меасофт
    $measoft = new Measoft('login', 'pass', 8);

    //Пытаемся отправить заказ
    if ($orderNumber = $measoft->orderRequest($order, $items)) {        print 'Заказ '.$orderNumber.' успешно создан<br>';

        if ($status = $measoft->statusRequest($orderNumber)) {
            print 'Заказ '.$orderNumber.' сейчас: '.$status;
        } else {
            print 'При получении статуса произошли ошибки:<br>';
            print_r($measoft->errors);
        }
    } else {        print 'При отправке заказа произошли ошибки:<br>';
        print_r($measoft->errors);
    }



