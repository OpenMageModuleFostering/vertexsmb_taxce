<?php
 
class VertexSMB_TaxCE_Model_Resource_TaxRequest extends Mage_Core_Model_Resource_Db_Abstract {
       
  /**
     * Resource initialization
     *
     */
    public function _construct()
    {
        $this->_init('taxce/taxrequest', 'request_id');
    }
    
}