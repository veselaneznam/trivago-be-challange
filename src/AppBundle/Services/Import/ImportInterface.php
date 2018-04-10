<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/4/16
 * Time: 4:55 PM
 */

namespace AppBundle\Services\Import;


interface ImportInterface
{
    public function import();

    public function getWarning();

    public function reset();

    /**
     * @param array $row
     * @return mixed
     */
    public function setData(array $row);
}