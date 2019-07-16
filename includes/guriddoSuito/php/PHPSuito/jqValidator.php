<?php
/**
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @version 5.3.1
 * @package jqValidator
 *
 * @abstract
 * A PHP class for  validation and sanitataion of the posted data.
 * Can be used separatley. The calss is build in jqGrid and jqForm classes 
 *
 */
/*
 nclude 'jqValidator.php';
$validations = array(
    'name' => array("required"=>true),
    'email' => array('email'=>true, "required"=>true),
    'alias' => array('anything'=>true),
    'pwd'=>array('anything'=>true),
    'gsm' => array('phone'=>true),
    'birthdate' => array('date'=>true, "format"=>"d/m/Y"),
	'price'=>array("float"=>true,"minValue"=>10,"maxValue"=>50)
);
$sanatize = array('alias');

$validator = new jqValidator($validations, $sanatize);

$testdata = array("email"=>"test@ssomom", "birthdate"=>"01/01/1973", "name"=>'d', "alias"=>"<b>gfgf</b>","gsm"=>"1234567890", "pwd"=>"sssssssss", "price"=>15);

date_default_timezone_set('UTC');

if($validator->validate($testdata))
{
    $testdata = $validator->sanatize($testdata);
	var_dump($testdata);
    // now do your saving, $_POST has been sanatized.
    echo $validator->getJSON();
}
else
{
    echo ($validator->getJSON());
}

 */

class jqValidator 
{
    public static $regexes = Array(
    		'date' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$",
			'datetime' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$",
			'time' => "^[0-9]{2}[:/][0-9]{1,2}[:/][0-9]{1,2}\$",
    		'alfanum' => "^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
    		'not_empty' => "[a-z0-9A-Z]+",
    		'words' => "^[A-Za-z]+[A-Za-z \\s]*\$",
    		'phone' => "^[0-9]{10,11}\$",
    		'zipcode' => "^[1-9][0-9]{3}[a-zA-Z]{2}\$",
    		'plate' => "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$",
    		'2digitopt' => "^\d+(\,\d{2})?\$",
    		'2digitforce' => "^\d+\,\d\d\$",
    		'anything' => "^[\d\D]{0,}\$", //incl empty
			'string'=> "^[\d\D]{0,}\$",
			'text'=> "^[\d\D]{0,}\$"
    );
	public static $errors_msg = array(
		"notdef" => ": is not defined in the post!",
		"notvalid"=> "is not valid!",
		"required"=>" field is required!",
		"min" => "value must be greater than or equal to ",
		"max"=>"value must be less than or equal to",
		"notype"=>"Unknown type"
	);
    private $validations, $sanatations, $errors, $corrects, $fields; 
	public static $lasterror;
	
	public $stringoutput = 'text'; //or json

	public $linebreak = '\n';

    public function __construct($validations=array(), $sanatations = array())
    {
    	$this->validations = $validations;
    	$this->sanatations = $sanatations;
    	$this->errors = array();
    	$this->corrects = array();
    }

    /**
     * Validates an array of items (if needed) and returns true or false
     *
     */
    public function validate($items)
    {
    	$this->fields = $items;
    	$havefailures = false;
    	foreach($this->validations as $key=>$rules)
    	{
			$rule = $rules;
			if(!isset($rule['displayName'])) {
				$rule['displayName'] = $key;
			}
            if(!array_key_exists($key,$items)) 
            {
                    $this->addError($key, self::$errors_msg['notdef'], $rule['displayName']);
                    continue;
            }    		
			self::$lasterror = "";
			$result = self::validateItem($items[$key], $rule);
    		if($result === false) {
    			$havefailures = true;
    			$this->addError($key, self::$lasterror ? self::$lasterror : "unknown error when validating", $rule['displayName']);
    		} else {
    			$this->corrects[] = $key;
    		}
    	}
    	return(!$havefailures);
    }

