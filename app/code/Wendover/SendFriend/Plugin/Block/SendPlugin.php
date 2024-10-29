<?php
declare(strict_types=1);

namespace Wendover\SendFriend\Plugin\Block;

use Magento\SendFriend\Block\Send;

class SendPlugin
{
    /**
     * @param Send $subject
     * @param string $result
     * @return string
     */
    public function afterGetSendUrl(Send $subject, string $result): string {
        $childId = $subject->getRequest()->getParam('child_id');
        if (empty($childId)) {
            return $result;
        }
        return $subject->getUrl(
            'sendfriend/product/sendmail',
            [
                'id' => $subject->getProductId(),
                'cat_id' => $subject->getCategoryId(),
                'child_id' => $childId,
            ]
        );
    }
}
