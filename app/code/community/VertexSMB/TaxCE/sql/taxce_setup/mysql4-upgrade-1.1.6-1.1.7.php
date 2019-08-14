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

if ($installer->getConnection()->isTableExists($installer->getTable('taxce/taxrequest')) == true) {
    $installer->getConnection()->addIndex(
        $installer->getTable('taxce/taxrequest'),
        $installer->getIdxName(
            'taxce/taxrequest',
            array(
            'request_id'
            )
        ),
        array(
        'request_id'
        ),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
    
    $installer->getConnection()->addIndex(
        $installer->getTable('taxce/taxrequest'),
        $installer->getIdxName(
            'taxce/taxrequest',
            array(
            'request_type'
            )
        ),
        array(
        'request_type'
        )
    );
    
    $installer->getConnection()->addIndex(
        $installer->getTable('taxce/taxrequest'),
        $installer->getIdxName(
            'taxce/taxrequest',
            array(
            'order_id'
            )
        ),
        array(
        'order_id'
        )
    );
}

$this->endSetup();
