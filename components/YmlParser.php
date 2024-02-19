<?php
namespace components;

class YmlParser
{

    public function getGoodsFromYML($YMLURL)
    {
        $Loger =  new Loger;
        $Loger->setLog('[info] Загрузка YML...');
        $answ = "UPDATE good SET error = 0 WHERE (shop_id = ".GROUP_ID.")";
        mysqlQuery($answ);
        $goods = [];
        set_time_limit(300);
        $ch = curl_init($YMLURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
            if(defined('CONSOOLE_ROOT')) 
            {
                $path = CONSOOLE_ROOT.'/runtime';
            }else{
                $path = $_SERVER['DOCUMENT_ROOT'].'/runtime';
            }
        $shopDir = $path.'/shop_'. GROUP_ID;   
        if(!is_dir($shopDir)) mkdir($shopDir, 0755);
        $file = $shopDir. '/file.yml';
        file_put_contents($file, $html);
        $data = simplexml_load_file($file);
        $i = 0;
            foreach($data->shop->offers->offer as $good)
            {
                
                $goods[$i] = [];
                $goods[$i]['id']          = strval($good['id']);
                $goods[$i]['available']   = (strtolower(strval($good['available'])) == 'true') ? 1 : 0;
                $goods[$i]['url']         = strval($good->url);
                $goods[$i]['price']       = intval($good->price);
                    if(is_int(intval($good->old_price)))
                    {
                        $goods[$i]['old_price']       = intval($good->old_price);
                    }
                    if(is_int(intval($good->categoryId)))
                    {
                        $goods[$i]['categoryId']       = intval($good->categoryId);
                    }
                $goods[$i]['picture']       = strval($good->picture);
                $goods[$i]['store']   = (strtolower($good->store)== 'true') ? 1 : 0;
                $goods[$i]['pickup']  = (strtolower($good->store)== 'true') ? 1 : 0;
                $name = strval($good->name);
                $name = str_replace("'", "\'", $name);
                $goods[$i]['name']     = $name;
                $goods[$i]['vendor']   = strval($good->vendor);
                $goods[$i]['color']    = 'no define';
                $goods[$i]['size']     = 'no define';
                    if(property_exists($good, 'param'))
                    {
                        foreach($good->param as $param)
                        {
                            if(mb_strtolower(strval($param['name'][0])) == 'цвет')
                            {
                                $goods[$i]['color'] = strval($param[0]);
                            }
                            if(mb_strtolower(strval($param['name'][0])) == 'размер')
                            {
                                $goods[$i]['size'] = strval($param[0]);
                            }
                       }
                    }
                $i++;
            }
            if(file_exists($file)) unlink($file);

            return $goods;
    }
}
