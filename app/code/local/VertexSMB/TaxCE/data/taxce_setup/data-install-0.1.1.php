<?php
/**
 * @package     VertexSMB_TaxCE
 * @copyright   Copyright (c) 2015 Net@Work (http://www.netatwork.com)
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
$installer = $this;
 
$data = array(
    array(         
        'class_name'   => 'Refund Adjustments',
        'class_type'   => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
    ),
 array(         
        'class_name'   => 'Gift Options',
        'class_type'   => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
    )     
);


foreach ($data as $row) {
    $installer->getConnection()->insertForce($installer->getTable('tax/tax_class'), $row);
}
  