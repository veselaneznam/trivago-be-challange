<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/4/16
 * Time: 4:49 PM
 */

namespace AppBundle\Services\Import\FileProcessing;


interface FileProcessorInterface
{
    /**
     * @param $filePath
     * @return array
     * @throws \Exception
     */
    public function process($filePath);
}