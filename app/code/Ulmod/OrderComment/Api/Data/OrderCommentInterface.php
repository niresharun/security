<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ulmod\OrderComment\Api\Data;

/**
 * Interface OrderCommentInterface
 * @api
 */
interface OrderCommentInterface
{
    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return null
     */
    public function setComment($comment);
}
