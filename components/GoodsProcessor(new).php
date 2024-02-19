<?php
namespace components;

class GoodsProcessor
{
    public function fsScandir($dir)
    {
        $list = scandir($dir);
        unset($list[0],$list[1]);
        return array_values($list);
    }
    public function fsCleardir($dir)
    {
        $list = self::fsScandir($dir);
        foreach ($list as $file)
        {
            if (is_dir($dir.$file))
            {
                self::fsCleardir($dir.$file.'/');
                rmdir($dir.$file);
            }
            else
            {
                unlink($dir.$file);
            }
        }
        return;
    }
    private function getOldGoods()
    {
        $answ = "SELECT 
        id          as id,
        good_id     as good_id,
        item_id     as item_id

        FROM good WHERE shop_id = ".GROUP_ID ." AND item_id != 0 GROUP BY good_id";
        $goods =  mysqlQuery($answ);
        if(mysqli_num_rows($goods) == 0) return false;
        return $goods;    
    }
    private function saveOldgoods($oldGoods)
    {
        $answ = "DELETE FROM deleted_good WHERE shop_id = ".GROUP_ID;
        $answ =  mysqlQuery($answ);

        $answ = "INSERT INTO deleted_good(id, good_id, item_id, shop_id) VALUES ";
        $data = [];
            foreach($oldGoods as $oldGood)
            {
                $data[] = "('null', '".$oldGood['good_id']."',".$oldGood['item_id']. ",". GROUP_ID.")";
            }
            $answ .= implode(',', $data);
            $answ =  mysqlQuery($answ);
        return;
    }
    private function deleteGoods($existIDs)
    {
        $answ = "DELETE FROM deleted_good WHERE shop_id = ".GROUP_ID ." AND good_id IN('".implode(',', $existIDs)."')";
        $answ =  mysqlQuery($answ);
        $answ = "SELECT 
                        item_id          as item_id

                 FROM deleted_good WHERE shop_id = ".GROUP_ID ." GROUP BY item_id";
            $goods =  mysqlQuery($answ);
            if(mysqli_num_rows($goods) == 0) return;
            $Router = new Router;
                while ($good = mysqli_fetch_array($goods))  
                {
                 $Router->deleteGood($good['item_id']);
                }
        return;
    }
    public function saveGoods($goods)
    {
        $oldGoods = self::getOldGoods();
            if($oldGoods)
            {
                self::saveOldgoods($oldGoods);
            }
        //print_r($oldGoods,false);
        /*$answ = "DELETE FROM good WHERE shop_id = ".GROUP_ID;
        $answ =  mysqlQuery($answ);*/
        $answ = "INSERT INTO good(id, good_id, available, url, price, old_price, categoryId, picture, store, pickup, name, vendor, loaded_image, shop_id) VALUES ";
        $data = [];    
        $existIDs = [];   
             $answSendedGoods = "SELECT 
                                        good_id          as good_id

                                        FROM good WHERE shop_id = ".GROUP_ID ." AND (item_id IS NOT NULL) GROUP BY item_id";
                            $sendedGoods =  mysqlQuery($answSendedGoods);
                            //echo $answ;
                            $oldIDs = [];
                            if(mysqli_num_rows($answSendedGoods) != 0)
                            {
                                while ($answSendedGood = mysqli_fetch_array($answSendedGoods))  
                                {
                                    $oldIDs[] = $answSendedGood['good_id'];
                                }
                            }

            foreach($goods as $good)
            {
                    if(in_array($good['id'], $oldIDs))
                    {
                        continue;
                    }

                $data[] = "('null', '".$good['id']."', ".$good['available'].", '".$good['url']."', ".$good['price'].", ". $good['old_price'].", ".$good['categoryId'].", '".$good['picture']."', ".$good['store'].", ".$good['pickup'].", '".$good['name']."', '".$good['vendor']."', 0, ".GROUP_ID. ".)";
                $$existIDs[] = "'".$good['id']."'";
            }
            $answ .= implode(',', $data);
            $answ =  mysqlQuery($answ);
            self::deleteGoods($existIDs);
         return;   
    }
    public function loadImages()
    {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/images/')) mkdir($_SERVER['DOCUMENT_ROOT'].'/images/', 0777);
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/images/shop_'.GROUP_ID)) mkdir($_SERVER['DOCUMENT_ROOT'].'/images/shop_'.GROUP_ID, 0777);
        self::fsCleardir($_SERVER['DOCUMENT_ROOT'].'/images/shop_'.GROUP_ID);
        $answ = "SELECT 
                        id          as id,
                        good_id     as good_id,
                        picture     as picture,
                        available   as available

                 FROM good WHERE shop_id = ".GROUP_ID ." AND available = 1 GROUP BY good_id";
        $goods =  mysqlQuery($answ);
            if(mysqli_num_rows($goods) == 0) return;
                $arrContextOptions = array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ),
                );
                while ($good = mysqli_fetch_array($goods))
                {
                    $filenameArr =  explode('.', basename($good['picture']));
                    $type = $filenameArr[count($filenameArr) - 1];
                    $response = file_get_contents($good['picture'], false, stream_context_create($arrContextOptions));
                    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/images/shop_'.GROUP_ID.'/imageGood_'.$good['id'].'.'.$type, $response);
                   // echo '<pre>';print_r($good,false);echo '</pre>';
                }
    
    }
}
