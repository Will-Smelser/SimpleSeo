<?php
/**
 * Author: Will Smelser
 * Date: 4/5/14
 * Time: 6:03 PM
 * Project: simple-seo-api.com
 */

namespace api;

class FileGetContentsAdapter {
    private static $cachDir = 'C:/xampp/htdocs/simple-seo-api.com/site/protected/extensions/seo/tmp/';
    private static $expireMin = 30;

    /* gets the contents of a file if it exists, otherwise grabs and caches */
    public static function get_content($url) {


        //vars
        $file = self::$cachDir . md5($url);
        $currentTime = time();
        $expireTime = self::$expireMin * 60;
        $fileTime = (file_exists($file)) ? filemtime($file) : 0;

        $content = '';

        //decisions, decisions
        if(file_exists($file) && ($currentTime - $expireTime < $fileTime)) {
            $content = @file_get_contents($file);
            touch($file);
        } else {
            $content = @file_get_contents($url);

            //try and save on file io if we can
            if(file_exists($file)){
                $lastModified = 0;
                foreach($http_response_header as $row){
                    $parts = explode(':',$row);
                    if(strtolower($parts[0]) === 'last-modified'){
                        $lastModified = strtotime($parts[1]);
                    }
                }

                //document has been modified since our creation
                if($lastModified > $fileTime){
                    file_put_contents($file,$content);
                //document has not been modified, lets update the file's time
                }else{
                    touch($file);
                }
            }else{
                @file_put_contents($file,$content);
            }
        }

        return $content;
    }
}