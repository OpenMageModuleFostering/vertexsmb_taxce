<?php

$installer = $this;
$installer->startSetup();

if ($installer->getConnection()->isTableExists($installer->getTable('taxce/taxrequest')) == true) {
    
    $installer->getConnection()->addColumn($installer->getTable('taxce/taxrequest'),
        'tax_area_id', 
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => null,
            'length' => 255,
            'comment' => 'Tax Jurisdictions Id'
        )
    ); 
  
}
  
$this->endSetup();