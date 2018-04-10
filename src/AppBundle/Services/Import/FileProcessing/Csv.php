<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/4/16
 * Time: 4:50 PM
 */

namespace AppBundle\Services\Import\FileProcessing;

use AppBundle\Services\Import\ImportInterface;

class Csv implements FileProcessorInterface
{
    const DELIMITER = ',';
    /**
     * @var ImportInterface
     */
    private $importService;

    public function __construct(ImportInterface $importService)
    {
        $this->importService = $importService;
    }

    /**
     * @param string $filePath
     * @return array
     * @throws \Exception
     */
    public function process($filePath)
    {
        $lineCounter = 0;
        $counter = 0;
        $warnings = null;

        if (($handle = fopen($filePath, "r")) !== false) {

            while (($row = fgetcsv($handle, null, static::DELIMITER)) !== false) {

                $lineCounter++;
                if ($lineCounter > 1) {

                    $this->importService->reset();
                    $this->importService->setData($row);
                    $this->importService->import();
                    if (empty($this->importService->getWarning())) {
                        $counter++;
                    } else {
                        $error = $this->importService->getWarning();

                        $warnings[] = 'Warning for line ' . $lineCounter . ' : ' . $error;
                    }
                    $this->importService->reset();
                }
            }
            return array($counter, $warnings);
        }
    }
}