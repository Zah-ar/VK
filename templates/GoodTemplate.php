<?php
namespace templates;
use \components\Loger;
abstract class GoodTemplate
{
    private function setUrl(&$result, $good)
    {
        $result = str_replace('%url%', $good['url'], $result);
        return;
    }
    private function setParams(&$result, $good)
    {
        if($good['size'] !=  'no defined' && mb_strlen($good['size']) != 0)
        {
            $result = str_replace('%size%', $good['size'], $result);
        }else{
            $result = str_replace('%size%', '', $result);
            $result = str_replace('  ', '', $result);
        }
        if($good['color'] !=  'no defined' && mb_strlen($good['color']) != 0)
        {
            $result = str_replace('%color%', $good['color'], $result);
        }else{
            $result = str_replace('%color%', '', $result);
            $result = str_replace('  ', '', $result);
        }
        return;
    }
    public function getDescription($good)
    {
        /*$Loger =  new Loger;
        $Loger->setDebug($good);*/
        if(!defined('DESCRIPTION_TEMPLATE')) return $good['name'].' '.self::urlPrepare($good['url']);
        //https://vk.com/away.php?to=https%3A%2F%2F4mma.ru%2Fgood%2F49681%2F%3Futm_source%3Dvk.com%26utm_medium%3DVKontakte%26utm_campaign%3Dvk_market
        $result = DESCRIPTION_TEMPLATE;
        self::setUrl($result, $good);
        self::setParams($result, $good);
        return $result;
    }
}
    