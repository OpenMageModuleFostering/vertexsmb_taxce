<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 **/
 
class VertexSMB_TaxCE_Model_Source_Taxinvoice {
     protected $_options;
     
    public function toOptionArray($isMultiselect=false)
    {
      if (!$this->_options) {         
          $this->_options[]=array('label'=>"When Invoice Created", 'value'=>'invoice_created');
          $this->_options[]=array('label'=>"When Order Status Is", 'value'=>'order_status');
      }            
      $options = $this->_options;
      return $options;
    }
}