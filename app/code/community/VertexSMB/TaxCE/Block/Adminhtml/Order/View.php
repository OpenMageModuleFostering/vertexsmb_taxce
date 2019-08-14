<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{

    /**
     * @return VertexSMB_TaxCE_Block_Adminhtml_Order_View
     */
    public function __construct()
    {
        parent::__construct();
        
        if (Mage::Helper('taxce')->isVertexSMBActive()) {
            $totalInvoicedTax = Mage::getModel('taxce/taxRequest')->getTotalInvoicedTax(
                $this->getOrder()
                    ->getId()
            );
            if ($totalInvoicedTax || ! Mage::helper('taxce')->showManualInvoiceButton()) {
                return $this;
            }
            $this->_addButton(
                'vertex_invoice',
                array(
                'label' => Mage::helper('taxce')->__("Vertex SMB Invoice"),
                'onclick' => 'setLocation(\'' . $this->getVertexInvoiceUrl() . '\')',
                'class' => 'go'
                )
            );
        }
    }

    /**
     * Vertex Invoice Url
     * @return string
     */
    public function getVertexInvoiceUrl()
    {
        return $this->getUrl('*/vertexSMB/invoicetax');
    }
}