    /**
     *
     *	Adds unvalidated class to thos elements that are not validated. Removes them from classes that are.
     */
    public function getJSON() {
		$errors = array();
		$correct = array();

		if(!empty($this->errors))
		{            
			foreach($this->errors as $key=>$val) { $errors[$key] = $val; }            
		}

		if(!empty($this->corrects))
		{
			foreach($this->corrects as $key=>$val) { $correct[$key] = $val; }                
		}
		if($this->stringoutput == 'text') {
			$output ='';
			foreach($this->errors as $key =>$val) {
				$output .= $val['display'].": ".$val['msg'].$this->linebreak;
			}
			return $output;
		}
		$output = array('errors' => $errors);//, 'correct' => $correct);

		return json_encode($output);
	}    

    /**
     *
     * Sanatizes an array of items according to the $this->sanatations
     * sanatations will be standard of type string, but can also be specified.
     * For ease of use, this syntax is accepted:
     * $sanatations = array('fieldname', 'otherfieldname'=>'float');
     */
    public function sanatize($items)
    {
		//var_dump($this->sanatations);
    	foreach($items as $key=>$val)
    	{
    		if(array_search($key, $this->sanatations) === false && !array_key_exists($key, $this->sanatations)) continue;
    		$items[$key] = self::sanatizeItem($val, $this->validations[$key]);
    	}
    	return($items);
    }

    /**
     *
     * Adds an error to the errors array.
     */ 
    private function addError($field, $type='string', $display)
    {
    	$this->errors[$field] = array("msg"=>$type, "display"=>$display);
    }
	public static function checkDate($format = 'Y-m-d H:i:s', $date, $ret = false)
	{
		$r = false;
		$d = DateTime::createFromFormat($format, $date);
		$check = $d !== false && $d->format($format) == $date;
		if($check) {
			if(!$ret) {
				$r = $check;
			} else {
				$r = $d;
			}
		}
		return $r;
	}

    /**
     *
     * Sanatize a single var according to $type.
     * Allows for static calling to allow simple sanatization
     */
    public static function sanatizeItem($var, $type)
    {
		$types = array("url","int","integer","float","number","email","text","string");
		$ctype = "string";
		foreach($types as $k=>$v) {
			if(array_key_exists($v, $type)) {
				$ctype = $v;
				break;
			}
		}
    	$flags = NULL;
    	switch($ctype)
    	{
    		case 'url':
    			$filter = FILTER_SANITIZE_URL;
    		break;
    		case 'int':
			case 'integer':
    			$filter = FILTER_SANITIZE_NUMBER_INT;
    		break;
    		case 'float':
			case 'number':
    			$filter = FILTER_SANITIZE_NUMBER_FLOAT;
    			$flags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
    		break;
    		case 'email':
    			$var = substr($var, 0, 254);
    			$filter = FILTER_SANITIZE_EMAIL;
    		break;
    		default:
    			$filter = FILTER_SANITIZE_STRING;
    			$flags = FILTER_FLAG_NO_ENCODE_QUOTES;
    		break;

    	}
    	$output = filter_var($var, $filter, $flags);		
    	return($output);
    }
	
	public static function checkRequired ($var, $rule){
		$retval = true;
		if(is_array($rule)) {
			if(isset($rule['required']) && $rule['required'] !== false) {
				if($var === '' ) {
					$retval = false;
				}
			}
		}
		return $retval;
	}
	
	public static function checkMin ($var, $rule, $type){
		$retval = true;
		if(is_array($rule)) {
			if(isset($rule['minValue']) && $rule['minValue'] !== "") {
				if($type == 'date' || $type == 'datetime' || $type == 'time') {
					$dateformat = 'Y-m-d';
					if(isset($rule['format']) && $rule['format'] !== "") {
						$dateformat = $rule['format'];
					}
					$date1 = self::checkDate($dateformat, $var, true);
					$date2 = self::checkDate($dateformat, $rule['minValue'], true);
					if($date1 !== false && $date2 !== false) {
						$retval = !($date1 < $date2); // PHP >=5.2.2
					}
				} else if( $var < $rule['minValue'] ) {
					$retval = false;
				}
			}
		}
		return $retval;
	}

