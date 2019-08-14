<?php
/**
 * @package     VertexSMB_TaxCE
 * @copyright   Copyright (c) 2015 Net@Work (http://www.netatwork.com)
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */

class VertexSMB_TaxCE_Model_Resource_Taxrequest_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
       
     /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('taxce/taxrequest');
    }
    
    /**
     * Add request type filter to result     
     */
    public function setRequestTypeFilter($requestType)
    {
        return $this->addFieldToFilter('main_table.request_type', $requestType);
    }

}