<?php
/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_address'),
    'tax_area_id',
    array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable' => false,
    'default' => null,
    'length' => 255,
    'comment' => 'Tax Jurisdiction Id'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_address'),
    'tax_area_id',
    array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable' => false,
    'default' => null,
    'length' => 255,
    'comment' => 'Tax Jurisdiction Id'
    )
);

$this->endSetup();
