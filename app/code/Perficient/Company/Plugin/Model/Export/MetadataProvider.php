<?php

namespace Perficient\Company\Plugin\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Ui\Model\Export\MetadataProvider as parentClass;

class MetadataProvider
{
    public function beforeGetRowData(parentClass $subject, DocumentInterface $document, $fields, $options): array
    {
        if (isset($data)) {
            $data = $options['type_of_projects'];
            foreach ($data as $key => $value) {
                if (preg_match("/Residential /i", (string)$value)) {
                    $options['type_of_projects'][$key] = str_replace('Residential ', 'Residential, ', (string)$value);
                } elseif (preg_match("/Commercial /i", (string)$value)) {
                    $options['type_of_projects'][$key] = str_replace('Commercial ', 'Commercial, ', (string)$value);
                }

            }
        }
        return [$document, $fields, $options];
    }
}
