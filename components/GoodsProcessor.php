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
                unlink($dir.'/'.$file);
            }
        }
        return;
    }
    
    private function getOldGoods()
    {
        $Loger =  new Loger;
        $Loger->setLog('[info] Получение старых товаров...');
        $answ = "SELECT 
                        id          as id,
                        good_id     as good_id,
                        item_id     as item_id

                        FROM good WHERE shop_id = ".GROUP_ID ."  GROUP BY good_id";
        $goods =  mysqlQuery($answ);
        if(mysqli_num_rows($goods) == 0) return false;
        return $goods;    
    }
    public function saveOldgoods()
    {
        $Loger =  new Loger;
        $Loger->setLog('[info] сохранение старых товаров...');
        if(defined('CONSOOLE_ROOT')) 
        {
            $tmpDir = CONSOOLE_ROOT.'/temp';
        }else{
            $tmpDir = $_SERVER['DOCUMENT_ROOT'].'/temp';
        }
        $shopsDir = $tmpDir.'/shops';
        $shopDir  = $shopsDir.'/shop_'.GROUP_ID;
        if(!is_dir($tmpDir))    mkdir($tmpDir, 0755);
        if(!is_dir($shopsDir))  mkdir($shopsDir, 0755);
        if(!is_dir($shopDir))   mkdir($shopDir, 0755);
        $file = $shopDir.'/goods.txt';
        if(file_exists($file)) unlink($file);
        $goods = self::getOldGoods();
        if(!$goods) return;
        $goodIDs = [];
                while ($good = mysqli_fetch_array($goods))  
                {
                 $goodIDs[] = $good['good_id'];
                }
        file_put_contents($file, json_encode($goodIDs));        
        return;
    }
    private function updateGoods($goodsForUpdate)
    {
        
            foreach ($goodsForUpdate as $good)
            {
                $answ =  "UPDATE good SET ";
                $answ .= "available = ".$good['available'].", ";
                $answ .= "url = '".$good['url']."', ";
                $answ .= "price = ".$good['price'].", ";
                $answ .= "old_price = ".$good['old_price'].", ";
                $answ .= "categoryId = ".$good['categoryId'].", ";
                $answ .= "picture = '".$good['picture']."', ";
                $answ .= "store = ".$good['store'].", ";
                $answ .= "pickup = ".$good['pickup'].", ";
                $answ .= "name = '".$good['name']."', ";
                $answ .= "vendor = '".$good['vendor']."', ";
                $answ .= "color = '".$good['color']."', ";
                $answ .= "size = '".$good['size']."', ";
                $answ .= "need_update = 1, ";
                $answ .= "error = 0";
                $answ .= " WHERE(good_id = '".$good['id']."' AND shop_id = ".GROUP_ID." )";
            }
            $answ =  mysqlQuery($answ);
        return;
    }
    private function deleteGoods($goodIDsForDelete)
    {
        $goodIDsForDelete = implode(',', $goodIDsForDelete);
        $answ  = "UPDATE good SET need_delete = 1 WHERE good_id IN (".$goodIDsForDelete.") AND shop_id = ".GROUP_ID;
        $answ =  mysqlQuery($answ);
        return;
    }
    private function loadOldGoods()
    {
        $Loger =  new Loger;
        $Loger->setLog('[info] загрузка старых товаров из файла...');
        if(defined('CONSOOLE_ROOT')) 
        {
            $tmpDir = CONSOOLE_ROOT.'/temp';
        }else{
            $tmpDir = $_SERVER['DOCUMENT_ROOT'].'/temp';
        }
        $shopsDir = $tmpDir.'/shops';
        $shopDir  = $shopsDir.'/shop_'.GROUP_ID;
        $file = $shopDir.'/goods.txt';
            if(!file_exists($file))
            {
                $Loger->setLog('[warning] старые товары не найдены...');
                return false;
            }
        $goods = json_decode(file_get_contents($file));
        if(!is_array($goods)) return false;
        return $goods;
    }   
    public function saveGoods($goods)
    {
        $Loger =  new Loger;
        $oldGoods = self::loadOldGoods();
        $answ = "INSERT INTO good(id, good_id, available, url, price, old_price, categoryId, picture, store, pickup, name, vendor, color, size, need_update, need_delete, shop_id, error) VALUES ";
        $data = [];
        //print_r($goods,false);    
        $newGoodsIDs = [];
        $goodsForUpdate = [];
        $goodIDsForDelete = [];
                foreach($goods as $good)
                {
                        $insertGood = true;
                        $newGoodsIDs[] = $good['id'];
                            if($oldGoods)
                            {
                                if(in_array(trim($good['id']), $oldGoods))
                                {
                                    $goodsForUpdate[] = $good;
                                    $insertGood = false;
                                }
                            }      
                            if($insertGood)
                            {
                                $data[] = "('null', '".$good['id']."', ".$good['available'].", '".$good['url']."', ".$good['price'].", ". $good['old_price'].", ".$good['categoryId'].", '".$good['picture']."', ".$good['store'].", ".$good['pickup'].", '".$good['name']."', '".$good['vendor']."', '".$good['color']."', '".$good['size']."', 0, 0,".GROUP_ID. ", 0)";
                            }
                }     
                //print_r($data,false);
                if(count($goodsForUpdate) > 0) self::updateGoods($goodsForUpdate);
                //Поиск товаров для удаления
                    if($oldGoods)
                    {
                        foreach($oldGoods as $oldGood)
                        {
                            if(!in_array($oldGood, $newGoodsIDs))
                            {
                                $goodIDsForDelete[] = "'".$oldGood."'";
                            }
                        }
                    }
                    if(count($goodIDsForDelete))
                    {
                        $Loger->setLog('[info] Удаляю товары...');
                        self::deleteGoods($goodIDsForDelete);
                    } 
            if(count($data) == 0) return;
            $answ .= implode(',', $data);
            $answ =  mysqlQuery($answ);
         return;   
    }
    public function loadImages()
    {
        $Loger =  new Loger;
        $Loger->setLog('[info] Загрузка изображений из фида...');
        
        if(defined('CONSOOLE_ROOT')) 
        {
            $Root = CONSOOLE_ROOT;
        }else{
            $Root = $_SERVER['DOCUMENT_ROOT'];
        }
        if(!file_exists($Root.'/images/')) mkdir($Root.'/images/', 0777);
        if(!file_exists($Root.'/images/shop_'.GROUP_ID)) mkdir($Root.'/images/shop_'.GROUP_ID, 0777);
        self::fsCleardir($Root.'/images/shop_'.GROUP_ID);
        $answ = "SELECT 
                        id          as id,
                        good_id     as good_id,
                        picture     as picture,
                        available   as available

                 FROM good WHERE shop_id = ".GROUP_ID ." AND available = 1 AND(item_id IS NULL OR need_update = 1) GROUP BY good_id";
        $goods =  mysqlQuery($answ);
            if(mysqli_num_rows($goods) == 0) return;
                $arrContextOptions = array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ),
                );
                $cnt = 0;
                $allCnt = 0;
                while ($good = mysqli_fetch_array($goods))
                {
                    //echo $good['id'].'; ';
                        if(strpos($good['picture'], '.pn') === false && strpos($good['picture'], '.jp') === false )
                        {
                            continue;
                        }
                    $response = file_get_contents($good['picture'], false, stream_context_create($arrContextOptions));
                    $filenameArr =  explode('.', basename($good['picture']));
                    $type = $filenameArr[count($filenameArr) - 1];
                    $filename = $Root.'/images/shop_'.GROUP_ID.'/imageGood_'.$good['good_id'].'.'.$type;
                    file_put_contents($filename, $response);
                    $cnt++;
                        if($cnt == 100)
                        {
                            $cnt = 0;
                            sleep(IMAGEDOWNLOAD_TIMEOUT);
                        }
                    $allCnt++;
                }
                $Loger->setLog('[info] Загрузил '.$allCnt .' изображений');
    }
    
}
