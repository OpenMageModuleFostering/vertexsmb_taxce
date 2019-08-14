<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Model_TaxRequest extends Mage_Core_Model_Abstract {
    
    public function _construct()
    {
        $this->_init('taxce/taxRequest');
    }
    
    public function getTotalInvoicedTax($orderId){
        $TotalTax=0;
        $invoices=$this->getCollection()->addFieldToSelect('total_tax')->addFieldToFilter('order_id',$orderId)->addFieldToFilter('request_type','invoice');
        foreach ($invoices as $invoice) {
    
            $TotalTax+=$invoice->getTotalTax();
        }
        return $TotalTax;
    }
    
    public function RemoveQuotesLookupRequests(){
        $quotes=$this->getCollection()->addFieldToSelect('request_id')->addFieldToFilter('request_type',array('in'=>array('quote','tax_area_lookup')));
         
        foreach ($quotes as $quote)
            $quote->delete();
    
        return $this;
    }
    
    public function RemoveInvoicesforCompletedOrders(){
        $invoices=$this->getCollection()->addFieldToSelect('order_id')->addFieldToFilter('request_type','invoice');
         
        $invoices->getSelect()->join( array('order'=> 'sales_flat_order'), 'order.entity_id = main_table.order_id', array('order.state'));
        $invoices->addFieldToFilter('order.state',array('in'=>array('complete','canceled','closed')));
    
        $completed_order_ids=array();
        foreach ($invoices as $invoice)
            $completed_order_ids[]=$invoice->getOrderId();
    
        $completed_invoices=$this->getCollection()->addFieldToSelect('request_id')->addFieldToFilter('order_id',array('in'=>$completed_order_ids));
        foreach ($completed_invoices as $completeted_invoice)
            $completeted_invoice->delete();
    
    
        return $this;
    }    
}