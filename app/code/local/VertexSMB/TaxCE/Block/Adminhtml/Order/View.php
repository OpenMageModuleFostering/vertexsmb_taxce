<?php 
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
 
    public function __construct()
    {
        parent::__construct();
        
        if (Mage::Helper('taxce')->IsVertexSMBActive()) {
            $TotalInvoicedTax=Mage::getModel('taxce/taxRequest')->getTotalInvoicedTax( $this->getOrder()->getId());
            if ($TotalInvoicedTax || !Mage::helper('taxce')->ShowManualInvoiceButton())  
                return $this;
            $this->_addButton('vertex_invoice', array(
                   'label'     => Mage::helper('taxce')->__("Vertex SMB Invoice"),
                   'onclick'   => 'setLocation(\'' . $this->getVertexInvoiceUrl() . '\')',
                   'class'     => 'go'  
               ));                             
        }
    }
    
    public function getVertexInvoiceUrl(){
        return $this->getUrl('*/vertexSMB/invoicetax');
    }
    
}