<?php
class LogEdit{

    public static function writeLogs($logInfo){
        $dir = __DIR__."/modules/log/auth.log";

        if (!file_exists($dir)){
            echo "Error: File '/modules/log/auth.log' doen't exist.";
        }
        else{
            $fw = fopen($dir, "a");
            fwrite($fw, "\n".date('Y-m-d H:i:s').' '.$logInfo);
            fclose($fw);
        }
    }
}