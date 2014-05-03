<?php
/**
 * Author: Will Smelser
 * Date: 5/3/14
 * Time: 6:31 PM
 * Project: simple-seo-api.com
 */
$base_path = __DIR__;
if (realpath( $base_path ) !== false) {
    $base_path = realpath($base_path);
}
$base_path = rtrim($base_path, '/').'/';
$base_path = str_replace('\\', '/', $base_path);

$tmpdir = $base_path.'/../extensions/seo/tmp';

$now = time();

foreach(scandir($tmpdir) as $file){
    if($file[0] !== '.'){
        if(filemtime($tmpdir.'/'.$file) < $now-24*60*60){
            echo 'DELETE - ' . $file." \n";
            unlink($tmpdir.'/'.$file);
        }
    }
}