<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 11/19/13
 * Time: 6:29 PM
 */

namespace api\lang;

final class Data {
    private $desc,$good,$bad,$warn,$ease,$func;
    public  function __construct($desc,$good,$bad,$warn,$ease,&$func){
        $this->desc = $desc;
        $this->good = $good;
        $this->bad = $bad;
        $this->warn = $warn;
        $this->ease = $ease;
        $this->func = $func;
    }
    public function getDesc(){return $this->desc;}
    public function getGood(){return $this->good;}
    public function getBad(){return $this->bad;}
    public function getEase(){return $this->ease;}
    public function getWarn(){return $this->warn;}
    public function getFunc(){return $this->func;}
}

/**
 * Class Loader.  Loads a given
 * language file for specific apiClass and
 * all its methods.
 * @package api\lang
 */
final class Loader {
    //holds all the instance loaders
    private static $loaders = array();

    //states
    public static $ERROR = 'ERROR';
    public static $WARN = 'WARN';
    public static $GOOD = 'GOOD';
    public static $NONE = 'NONE';

    //below are instance variables
    //final
    private $DIR;
    private $LANGDIR;
    private $CLASSNAME;
    private $LANG='en';

    private $data = array();

    /**
     * Construction happens through static
     * constructors.  Generates singleton for
     * each apiClass.
     */
    private function __construct(){}

    public function toArray($method, $classData){
        $data = $this->get($method);
        $func = $data->getFunc();
        $test = ($classData === null) ? null : $func($classData);

        $msg = null;
        switch($test){
            case self::$ERROR:
                $msg = $data->getBad();
                break;
            case self::$WARN:
                $msg = $data->getWarn();
                break;
            case self::$GOOD;
                $msg = $data->getGood();
                break;
            case self::$NONE:
            default:
                $msg = null;
        }

        return array(
            'msg'=>$msg,
            'ease'=>$data->getEase(),
            'desc'=>$data->getDesc()
        );
    }

    /**
     * Create a loader object and load the
     * language file
     * @param string $className The api class to load
     * @param string $lang The language to load
     * @return Loader An instance of the Loader object.
     * @throws \Exception If the language file does not exist.
     */
    private static function Load($className,$lang='en'){
        $loader = new Loader();

        $dir = dirname(__FILE__);

        if(!is_dir($dir.'/'.$lang))
            throw new \Exception("Unsupported Language ($lang)");

        $langDir = $dir . '/' . $lang;

        $loader->DIR = $dir;
        $loader->LANGDIR = $langDir;
        $loader->CLASSNAME = $className;
        $loader->LANG = $lang;

        self::$loaders[self::makeName($className,$lang)] = $loader;
error_reporting(E_ALL);
        if(file_exists($langDir.'/'.$className.'.php'))
            include $loader->LANGDIR.'/'.$className.'.php';

        return $loader;
    }

    /**
     * Creates a key for looking up the loader.
     * @param $className
     * @param $lang
     * @return string
     */
    private static function makeName($className,$lang){
        return $lang.'_'.$className;
    }

    /**
     * Quick check for apiMethod before accessing
     * @param $apiMehtod
     * @throws \Exception
     */
    private function getApiMethod($apiMehtod){
        if(!isset($this->data[$apiMehtod])){
            $temp = function(){Loader::$NONE;};
            return new Data(null,null,null,null,null,$temp);
        }
        return $this->data[$apiMehtod];
    }

    /**
     * Get the loader object for a given api class and
     * language.  If it the loader object does not exist, then
     * it will be created.
     * @param $className
     * @param string $lang
     * @return Loader An instance of the loader for the given className and language
     */
    public static function getLoader($className,$lang='en'){
        $key = self::makeName($className,$lang);
        if(!isset(self::$loaders[$key]))
            self::Load($className,$lang);
        return self::$loaders[$key];
    }

    /**
     * Get the description of the loader
     * @param $apiMethod
     * @return \api\lang\Data
     * @throws \Exception
     */
    public function get($apiMethod){
        return $this->getApiMethod($apiMethod);
    }

    /**
     * Register the method and its associated
     * information
     * @param $apiMethod
     * @param $desc
     * @param $good
     * @param $bad
     * @param $func
     */
    public function register($apiMethod, $desc, $good, $bad, $warn, $func, $ease=null){
        $this->data[$apiMethod] = new Data($desc,$good,$bad,$warn, $ease,$func);
    }


} 