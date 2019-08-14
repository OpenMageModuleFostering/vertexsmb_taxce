<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 **/ 
 
class VertexSMB_TaxCE_Model_Observer {
    
    /**
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function InvoiceCreated(Varien_Event_Observer $observer) {
       if (!$this->_getHelper()->IsVertexSMBActive() || !$this->_getHelper()->RequestbyInvoiceCreation())           
            return $this;              
        
        /* @var $order Magento_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $invoice_request_data=Mage::getModel('taxce/taxInvoice')->PrepareInvoiceData($invoice,'invoice'); 
               
       if ($invoice_request_data && Mage::getModel('taxce/taxInvoice')->SendInvoiceRequest($invoice_request_data,$invoice->getOrder())) 
               $this->_getSession()->addSuccess( $this->_getHelper()->__('The Vertex SMB invoice has been sent.'));                   
            
        return $this;
    }
    
    /**
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function OrderSaved(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if (!$this->_getHelper()->IsVertexSMBActive() || !$this->_getHelper()->RequestbyOrderStatus($order->getStatus()))           
            return $this;             
        
        $invoice_request_data=Mage::getModel('taxce/taxInvoice')->PrepareInvoiceData($order); 
        if ($invoice_request_data && Mage::getModel('taxce/taxInvoice')->SendInvoiceRequest($invoice_request_data,$order)) 
            $this->_getSession()->addSuccess( $this->_getHelper()->__('The Vertex SMB invoice has been sent.'));                   
        return $this;
    }
    
    /**
     * @deprecated 
     * @param Varien_Event_Observer $observer     
     * @return VertexSMB_TaxCE_Model_Observer
     */
    
    public function OrderCancelled(Varien_Event_Observer $observer) {
       /* Commented upon new logic
        * $order = $observer->getEvent()->getOrder();
        $invoiced_tax= Mage::getModel('vertex/taxrequest')->getTotalInvoicedTax($order->getId());
        if (!$this->_getHelper()->IsVertexSMBActive() || !$invoiced_tax)
             return $this;
         $cancel_request_data=Mage::getModel('vertex/taxInvoice')->PrepareInvoiceData($order,'cancel'); 
         if ($cancel_request_data && Mage::getModel('vertex/taxInvoice')->SendCancelRequest($cancel_request_data,$order)) 
            $this->_getSession()->addSuccess( $this->_getHelper()->__('The Vertex invoice has been canceled.'));          
         */
         return $this;
    }
    
    /**
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function OrderCreditmemoRefund(Varien_Event_Observer $observer){       
        $credit_memo= $observer->getCreditmemo();
        $order=$credit_memo->getOrder();
        $invoiced_tax= Mage::getModel('taxce/taxRequest')->getTotalInvoicedTax($order->getId());
         if (!$this->_getHelper()->IsVertexSMBActive() || !$invoiced_tax)
             return $this;
        $creditmemo_request_data=Mage::getModel('taxce/taxInvoice')->PrepareInvoiceData($credit_memo,'refund');
        if ($creditmemo_request_data && Mage::getModel('taxce/taxInvoice')->SendRefundRequest($creditmemo_request_data,$order)) 
                $this->_getSession()->addSuccess( $this->_getHelper()->__('The Vertex SMB invoice has been refunded.'));       
        
        return $this;        
    }
    
    /**
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function changeSystemConfig(Varien_Event_Observer $observer) {
         $config = $observer->getConfig();
         $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_website=0;
         $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_default=0;
         $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_store=0;
         
         $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_website=0;
         $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_default=0;
         $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_store=0;        
         
         $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_website=0;
         $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_default=0;
         $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_store=0;        
                  
         $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_website=0;
         $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_default=0;
         $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_store=0;          
         
         $config->getNode('sections/tax/groups/weee')->show_in_website=0;
         $config->getNode('sections/tax/groups/weee')->show_in_default=0;
         $config->getNode('sections/tax/groups/weee')->show_in_store=0;    
         
         $config->getNode('sections/tax/groups/defaults')->show_in_website=0;
         $config->getNode('sections/tax/groups/defaults')->show_in_default=0;
         $config->getNode('sections/tax/groups/defaults')->show_in_store=0;   
 
         if (!Mage::getConfig ()->getModuleConfig ( 'Enterprise_Enterprise' )
                     || !Mage::getConfig ()->getModuleConfig ( 'Enterprise_Enterprise' )->is('active', 'true')) {

         $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_website=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_default=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_store=0;  
         
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_website=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_default=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_store=0;  
         
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_website=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_default=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_store=0;  
         
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_website=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_default=0;
         $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_store=0;  
         
         $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_website=0;
         $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_default=0;
         $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_store=0;  
         
         $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_website=0;
         $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_default=0;
         $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_store=0;           
         
         }
                                      
         return $this;
    }
    
    /**
     * @param unknown $schedule
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function CleanLogs($schedule){
       $request_model = Mage::getModel('taxce/taxRequest');
       $request_model->RemoveQuotesLookupRequests();
       $request_model->RemoveInvoicesforCompletedOrders();
       return $this;
    }
    
    /**
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function _getHelper() {
        return Mage::helper('taxce');
    }           
    
     /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }      
}