	public static function checkMax ($var, $rule, $type){
		$retval = true;
		if(is_array($rule)) {
			if(isset($rule['maxValue']) && $rule['maxValue'] !== "") {
				if($type == 'date' || $type == 'datetime' || $type == 'time') {
					$dateformat = 'Y-m-d';
					if(isset($rule['format']) && $rule['format'] !== "") {
						$dateformat = $rule['format'];
					}
					$date1 = self::checkDate($dateformat, $var, true);
					$date2 = self::checkDate($dateformat, $rule['maxValue'], true);
					if($date1 !== false && $date2 !== false) {
						$retval = !($date1 > $date2); // PHP >=5.2.2
					}
				} else if( $var > $rule['maxValue'] ) {
					$retval = false;
				}
			}
		}
		return $retval;
	}
	
	public static function validateDate($var, $rule){
		$dateformat = 'Y-m-d';
		if(is_array($rule)) {
			if(isset($rule['format']) && $rule['format'] !== "") {
				$dateformat = $rule['format'];
			}
		}
		return self::checkDate($dateformat, $var);//DateTime::createFromFormat($dateformat, $var) === false ? false : true;		
	}

	public static function validateTime($var, $rule){
		$dateformat = 'H:i:s';
		if(is_array($rule)) {
			if(isset($rule['format']) && $rule['format'] !== "") {
				$dateformat = $rule['format'];
			}
		}
		return self::checkDate($dateformat, $var); //DateTime::createFromFormat($dateformat, $var) === false ? false : true;		
	}

    /** 
     *
     * Validates a single var according to $type.
     * Allows for static calling to allow simple validation.
     *
     */
    public static function validateItem($var, $rule)
    {
		$type = false;
		$basetypes = array("number","float","int","integer","boolean","email","url", "date", "time", "datetime", "ip");
		$validtypes = array_merge($basetypes, array_keys(self::$regexes));
		foreach($validtypes as $key=>$types) {
			if(is_array($rule)) {
				if(array_key_exists($types, $rule)) {
					if($rule[$types] !== false) {
						$type = $types;
						break;
					}
				}
			} else {
				if($types == $rule) {
					$type = $rule;
					break;
				}
			}
		}
		if(!$type) {
			self::$lasterror = self::$errors_msg['notype'];
			return false;
		}
    	if(array_key_exists($type, self::$regexes))
    	{
			if($type == 'date') {
				$returnval = self::validateDate($var, $rule);
			} else if( $type == 'time') {
				$returnval = self::validateTime($var, $rule);
			} else {
				$returnval =  filter_var($var, FILTER_VALIDATE_REGEXP, array("options"=> array("regexp"=>'!'.self::$regexes[$type].'!i'))) !== false;
				//var_dump($type, $var, $returnval);
			}
    		if(!$returnval) {
				self::$lasterror = "(".$type.") ".self::$errors_msg['notvalid'];
				return false;
			}
    	} else {
			$filter = false;
			switch($type)
			{
				case 'number':
				case 'float':
					$filter = FILTER_VALIDATE_FLOAT;
				break;
				case 'email':
					$var = substr($var, 0, 254);
					$filter = FILTER_VALIDATE_EMAIL;	
	    		break;
				case 'int':
				case 'integer':
					$filter = FILTER_VALIDATE_INT;
				break;
	    		case 'boolean':
					$filter = FILTER_VALIDATE_BOOLEAN;
				break;
				case 'ip':
					$filter = FILTER_VALIDATE_IP;
				break;
				case 'url':
					$filter = FILTER_VALIDATE_URL;
				break;
			}
			$returnval = ($filter === false) ? false : filter_var(trim($var), $filter) !== false ? true : false;
    		if(!$returnval) {
				self::$lasterror = "(".$type.") ".self::$errors_msg['notvalid'];
				return false;
			}
		}
		if(!self::checkRequired($var, $rule)) {
			self::$lasterror = self::$errors_msg['required'];
			return false;
		}
		if(!self::checkMin($var, $rule, $type)) {
			self::$lasterror = self::$errors_msg['min'].": ".$rule['minValue'];
			return false;
		}
		if(!self::checkMax($var, $rule, $type)) {
			self::$lasterror = self::$errors_msg['max'].": ".$rule['maxValue'];
			return false;
		}		
		return true;
    }
}           