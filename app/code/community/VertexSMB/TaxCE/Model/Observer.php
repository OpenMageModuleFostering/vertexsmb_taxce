<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_Observer
{

    /**
     *
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function invoiceCreated(Varien_Event_Observer $observer)
    {
        if (! $this->_getHelper()->isVertexSMBActive() || ! $this->_getHelper()->requestByInvoiceCreation()) {
            return $this;
        }
 
        $invoice = $observer->getEvent()->getInvoice();
        $invoiceRequestData = Mage::getModel('taxce/taxInvoice')->prepareInvoiceData($invoice, 'invoice');
        
        if ($invoiceRequestData && Mage::getModel('taxce/taxInvoice')->sendInvoiceRequest($invoiceRequestData, $invoice->getOrder())) {
            $this->_getSession()->addSuccess(
                $this->_getHelper()
                    ->__('The Vertex SMB invoice has been sent.')
            );
        }
        
        return $this;
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function orderSaved(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (! $this->_getHelper()->isVertexSMBActive() || ! $this->_getHelper()->requestByOrderStatus($order->getStatus())) {
            return $this;
        }
        
        $invoiceRequestData = Mage::getModel('taxce/taxInvoice')->prepareInvoiceData($order);
        if ($invoiceRequestData && Mage::getModel('taxce/taxInvoice')->sendInvoiceRequest($invoiceRequestData, $order)) {
            $this->_getSession()->addSuccess(
                $this->_getHelper()
                    ->__('The Vertex SMB invoice has been sent.')
            );
        }
        return $this;
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function orderCreditmemoRefund(Varien_Event_Observer $observer)
    {
        $creditMemo = $observer->getCreditmemo();
        $order = $creditMemo->getOrder();
        $invoicedTax = Mage::getModel('taxce/taxRequest')->getTotalInvoicedTax($order->getId());
        if (! $this->_getHelper()->isVertexSMBActive() || ! $invoicedTax) {
            return $this;
        }
        $creditmemoRequestData = Mage::getModel('taxce/taxInvoice')->prepareInvoiceData($creditMemo, 'refund');
        if ($creditmemoRequestData && Mage::getModel('taxce/taxInvoice')->sendRefundRequest($creditmemoRequestData, $order)) {
            $this->_getSession()->addSuccess(
                $this->_getHelper()
                    ->__('The Vertex SMB invoice has been refunded.')
            );
        }
        
        return $this;
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function changeSystemConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getConfig();
        $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_store = 0;
        
        $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_store = 0;
        
        $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_store = 0;
        
        $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_store = 0;
        
        $config->getNode('sections/tax/groups/weee')->show_in_website = 0;
        $config->getNode('sections/tax/groups/weee')->show_in_default = 0;
        $config->getNode('sections/tax/groups/weee')->show_in_store = 0;
        
        $config->getNode('sections/tax/groups/defaults')->show_in_website = 0;
        $config->getNode('sections/tax/groups/defaults')->show_in_default = 0;
        $config->getNode('sections/tax/groups/defaults')->show_in_store = 0;
        
        if (! Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') || ! Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')->is('active', 'true')) {
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_store = 0;
            
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_store = 0;
            
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_store = 0;
            
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_store = 0;
            
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_store = 0;
            
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_store = 0;
        }
        
        return $this;
    }

    /**
     *
     * @param unknown $schedule
     * @return VertexSMB_TaxCE_Model_Observer
     */
    public function cleanLogs($schedule)
    {
        $requestModel = Mage::getModel('taxce/taxRequest');
        $requestModel->removeQuotesLookupRequests();
        $requestModel->removeInvoicesforCompletedOrders();
        return $this;
    }

    /**
     *
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function _getHelper()
    {
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
