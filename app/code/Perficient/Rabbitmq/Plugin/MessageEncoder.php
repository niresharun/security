<?php
/**
 * Remove slashes from rabbitmq message
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Plugin;

use Magento\Framework\MessageQueue\MessageEncoder as MagentoMessageEncoder;

class MessageEncoder
{
    /**
     * Remove slashes from json string
     *
     * @param $result
     * @param $topic
     * @return mixed|string
     */
    public function afterEncode(MagentoMessageEncoder $subject, $result, $topic)
    {
        //Check message in second position in publish message
        $stingPosition = strpos((string) $result, 'message');

        /*
         * #JIRA TASK: BMKONGOING-213
         * Added topic condition to fix the export issue
        */
        if ($stingPosition != 2 && $topic != 'import_export.export') {
            $jsonString = stripslashes((string) $result);
            $jsonString = str_replace('["', '[', $jsonString);
            $jsonString = str_replace('"]', ']', $jsonString);
            $jsonString = str_replace('}"', '}', $jsonString);
            $jsonString = str_replace('"{', '{', $jsonString);
            return $jsonString;
        }
        return $result;
    }
}