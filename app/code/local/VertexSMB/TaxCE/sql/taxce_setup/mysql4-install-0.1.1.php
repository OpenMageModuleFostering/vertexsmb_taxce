<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
$installer = $this;
if ($installer->getConnection()->isTableExists($installer->getTable('taxce/taxrequest')) != true) {
    /**
     * Create table 'taxce/taxrequest'
     */
    $table = $installer->getConnection()
        ->newTable($installer->getTable('taxce/taxrequest'))
        ->addColumn('request_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Request Id')
        ->addColumn('request_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            'default'   => NULL,
            ), 'Request Type')   
        ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable'  => false,             
            ), 'Quote Id')      
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable'  => false,  
            'default'   => 0,
            ), 'Order Id')         
        ->addColumn('total_tax', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false, 
            'default'   =>  NULL,
            ), 'Total Tax')             
        ->addColumn('request_date', Varien_Db_Ddl_Table::TYPE_DATETIME, 0, array(
            'nullable'  => false
            ), 'Request Date')
        ->addColumn('request_xml', Varien_Db_Ddl_Table::TYPE_TEXT, 0, array(
            'nullable'  => false,
            'default'   => NULL,
            ), 'Request XML')
        ->addColumn('response_xml', Varien_Db_Ddl_Table::TYPE_TEXT, 0, array(
            'nullable'  => false,
            'default'   => NULL,
            ), 'Response XML')        
        ->setComment('Log of requests to Vertex SMB');
    $installer->getConnection()->createTable($table);
}
/*Customer Attribute*/
$entity = $this->getEntityTypeId('customer');
if(!$this->attributeExists($entity, 'customer_code'))
{ 
    $this->addAttribute($entity, 'customer_code', array(
        'type' => 'text',	 
        'label' => 'Vertex SMB Customer Code',	 
        'input' => 'text',	 
        'visible' => TRUE,	 
        'required' => FALSE,	 
        'default_value' => '',	 
       /* 'adminhtml_only' => '1',*/
        'user_defined'      => true,
       /* 'used_in_forms' => array('adminhtml_customer')*/
    )); 
    $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_code');
    $attribute->setData('used_in_forms', array('adminhtml_customer'))->save();      
}
 
$this->endSetup();