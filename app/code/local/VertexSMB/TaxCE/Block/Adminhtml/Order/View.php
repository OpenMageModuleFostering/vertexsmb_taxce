<?php

class VertexSMB_TaxCE_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
    
     
    
    public function __construct()
    {
        parent::__construct();
        
        if (Mage::Helper('taxce')->IsVertexActive()) {
            $TotalInvoicedTax=Mage::getModel('taxce/taxRequest')->getTotalInvoicedTax( $this->getOrder()->getId());
            if ($TotalInvoicedTax || !Mage::helper('taxce')->ShowManualInvoiceButton())  
                return $this;
            $this->_addButton('taxce_invoice', array(
                   'label'     => Mage::helper('taxce')->__("VertexSMB Invoice"),
                   'onclick'   => 'setLocation(\'' . $this->getVertexInvoiceUrl() . '\')',
                   'class'     => 'go'  
               ));                 
            
        }
    }
    
    public function getVertexInvoiceUrl(){
        return $this->getUrl('*/taxce/invoicetax');
    }
    
}