<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
$installer = $this;
$installer->startSetup();

if ($installer->getConnection()->isTableExists($installer->getTable('taxce/taxrequest')) == true) {
    
    $installer->getConnection()->addColumn($installer->getTable('taxce/taxrequest'),
        'source_path', 
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => null,
            'length' => 255,
            'comment' => 'Source path controller_module_action'
        )
    );
  
}
  
$this->endSetup();