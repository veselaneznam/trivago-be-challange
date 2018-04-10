<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/6/16
 * Time: 9:13 PM
 */

namespace AppBundle\Services\Import\Validator;

interface ValidatorInterface
{    
    public function validate();
    
    public function getMessage();
}