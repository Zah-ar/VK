<?
    define('SOURCE', '********');
    define('VK_URL', 'https://api.vk.com/method/');
    define('ACCESS_TOKEN', '*****');
    define('OWNER_ID', *****);
    define('GROUP_ID', *******);
    define('TIMEOUT', 5);
    define('GOODS_STEP', 400); //count goods in one transaction
    define('TRANSACTION_TIMEOUT', 10); // transaction timeout
    define('ERROR_TIMEOUT', 20); // transaction error
    define('IMAGEDOWNLOAD_TIMEOUT',5); // image download timeout
    $tasks = [];
    $tasks['create_goods'] = 'CREATE_GOODS';
    $tasks['uodate_goods'] = 'UPDATE_GOODS';
    $tasks['delete_goods'] = 'DELETE_GOODS';
    define('DESCRIPTION_TEMPLATE', '
    %size%
    %color%

