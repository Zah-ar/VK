    <?php
    require_once __DIR__.'/config/config.php';
    if(!defined('SOURCE'))
    {
        echo "Error! SOURCE no define. Define in '/config/config.php' ";
        die();
    } 
    if(!defined('ACCESS_TOKEN'))
    {
        echo "Error! ACCESS_TOKEN no define. Define in '/config/config.php' ";
        die();
    } 
    if(!defined('OWNER_ID'))
    {
        echo "Error! OWNER_ID no define. Define in '/config/config.php' ";
        die();
    } 
    if(!defined('GROUP_ID'))
    {
        echo "Error! GROUP_ID no define. Define in '/config/config.php' ";
        die();
    } 
    if(!defined('TIMEOUT') || TIMEOUT < 3)
    {
        echo "Error! TIMEOUT no define or TIMEOUT < 3. Minimal TIMEOUT is 3 sec. Define in '/config/config.php' ";
        die();
    } 

    date_default_timezone_set('Europe/Moscow');
    set_time_limit(0);
    require_once __DIR__.'/config/mysqli_db_connect.php';
    require_once __DIR__.'/components/Loger.php';
    require_once __DIR__.'/components/Taskmanager.php';  
    require_once __DIR__.'/components/Router.php';  
    require_once __DIR__.'/components/YmlParser.php';
    require_once __DIR__.'/components/GoodsProcessor.php';
    require_once __DIR__.'/templates/GoodTemplate.php';
    require_once __DIR__.'/datasource/Goods.php';

    use \components\Loger            as Loger;
    use \components\Router           as Router;
    use \components\YmlParser        as YmlParser;
    use \components\GoodsProcessor   as GoodsProcessor;
    use \templates\GoodTemplate      as GoodTemplate;
    use \datasource\Goods            as Goods;
    
    $disabled = '';
    $task = '';
    $dis_no = 'dis_no';
    $Loger =  new Loger;
    $Loger->setLog('[Info] Starting...');
    $Router = new Router;
    $currentTask  = $Router->getTask();
        if($currentTask)
        {
            $dis_no = '';
            $disabled = 'disabled';
        }           global $cronTask;

                    if($cronTask == 'sync')
                    {
                        $Router->setTask($cronTask);
                        GoodsProcessor::saveOldgoods($Loger);
                        $goods =  YmlParser::getGoodsFromYML(SOURCE);
                        GoodsProcessor::saveGoods($goods);
                        GoodsProcessor::loadImages();
                        $Loger->setLog('[Info] Goods loaded...');
                        
                        $goods =  Goods::getGoods('DELETE_GOODS', 0);
                            if($goods)
                            {
                                $Loger->setLog('[Info] Start goods delete...'); 
                                $Router->setTask('DELETE_GOODS');
                                    do{
                            
                                            $result = $Router->deleteGoods($goods['goods'], $Loger, 'DELETE_GOODS');
                                            $goods =  Goods::getGoods('DELETE_GOODS', $goods['last_id']);
                                            sleep(TIMEOUT);
                                        }while($goods);
                                $Router->deleteTask('DELETE_GOODS');      
                                $Loger->setLog('[Info] Goods deleted...');
                            }
                    
                        $goods =  Goods::getGoods('UPDATE_GOODS', 0);
                            if($goods)
                            {
                                $Router->setTask('UPDATE_GOODS');
                                    do{
                            
                                            $result = $Router->sendGoods($goods['goods'], $Loger, 'UPDATE_GOODS');
                                            $goods =  Goods::getGoods('UPDATE_GOODS', $goods['last_id']);
                                            sleep(TIMEOUT);
                                        }while($goods);
                                $Router->deleteTask('UPDATE_GOODS');
                                $Loger->setLog('[Info] Goods updated...');
                            }
                            
                         $goods =  Goods::getGoods('CREATE_GOODS', 0);
                            if($goods)
                            {
                                $Loger->setLog('[Info] Start goods create...');
                                $Router->setTask('CREATE_GOODS');
                                    do{
                              
                                            $result = $Router->sendGoods($goods['goods'], $Loger, 'CREATE_GOODS');
                                            $goods =  Goods::getGoods('CREATE_GOODS', $goods['last_id']);
                                            sleep(TIMEOUT);
                                        }while($goods);
                                  $Router->deleteTask('CREATE_GOODS');
                                
                                $Loger->setLog('[Info] Goods created...');
                            }
                            if(defined('CONSOOLE_ROOT')) 
                            {
                                $Root = CONSOOLE_ROOT;
                            }else{
                                $Root = $_SERVER['DOCUMENT_ROOT'];
                            }
                            GoodsProcessor::fsCleardir($Root.'/images/shop_'.GROUP_ID);
                            $Router->deleteAllTasks();   
                            echo 'ALL DONE!';   
                            $Loger->setLog('[Info] ALL DONE!');
                    }
        
    ?>
    