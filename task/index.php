<?php
    global $cronTask;
    $cronTask = 'sync';
    require_once getenv('HOME').'/www/vkfeed.4mma.ru/components/Taskmanager.php';
    require_once getenv('HOME').'/www/vkfeed.4mma.ru/components/Router.php';
    require_once getenv('HOME').'/www/vkfeed.4mma.ru/config/config.php';
    require_once getenv('HOME').'/www/vkfeed.4mma.ru/config/mysqli_db_connect.php';
    $root = getenv('HOME').'/www/vkfeed.4mma.ru';
    define('CONSOOLE_ROOT', $root);
    use \components\Router           as Router;
    $Router = new Router;
  //  $Router->deleteAllTasks();//Удалить!!!   
    $currentTask  = $Router->getTask();
        if($currentTask)
        {
            //если с снхронихзация активна
            echo 'Синхронизация выполняется...';
            exit(0);
        }
     //if(defined('CONSOOLE_ROOT')) echo 'RootDefine = '.'CONSOOLE_ROOT'  ;    

    require_once getenv('HOME').'/www/vkfeed.4mma.ru/index.php';