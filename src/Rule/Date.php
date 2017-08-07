<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 07/08/2017
 * Time: 16:56
 */

namespace ObjectivePHP\Validation\Rule;


use ObjectivePHP\Validation\Rule\Adapter\ZendValidatorAdapter;

class Date extends ZendValidatorAdapter
{

    /**
     * Date constructor.
     */
    public function __construct($format = \Zend\Validator\Date::FORMAT_DEFAULT)
    {
        $this->setValidator(new \Zend\Validator\Date(compact('format')));
    }
}
