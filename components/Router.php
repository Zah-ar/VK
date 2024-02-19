<?php

namespace components;
class Router extends Taskmanager
{
    private $marketUploadServer;

    public function __construct()
    {
        $url = VK_URL.'photos.getMarketUploadServer?access_token='.ACCESS_TOKEN.'&v=5.131&group_id='.GROUP_ID;
        $json_html = file_get_contents($url);
        $json = json_decode($json_html, true);
        //echo '<pre>';print_r($json);echo'</pre>';
        $this->marketUploadServer = $json['response']['upload_url'];
        return;
    }
    public function sendImg($imgUrl)
    {
            if(!file_exists($imgUrl))
            {
                echo 'File '.basename($imgUrl).' not found!';
                return false; 
            }
        $cFile = curl_file_create($imgUrl);
        $ch = curl_init($this->marketUploadServer); // создаем подключение
        $postData = [];
        $postData['file'] = $cFile;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        sleep(TIMEOUT);
        $json_html = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($json_html, true);
        $img = $this->saveImg($json['server'], $json['photo'], $json['hash']);
        sleep(TIMEOUT);
            if($img == false)
            {
                return false;
            }
        return $img['response'][0]['id'];
    }
    
    private function saveImg($sever, $photo, $hash)
    {
        $url = VK_URL.'photos.saveMarketPhoto';
        $ch = curl_init($url);
        $postData = [];
        $postData['server'] = $sever;
        $postData['photo']  = $photo;
        $postData['hash']   = $hash;
        $postData['v'] = '5.131';
        $postData['access_token'] = ACCESS_TOKEN;
        $postData['group_id'] = GROUP_ID;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json_html = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($json_html, true);
        sleep(TIMEOUT);
            if(array_key_exists('error', $json))
            {
                echo $json['error']['error_msg'];
                return false;
            }
        return $json;
    }
    public function sendGoods($goods, $Loger, $action)
    {
        $sumbarket = 'add';
            if($action == 'UPDATE_GOODS')
            {
                $sumbarket = 'edit';
            }
        $cnt = 0;   

        foreach ($goods as $good)
        {
            $url = VK_URL.'market.'.$sumbarket.'/?access_token='.ACCESS_TOKEN.'&v=5.131&owner_id='.OWNER_ID.'&'. $good; 
            //echo $url;
            $json_html = file_get_contents($url);
            $json = json_decode($json_html, true);
            //echo '<pre>'; print_r($json); echo '<pre>';
            $Loger->setLog($json);
                /*if(array_key_exists('error', $json))
                {

                    echo 'zhopito';
                }*/
                if(array_key_exists('error', $json))
                {
                    sleep(ERROR_TIMEOUT);
                    continue;
                }   
                if($action  == 'CREATE_GOODS')
                {
                    if(array_key_exists('market_item_id', $json['response']))
                    {
                        $item_id = $json['response']['market_item_id'];
                        $goodArr = explode('&', $good);
                        $goodIDItem = $goodArr[count($goodArr) - 1];
                        $goodIDItem = explode('=', $goodIDItem);
                        $answ = "UPDATE good SET item_id = ".$item_id." WHERE id = ".$goodIDItem[1];
                        $answ =  mysqlQuery($answ);
                    }
                }
                if($action == 'UPDATE_GOODS')
                {
                    if(array_key_exists('market_item_id', $json['response']))
                    {
                        $item_id = $json['response']['market_item_id'];
                        $goodArr = explode('&', $good);
                        $goodIDItem = $goodArr[count($goodArr) - 1];
                        $goodIDItem = explode('=', $goodIDItem);
                        $answ = "UPDATE good SET need_update = 0 WHERE id = ".$goodIDItem[1];
                        $answ =  mysqlQuery($answ);
                    }
                }
           sleep(TIMEOUT);
        }
    }
    private function deleteGood($good, $Loger)
    {

        $answ = "DELETE FROM good WHERE item_id = ".$good;
        mysqlQuery($answ);
        $url = VK_URL.'market.delete?access_token='.ACCESS_TOKEN.'&v=5.131&owner_id='.OWNER_ID.'&item_id='.$good;
        $json_html = file_get_contents($url);
        $json = json_decode($json_html, true);
        $Loger->setDebug($json);
        //----------
        if(array_key_exists('error', $json))
        {
            sleep(ERROR_TIMEOUT);
            return;
        }
        return;
    }
    public function deleteGoods($goods, $Loger)
    {
            foreach ($goods as $good)
            {
                self::deleteGood($good, $Loger);
                sleep(TIMEOUT);
            }
        return;
    }
}
