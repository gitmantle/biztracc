<?php
/**
 * Suito PHP
 *
 * Copyright (c) 2010 - 2015 TriRand Ltd
 *
 *
 * @category   Guriddo Suito PHP
 * @package    Guriddo Suito PHP
 * @copyright  Copyright (c) 2010 - 2015 TriRand Ltd (http://www.guriddo.net)
 * @license    http://guriddo.net/?page_id=103507   
 * @version    5.2.1
 */

PHPSuito_Autoloader::Register();

/**
 * PHPSuito_Autoloader
 *
 * @category    Guriddo Suito PHP
 * @package     Guriddo Suito PHP
 * @copyright   Copyright (c) 2006 - 2015 TriRand Ltd (http://guriddo.net)
 */
class PHPSuito_Autoloader
{
    /**
     * Register the Autoloader with SPL
     *
     */
    public static function Register() {
        if (function_exists('__autoload')) {
            //    Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__autoload');
        }
		
        //    Register ourselves with SPL
        return spl_autoload_register(array('PHPSuito_Autoloader', 'Load'));
    }   //    function Register()


    /**
     * Autoload a class identified by name
     *
     * @param    string    $pClassName        Name of the object to load
     */
    public static function Load($pClassName){
        if ((class_exists($pClassName,FALSE)) ) {
            return FALSE;
        }

        $pClassFilePath = PHPSUITO_ROOT.$pClassName.'.php';
        if ((file_exists($pClassFilePath) === FALSE) || (is_readable($pClassFilePath) === FALSE)) {
            //    Can't load
            return FALSE;
        }
        require($pClassFilePath);
    }   //    function Load()

}
