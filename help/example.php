<?
function sync_catalog_vk($arr)
{
ini_set('max_execution_time', 0);
 
$url_vk = 'https://api.vk.com/method/';
$owner_id = '**********';
$access_token = '***********************************************************************************************************'; 
 
foreach ($arr as $key => $value)
{
if ($arr[$key]['vk_album_id'] == '')
{
// создание новой подборки
$buf = array();
$buf[] = 'owner_id=-'.$owner_id;
$buf[] = 'title='.urlencode($arr[$key]['name']);
$buf[] = 'access_token='.$access_token;
if ($arr[$key]['img'] <> '')
{
// Запрашиваем ссылку для загрузки фотографии
$url = $url_vk.'photos.getMarketAlbumUploadServer?group_id='.$owner_id.'&access_token='.$access_token;
echo '<br>'.$url;
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$upload_url = $json['response']['upload_url'];
 
// отправляем фотографию на сервер
echo '<br>'.$upload_url;
$ch = curl_init($upload_url); // создаем подключение
$postData['file'] = '@'.$_SERVER['DOCUMENT_ROOT'].$arr[$key]['img'];
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
sleep(3);
$json_html = curl_exec($ch);
curl_close($ch);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$server = $json['server'];
$photo = $json['photo'];
$hash = $json['hash'];
 
// сохраняем фотографию
$url = $url_vk.'photos.saveMarketAlbumPhoto?group_id='.$owner_id.'&photo='.$photo.'&server='.$server.'&hash='.$hash.'&access_token='.$access_token;
echo '<br>'.$url; 
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$buf[] = 'photo_id='.$json['response'][0]['pid'];
}
// добавляем подборку
$url = $url_vk.'market.addAlbum?'.implode('&', $buf);
echo '<br>'.$url; 
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$market_album_id = $json['response']['market_album_id'];
 
$arr[$key]['vk_album_id'] = $market_album_id;
$sql = 'UPDATE '.$arr[$key]['table'].' SET vk_album_id = "'.$market_album_id.'" WHERE '.$arr[$key]['table_name_col_id'].' = '.$arr[$key]['id'];
$query = mysql_query($sql);
}
foreach ($arr[$key]['goods'] as $key2 => $value2)
{
if ($arr[$key]['goods'][$key2]['vk_market_item_id'] == '')
{
 
// добавляем товар
 
$buf = array();
$buf[] = 'owner_id=-'.$owner_id; 
$buf[] = 'name='.urlencode($arr[$key]['goods'][$key2]['name']); 
$buf[] = 'description='.urlencode($arr[$key]['goods'][$key2]['description']);
$buf[] = 'category_id='.$arr[$key]['vk_category_id'];
$buf[] = 'price='.$arr[$key]['goods'][$key2]['price'];
$buf[] = 'deleted='.$arr[$key]['goods'][$key2]['deleted'];
$buf[] = 'access_token='.$access_token;
if ($arr[$key]['goods'][$key2]['img'] <> '')
{
// Запрашиваем ссылку для загрузки фотографии
$url = $url_vk.'photos.getMarketUploadServer?group_id='.$owner_id.'&access_token='.$access_token.'&main_photo=1';
echo '<br>'.$url;
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$upload_url = $json['response']['upload_url'];
 
// отправляем фотографию на сервер
echo '<br>'.$upload_url;
$ch = curl_init($upload_url); // создаем подключение
$postData['file'] = '@'.$_SERVER['DOCUMENT_ROOT'].$arr[$key]['goods'][$key2]['img'];
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
sleep(3);
$json_html = curl_exec($ch);
curl_close($ch);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$photo = $json['photo'];
$server = $json['server'];
$hash = $json['hash'];
$crop_data = $json['crop_data'];
$crop_hash = $json['crop_hash'];
 
// сохраняем фотографию
$url = $url_vk.'photos.saveMarketPhoto?group_id='.$owner_id.'&photo='.$photo.'&server='.$server.'&hash='.$hash.'&crop_data='.$crop_data.'&crop_hash='.$crop_hash.'&access_token='.$access_token;
echo '<br>'.$url; 
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$buf[] = 'main_photo_id='.$json['response'][0]['pid']; 
}
// добавляем товар
$url = $url_vk.'market.add?'.implode('&', $buf);
echo '<br>'.$url; 
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
$market_item_id = $json['response']['market_item_id'];
 
//переносим его в подборку
$url = $url_vk.'market.addToAlbum?access_token='.$access_token.'&owner_id=-'.$owner_id.'&item_id='.$market_item_id.'&album_ids='.$arr[$key]['vk_album_id'];
echo '<br>'.$url;
sleep(3);
$json_html = file_get_contents($url);
echo '<pre>';print_r($json);echo'</pre>';
 
//обновляем vk ключ у товара
$arr[$key]['goods'][$key2]['vk_market_item_id'] = $market_item_id;
$sql = 'UPDATE '.$arr[$key]['goods'][$key2]['table'].' SET vk_market_item_id = "'.$market_item_id.'" WHERE '.$arr[$key]['goods'][$key2]['table_name_col_id'].' = '.$arr[$key]['goods'][$key2]['id'];
$query = mysql_query($sql);
}
else
{
// изменяем товар
 
$buf[] = 'owner_id=-'.$owner_id; 
$buf[] = 'name='.urlencode($arr[$key]['goods'][$key2]['name']); 
$buf[] = 'description='.urlencode($arr[$key]['goods'][$key2]['description']);
$buf[] = 'category_id='.$arr[$key]['vk_category_id'];
$buf[] = 'price='.$arr[$key]['goods'][$key2]['price'];
$buf[] = 'deleted='.$arr[$key]['goods'][$key2]['deleted'];
$buf[] = 'access_token='.$access_token;
$buf[] = 'item_id='.$arr[$key]['goods'][$key2]['vk_market_item_id'];
 
$url = $url_vk.'market.getById?item_ids=-'.$owner_id.'_'.$arr[$key]['goods'][$key2]['vk_market_item_id'].'&access_token='.$access_token.'&extended=1';
echo '<br>'.$url;
sleep(3);
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
print_r($json);
$buf[] = 'main_photo_id='.$json['response'][1]['photos'][0]['pid'];
 
 
$url = $url_vk.'market.edit?'.implode('&', $buf);
echo '<br>'.$url; 
sleep(3);
 
$json_html = file_get_contents($url);
$json = json_decode($json_html, true);
echo '<pre>';print_r($json);echo'</pre>';
 
//удаляем из всех подборок
$list = array();
foreach ($arr as $key3 => $value3)
{
if ($arr[$key3]['vk_album_id'] <> $arr[$key]['vk_album_id'])
{
$list[] = $arr[$key3]['vk_album_id'];
}
}
$url = $url_vk.'market.removeFromAlbum?access_token='.$access_token.'&owner_id=-'.$owner_id.'&item_id='.$arr[$key]['goods'][$key2]['vk_market_item_id'].'&album_ids='.implode(',',$list);
echo '<br>'.$url;
sleep(3);
$json_html = file_get_contents($url);
echo '<pre>';print_r($json);echo'</pre>'; 
 
//переносим его в подборку
$url = $url_vk.'market.addToAlbum?access_token='.$access_token.'&owner_id=-'.$owner_id.'&item_id='.$arr[$key]['goods'][$key2]['vk_market_item_id'].'&album_ids='.$arr[$key]['vk_album_id'];
echo '<br>'.$url;
sleep(3);
$json_html = file_get_contents($url);
echo '<pre>';print_r($json);echo'</pre>'; 
 
}
}
}
}
 
В функцию мы передаем следующий массив

$array[$i]['name'] - название подборки
$array[$i]['img'] - ссылка на изображение подборки
$array[$i]['vk_album_id'] - номер подборки
$array[$i]['vk_category_id'] - номер категории товаров
$array[$i]['table'] - название таблицы где хранятся подборки
$array[$i]['table_name_col_id'] - название столбца в котором храниться уникальный номер подборки
$array[$i]['id'] - уникальный номер подборки
$array[$i]['goods'][$j]['name'] - название товара
$array[$i]['goods'][$j]['description'] - описание товара
$array[$i]['goods'][$j]['price'] - стоимость товара
$array[$i]['goods'][$j]['deleted'] - удален товар или нет
$array[$i]['goods'][$j]['img'] - ссылка на фотографию товара
$array[$i]['goods'][$j]['vk_market_item_id'] - номер товара по VK
$array[$i]['goods'][$j]['table'] - таблица в которой хранится товар
$array[$i]['goods'][$j]['table_name_col_id'] - название поля с уникальным id товара
$array[$i]['goods'][$j]['id'] - уникальный id товара
Смысл функции в том что она обходит массив с подборками и добавляет подборку если отсутствует vk_album_id.

За
?>