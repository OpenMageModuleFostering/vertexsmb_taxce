<?php

$installer = $this;
$installer->startSetup();

 
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),
        'tax_area_id', 
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => null,
            'length' => 255,
            'comment' => 'Tax Jurisdictions Id'
        )
); 
   

$installer->getConnection()->addColumn($installer->getTable('sales/order_address'),
        'tax_area_id', 
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => null,
            'length' => 255,
            'comment' => 'Tax Jurisdictions Id'
        )
); 
 
$this->endSetup();