<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 12/15/13
 * Time: 8:47 AM
 */

class ExtController extends Controller {
    public function parse($data){
        if(!isset($data['result']))
            throw new Exception('Expected $data to contain key "result".');

        return json_decode($data['result']);
    }

    public function renderInput($input){
        $type = gettype($input);
        switch($type){
            default:
                return $input;
            case 'boolean':
                return ($input) ? 'True' : 'False';
            case 'NULL':
                return 'Null';
            case 'object':
            case 'resource':
            case 'array':
            case 'unknown type':
                return 'Failed to parse type ('.$type.')';

        }
    }
}