<?
    namespace datasource;
    use \components\Router as Router;
    use \components\GoodsProcessor as GoodsProcessor;
    use \templates\GoodTemplate    as GoodTemplate;
    use \components\Loger          as Loger;

    class Goods extends GoodTemplate    
    {
        private function loadCoverTest($good, $Router)
        {
            $imgUrl = $_SERVER['DOCUMENT_ROOT'].'/images/170552571965a841d7653905.73215178.pr_23222_1.png';
            $mainPhotoID = $Router->sendImg($imgUrl);
            return $mainPhotoID;
        }
        private function loadCover($good, $Router, $Loger)
        {
            $filenameArr =  explode('.', basename($good['picture']));
            $type = $filenameArr[count($filenameArr) - 1];
            if(defined('CONSOOLE_ROOT')) 
            {
                $Root  = CONSOOLE_ROOT;
            }else{
                $Root = $_SERVER['DOCUMENT_ROOT'];
            }    
            $fileneme = $Root.'/images/shop_'.GROUP_ID.'/imageGood_'.$good['good_id'].'.'.$type;
            if(!file_exists($fileneme))
            {
                $Loger->setLog('[error] Изображение '.$fileneme.' не найдено');
                return false;
            }
            $mainPhotoID = $Router->sendImg($fileneme);
            return $mainPhotoID;
        }
         public function getGoods($action, $lastID) 
        {

            $Router = new Router;
            $Loger =  new Loger;

            $answ = "SELECT 
                        id          as id,
                        good_id     as good_id,
                        available   as available,
                        picture     as picture,
                        url         as url,
                        price       as price,
                        old_price   as old_price,
                        categoryId  as categoryId,
                        store       as store,
                        pickup      as pickup,
                        name        as name,
                        vendor      as vendor,
                        size        as size,
                        color       as color,
                        item_id     as item_id,
                        error       as error
                ";
                if($action == 'CREATE_GOODS')
                {
                    $answ .=  " FROM good WHERE shop_id = ".GROUP_ID ." AND available = 1 AND item_id IS NULL"; 
                }else if($action == 'UPDATE_GOODS'){
                    $answ .=  " FROM good WHERE need_update = 1 AND shop_id = ".GROUP_ID ." AND item_id IS NOT NULL"; 
                }else{
                    $answ .=  " FROM good WHERE need_delete = 1 AND shop_id  = ".GROUP_ID ." AND item_id IS NOT NULL"; 
                }
            $answ .= " AND error = 0 ";
            $answ .= " AND id >  ".$lastID;
            $answ .= " GROUP BY good_id ";
            $answ .= " ORDER BY id ASC ";
            $answ .=  " LIMIT 1";    
            //$answ .= " LIMIT ".GOODS_STEP;        
            //$answ .= " OFFSET  ".$offset;
            $Loger->setDebug($answ);
            //echo $answ;     
            $goodsFeed =  mysqlQuery($answ);
            if(mysqli_num_rows($goodsFeed) == 0) 
            {
                return false;
            }
                if($action ==  'DELETE_GOODS')
                {
                    $result = [];   
                    $goodsItemids = [];
                        while ($good = mysqli_fetch_array($goodsFeed))
                        {
                            $result['last_id'] = $good['id'];
                            $goodsItemids[] = $good['item_id'];
                        }
                        $Loger->setDebug('Товары для удаления:'.PHP_EOL);
                        //$Loger->setDebug(print_r($goodsItemids, true));
                        //$Loger->setDebug(json_encode($goodsItemids));
                        $result['goods'] = $goodsItemids; 
                    return $result;
                }
            $errors = [];
            $goods = [];
            $i = 0;
            $result = [];   
                while ($good = mysqli_fetch_array($goodsFeed))
                {
                    $result['last_id'] = $good['id'];
                    $i++;
                    $mainPhotoID  = self::loadCover($good, $Router, $Loger);
                        if($mainPhotoID == false)
                        {
                            $Loger->setLog('[error] Изображение товара '.$good['good_id'].' не найдено');
                            $answ = "UPDATE good SET error = 1 WHERE (good_id = '".$good['good_id']. "' AND shop_id = ".GROUP_ID.")";
                            mysqlQuery($answ);
                            $errors[] = $i;
                            continue;
                        }   
                    $goodTmp  = 'owner_id='.OWNER_ID;    
                    $goodTmp .= '&name='.urlencode($good['name']);
                    $goodTmp .= '&description='.urlencode(self::getDescription($good));
                    $goodTmp .= '&category_id='.$good['categoryId'];
                    $goodTmp .= '&price='.$good['price'];
                    $goodTmp .= '&url='.$good['url'];
                        if($good['old_price'] != 0)
                        {
                            $goodTmp .= '&old_price='.$good['old_price'];
                        }
                   $goodTmp .= '&main_photo_id='.$mainPhotoID;
                   $goodTmp .= '&item_id='.$good['item_id'];
                   $goodTmp .= '&id='.$good['id'];
                   $goods[] =  $goodTmp;
                        if($action == 'UPDATE_GOODS')
                        {
                            $answ = "UPDATE good SET need_update = 0  WHERE id = ".$good['id'];
                            $answ =  mysqlQuery($answ);
                        }
                }   
                $result['goods'] = $goods;
                //print_r($errors,false);
                sleep(TRANSACTION_TIMEOUT);
          return $result;
        }
        public function getGoodsTest()
        {
            $errors = [];
            $Router = new Router;
            $goods = [];
                for($i = 1; $i < 3; $i++)
                {
                    $mainPhotoID  = self::loadCoverTest($good, $Router);
                        if($mainPhotoID == false)
                        {
                            $errors[] = $i;
                            continue;
                        }   
                    $price = 7 * $i;    
                    $oldPrice = 100 + $price;
                    $goodTmp  = 'owner_id='.OWNER_ID;
                    $goodTmp .= '&name='.urlencode('good-'.$i); 
                    $goodTmp .= self::getDescription($good);//'&description='.urlencode('description good for test vk4mma feed-'.$i);
                    $goodTmp .= '&category_id=2';
                    $goodTmp .= '&price='.$price;
                    $goodTmp .= '&old_price='.$oldPrice;
                    $goodTmp .= '&main_photo_id='.$mainPhotoID;
                    $goods[] =  $goodTmp;
                    /*$goods[$i] = [];
                    $goods[$i][] = 'owner_id='.OWNER_ID; 
                    $goods[$i][] = 'name='.urlencode('good-'.$i); 
                    $goods[$i][] = 'description='.urlencode('descr-'.$i);
                    $goods[$i][] = 'category_id=2';
                    $goods[$i][] = 'price='.(7 * $i);
                    $goods[$i][] = 'access_token='.ACCESS_TOKEN;*/
                }
        return $goods;        
        }
    }
?>