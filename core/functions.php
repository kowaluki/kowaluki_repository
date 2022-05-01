<?php
    namespace functions; 

    function fileExt($file) {
        $fileExt = explode(".",$file);
        $number = count($fileExt);
        $number--;
        return $fileExt[$number];
        
    }

    function fileName($file) {
        $fileName = explode(".",$file);
        $number = count($fileName);
        $number--;
        unset($fileName[$number]);
        $name = "";
        for($i = 0; $i<count($fileName); $i++) {
            if($i!=0) $name .= ".";
            $name .= $fileName[$i];
        }
        return $name;
    }

    function getip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}
        return $ip;
    }

?>