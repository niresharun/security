<?php
/**
 * Modify Customer Account Sales Order Navigation
 * @category: Magento
 * @package: Perficient/Sales
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Sales
 */
declare(strict_types=1);

namespace Perficient\Sales\Block\Order;

use IntlDateFormatter;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template;
use Magento\OrderHistorySearch\Model\Config;
use Magento\OrderHistorySearch\Model\Order\Customer\DataProvider as CustomerDataProvider;
use Magento\OrderHistorySearch\Model\Order\Status\DataProvider as OrderStatusDataProvider;

/**
 * Filters block
 *
 * @api
 * @since 100.2.0
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Filters extends \Magento\OrderHistorySearch\Block\Filters
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * ORDER_STATUS_COMPLETE
     */
    const ORDERSTATUS_COMPLETE = 'complete';

    /**
     * Filters constructor.
     *
     * @param Template\Context $context
     * @param Config $config
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        private readonly CustomerSessionFactory $customerSessionFactory,
        private readonly OrderStatusDataProvider $statusDataProvider,
        private readonly CustomerDataProvider $customerDataProvider,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $config, $customerSessionFactory, $statusDataProvider, $customerDataProvider, $timezone, $data);
        $this->config = $config;
        $this->timezone = $timezone;
    }

    /**
     * Get select order status element
     *
     * @throws LocalizedException
     * @since 100.2.0
     */
    public function getOrderStatusSelectElementHtml(): string
    {
        return $this
            ->getSelectElementToHtml(
                'order-status',
                'order-status',
                __('Order status'),
                $this->getOrderStatusOptions(),
                'order-statuses'
            );
    }

    /**
     * Get html select element
     *
     * @param Phrase $title
     *
     * @throws LocalizedException
     */
    private function getSelectElementToHtml(
        string $name,
        string $id,
        Phrase $title,
        array $options,
        string $additionalClasses = ''
    ): string {
        return $this
            ->getSelectBlock()
            ->setName($name)
            ->setId($id)
            ->setTitle($title)
            ->setValue($this->getRequest()->getParam($id, ''))
            ->setOptions($options)
            ->setClass('multiselect ' . $additionalClasses)
            ->getHtml();
    }

    /**
     * Return select block element
     *
     * @throws LocalizedException
     */
    private function getSelectBlock(): BlockInterface
    {
        $block = $this->getData('_select_block');
        if (null === $block) {
            $block = $this->getLayout()->createBlock(Select::class);
            $this->setData('_select_block', $block);
        }

        return $block;
    }

    /**
     * Get order statuses as options.
     */
    private function getOrderStatusOptions(): array
    {
        $defaultValue =
            [
                [
                    'value' => '',
                    'label' => __('All'),
                ],
            ];
        return array_merge($defaultValue, $this->getAllowedOrderStatusOptions());
    }

    /**
     * Get allowed order statuses as options.
     */
    private function getAllowedOrderStatusOptions(): array
    {
        $statuses = $this->statusDataProvider->getOrderStatusOptions();

        foreach ($statuses as $statusKey => $statusValue) {
            if ($statusValue['value'] == self::ORDERSTATUS_COMPLETE) {
                unset($statuses[$statusKey]);
            }
        }

        return $statuses;
    }
}
