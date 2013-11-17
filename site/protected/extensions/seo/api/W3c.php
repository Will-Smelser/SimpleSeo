<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 11/16/13
 * Time: 5:34 PM
 */

namespace api;

/**
 * Requires pear and following pear package:
 * 		http://pear.php.net/package/Services_W3C_HTMLValidator/
 * 		$>pear install Services_W3C_HTMLValidator
 */

require_once 'Services/W3C/HTMLValidator.php';

/**
 * These methods access the W3C validation api using the pear package
 * http://pear.php.net/package/Services_W3C_HTMLValidator/.
 * @package api
 */
class W3c {

    /**
     * @ignore
     * @param unknown $url
     */
    public function __construct($url){
        $this->url = $url;
    }

    /**
     * @ignore
     * @var unknown
     */
    private $url;

    /**
     * @ignore
     * @var unknown
     */
    private $lastW3Cerrors = null;

    /**
     * @ignore
     * @var unknown
     */
    private $lastW3Cwarnings = null;

    /**
     * @ignore
     * @var unknown
     */
    private $w3cCalled = false;

    /**
     * @ignore
     * @var unknown
     */
    private $w3cValid = false;

    /**
     * @ignore
     * @throws Exception
     */
    private function initW3c(){
        if(!$this->w3cCalled){
            $this->w3cCalled = true;
            $v = new \Services_W3C_HTMLValidator();
            $r = $v->validate($this->url);
            if($r !== false){
                $this->lastW3Cerrors = $r->errors;
                $this->lastW3Cwarnings = $r->warnings;
                $this->w3cValid = $r->isValid();
            }else{
                throw new Exception("Request to W3C failed.");
            }
        }
    }

    /**
     * Validates using W3C pear package
     * @return boolean True on success, False on failure
     * @throws Exception
     */
    public function validateW3C(){
        $this->initW3c();

        return $this->w3cValid;
    }

    /**
     * Return the error array from last validateW3C() request.
     * @see validateW3C()
     * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Error.html
     * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Message.html
     */
    public function getValidateW3Cerrors(){
        $this->initW3c();

        if($this->lastW3Cerrors === null)
            throw new \Exception("Request to W3C failed");

        return $this->lastW3Cerrors;
    }

    /**
     * Return the error array from last validateW3C() request.
     * @see validateW3C()
     * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Error.html
     * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Message.html
     */
    public function getValidateW3Cwarnings(){
        $this->initW3c();

        if($this->lastW3Cwarnings === null)
            throw new \Exception("Request to W3C failed");

        return $this->lastW3Cwarnings;
    }
}
?>
