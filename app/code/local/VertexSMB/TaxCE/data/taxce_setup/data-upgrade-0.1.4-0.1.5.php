<?php
$installer = $this;
 
$data = array(
    array(         
        'class_name'   => 'Reward Points',
        'class_type'   => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
    )  
);

 if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_Reward' )
                     && Mage::getConfig ()->getModuleConfig ( 'Enterprise_Reward' )->is('active', 'true')) {
    foreach ($data as $row) {
        $installer->getConnection()->insertForce($installer->getTable('tax/tax_class'), $row);
    }
}
  