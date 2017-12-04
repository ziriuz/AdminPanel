<?php

/*
 ласс-маршрутизатор дл¤ определени¤ запрашиваемой страницы.
> цепл¤ет классы контроллеров и моделей;
> создает экземпл¤ры контролеров страниц и вызывает действи¤ этих контроллеров.
*/
class Route
{

  static function start($registry)
  {
    // контроллер и действие по умолчанию
    $controller_name = 'Main';
    $action_name = 'index';
    $uri = explode('?', $_SERVER['REQUEST_URI']);
    $routes = explode('/', $uri[0]);
    $controller_index = CONTROLLER_INDEX;

    // получаем им¤ контроллера
    if ( !empty($routes[$controller_index]) )
    {  
      $controller_name = $routes[$controller_index];
    }
    
    // получаем им¤ экшена
    if ( !empty($routes[$controller_index+1]) )
    {
      $action_name = $routes[$controller_index+1];
    }
    global $TITLE;
    $TITLE=$controller_name.'='.$action_name;
    // добавл¤ем префиксы
    $model_name = 'Model_'.$controller_name;
    $controller_name = 'Controller_'.$controller_name;
    $action_name = 'action_'.$action_name;

    /*
    echo "Model: $model_name <br>";
    echo "Controller: $controller_name <br>";
    echo "Action: $action_name <br>";
    */

    // подцепл¤ем файл с классом модели (файла модели может и не быть)

    $model_file = strtolower($model_name).'.php';
    $model_path = "application/models/".$model_file;
    if(file_exists($model_path))
    {
      include "application/models/".$model_file;
    }
    // подцепл¤ем файл с классом контроллера
    $controller_file = strtolower($controller_name).'.php';
    $controller_path = "application/controllers/".$controller_file;
    if(file_exists($controller_path))
    {
      include "application/controllers/".$controller_file;
    }
    else
    {
      /*
      правильно было бы кинуть здесь исключение,
      но дл¤ упрощени¤ сразу сделаем редирект на страницу 404
      */
      throw new Exception("Controller not found: $controller_path");
      //Route::ErrorPage404();      
    }
    
    // создаем контроллер
    $controller = new $controller_name($registry);
    $action = $action_name;
    
    if(method_exists($controller, $action))
    {
      // вызываем действие контроллера
      session_start();
      if(!$USER=displayLogin()){ if(isset($sql)) $sql->close(); exit;}
      $controller->$action();
    }
    else
    {
      // здесь также разумнее было бы кинуть исключение
      throw new Exception("Method not found: $controller_name -> $action_name");
      //Route::ErrorPage404();
    }
  
  }
  static function ErrorPage404()
  {
    $host = $_SERVER['HTTP_HOST'].'/';
    //    header('HTTP/1.1 404 Not Found');
    //header("Status: 404 Not Found");
    header('Location: http://'.$host.'404');
    exit;
    }
    
}
