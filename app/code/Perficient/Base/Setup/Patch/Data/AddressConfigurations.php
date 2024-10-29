<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
/**
 * Patch script to update address templates configuration
 */
class AddressConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const FORMAT_TEXT = 'text';
    final const FORMAT_HTML = 'html';
    final const FORMAT_PDF = 'pdf';
    final const PATH_PREFIX = 'customer/address_templates/';
    final const SCOPE_ID = 0;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setAddressTextFormat();
        $this->setAddressHtmlFormat();
        $this->setAddressPdfFormat();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Set Address Text Data configuration
     */
    public function setAddressTextFormat(): void
    {
        $pathFormatText = self::PATH_PREFIX . self::FORMAT_TEXT;
        $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{var country}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}
{{depend delivery_appointment}}Delivery Appointment: {{var delivery_appointment}}{{/depend}}
{{depend loading_dock_available}}Loading Dock Available: {{var loading_dock_available}}{{/depend}}
{{depend location}}Location: {{var location}}{{/depend}}
{{depend receiving_hours}}Receiving Hours: {{var receiving_hours}}{{/depend}}
{{depend receiver_telephone}}Receiver Telephone: {{var receiver_telephone}}{{/depend}}
{{depend receiver_name}}Receiver Name: {{var receiver_name}}{{/depend}}
{{depend order_shipping_notes}}Order/Shipping Notes: {{var order_shipping_notes}}{{/depend}}';
        $this->configWriter->save($pathFormatText, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Address HTML Data configuration
     */
    public function setAddressHtmlFormat(): void
    {
        $pathFormatHtml = self::PATH_PREFIX . self::FORMAT_HTML;
        $value = '<![CDATA[{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{var country}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}
{{depend delivery_appointment}}<br />Delivery Appointment: {{var delivery_appointment}}{{/depend}}
{{depend loading_dock_available}}<br />Loading Dock Available: {{var loading_dock_available}}{{/depend}}
{{depend location}}<br />Location: {{var location}}{{/depend}}
{{depend receiving_hours}}<br />Receiving Hours: {{var receiving_hours}}{{/depend}}
{{depend receiver_telephone}}<br />Receiver Telephone: {{var receiver_telephone}}{{/depend}}
{{depend receiver_name}}<br />Receiver Name: {{var receiver_name}}{{/depend}}
{{depend order_shipping_notes}}<br />Order/Shipping Notes: {{var order_shipping_notes}}{{/depend}}]]>';
        $this->configWriter->save($pathFormatHtml, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Address PDF Data configuration
     */
    public function setAddressPdfFormat(): void
    {
        $pathFormatPdf = self::PATH_PREFIX . self::FORMAT_PDF;
        $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{var country}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|
{{depend delivery_appointment}}Delivery Appointment: {{var delivery_appointment}}{{/depend}}
{{depend loading_dock_available}}Loading Dock Available: {{var loading_dock_available}}{{/depend}}
{{depend location}}Location: {{var location}}{{/depend}}
{{depend receiving_hours}}Receiving Hours: {{var receiving_hours}}{{/depend}}
{{depend receiver_telephone}}Receiver Telephone: {{var receiver_telephone}}{{/depend}}
{{depend receiver_name}}Receiver Name: {{var receiver_name}}{{/depend}}
{{depend order_shipping_notes}}Order/Shipping Notes: {{var order_shipping_notes}}{{/depend}}]]>';
        $this->configWriter->save($pathFormatPdf, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
