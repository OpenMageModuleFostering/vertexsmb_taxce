<?php
 
class VertexSMB_TaxCE_Model_Resource_TaxRequest_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
       
     /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('taxce/taxRequest');
    }
    
    /**
     * Add request type filter to result
     *
     */
    public function setRequestTypeFilter($requestType)
    {
        return $this->addFieldToFilter('main_table.request_type', $requestType);
    }

}