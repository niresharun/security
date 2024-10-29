<?php
/**
 * Set Address Configuration
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<sachin.badase@Perficient.com>
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Customer\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr config data
 */
class ConfigData implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const CUSTOMER_ADDRESS_TEMPLATES_TEXT = 'customer/address_templates/text';
    final const CUSTOMER_ADDRESS_TEMPLATES_HTML = 'customer/address_templates/html';
    final const CUSTOMER_ADDRESS_TEMPLATES_PDF = 'customer/address_templates/pdf';
    final const SCOPE_ID = false;

    /**
     * ConfigData constructor.
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    )
    {
    }

    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setCustomerAddressTemplatesValues();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Order Statuses
     */
    public function setCustomerAddressTemplatesValues(): void
    {
        $this->configWriter->save(self::CUSTOMER_ADDRESS_TEMPLATES_TEXT, "{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
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
        {{depend location}}Address Type: {{var location}}{{/depend}}
        {{depend order_shipping_notes}}Order/Shipping Notes: {{var order_shipping_notes}}{{/depend}}
        {{depend delivery_appointment}}<br /> Delivery Appointment: {{var delivery_appointment}}{{/depend}}", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CUSTOMER_ADDRESS_TEMPLATES_PDF, "{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
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
{{depend location}}Address Type: {{var location}}{{/depend}}
{{depend order_shipping_notes}}Order/Shipping Notes: {{var order_shipping_notes}}{{/depend}}]]>", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CUSTOMER_ADDRESS_TEMPLATES_HTML, "{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{var country}}<br />
{{depend telephone}}T: <a href=\"tel:{{var telephone}}\">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}
{{depend delivery_appointment}}<br />Delivery Appointment: {{var delivery_appointment}}{{/depend}}
{{depend loading_dock_available}}<br />Loading Dock Available: {{var loading_dock_available}}{{/depend}}
{{depend location}}<br />Address Type: {{var location}}{{/depend}}
{{depend order_shipping_notes}}<br />Order/Shipping Notes: {{var order_shipping_notes}}{{/depend}}
{{depend delivery_appointment}}<br /> Delivery Appointment: {{var delivery_appointment}}{{/depend}}", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    public function getAliases(): array
    {
        return [];
    }
}
