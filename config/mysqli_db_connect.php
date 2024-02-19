<?php
class DB
{
	
    static $link;
    static $count = 0; 

    public static function connect()
    {// Синглтончик-с в целях экономии
        if(empty(self::$link))
        {
            self::$link = @mysqli_connect('IP', 'username', 'password', 'dbname')
                           or die('No connect'); 
            
            mysqli_set_charset(self::$link, 'utf8');
        }
    }
}

// Запускаем не отходя от кассы
    DB::connect();
	 function mysqlQuery($sql) 
    {
        $result = mysqli_query(db::$link, $sql) 
                  or die(mysqli_error(db::$link)); 
     
        return $result;
    }    