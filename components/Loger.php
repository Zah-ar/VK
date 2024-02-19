<?php
    namespace components;

    class Loger
    {
        private $path;
        private $debugPath;

        public function __construct()
        {
            if(defined('CONSOOLE_ROOT')) 
            {
                $Root = CONSOOLE_ROOT;
            }else{
                $Root = $_SERVER['DOCUMENT_ROOT'];
            }
            $dir = $Root .'/logs/shop_'.GROUP_ID;
            if(!is_dir($dir)) mkdir($dir, 0775);
            $this->path = $dir.'/log.txt';
            if(!is_dir($dir.'/debug')) mkdir($dir.'/debug', 0775);
            $this->debugPath = $dir.'/debug/log.txt';
            return;
        }
        public function setLog($message)
        {
            $txt  = '['.date('d-m-Y H:i:s', time()).'] ';
            $txt .= $message . PHP_EOL;
            file_put_contents($this->path, $txt, FILE_APPEND);
            return;
        }
        public function setDebug($message)
        {
            if(is_array($message))
            {
                file_put_contents($this->debugPath, print_r($message, true));    
                return;
            }
            if(!$message)
            {
                file_put_contents($this->debugPath, 'FALSE');    
            }
            $txt  = '['.date('d-m-Y H:i:s', time()).'] ';
            $txt .= $message . PHP_EOL;
            file_put_contents($this->debugPath, $txt);
            return;
        }
        
    }