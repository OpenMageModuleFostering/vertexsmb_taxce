<?php
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
  