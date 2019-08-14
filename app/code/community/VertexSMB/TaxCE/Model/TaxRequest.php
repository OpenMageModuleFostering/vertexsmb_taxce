<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_TaxRequest extends Mage_Core_Model_Abstract
{

    /**
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function _construct()
    {
        $this->_init('taxce/taxRequest');
    }

    /**
     * @param int $orderId
     * @return number
     */
    public function getTotalInvoicedTax($orderId)
    {
        $totalTax = 0;
        $invoices = $this->getCollection()
            ->addFieldToSelect('total_tax')
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('request_type', 'invoice');
        foreach ($invoices as $invoice) {
            $totalTax += $invoice->getTotalTax();
        }
        return $totalTax;
    }

    /**
     * @return VertexSMB_TaxCE_Model_TaxRequest
     */
    public function removeQuotesLookupRequests()
    {
        $quotes = $this->getCollection()
            ->addFieldToSelect('request_id')
            ->addFieldToFilter(
                'request_type',
                array(
                'in' => array(
                'quote',
                'tax_area_lookup'
                )
                )
            );
        
        foreach ($quotes as $quote) {
            $quote->delete();
        }
        
        return $this;
    }

    /**
     * @return VertexSMB_TaxCE_Model_TaxRequest
     */
    public function removeInvoicesforCompletedOrders()
    {
        $invoices = $this->getCollection()
            ->addFieldToSelect('order_id')
            ->addFieldToFilter('request_type', 'invoice');
        
        $invoices->getSelect()->join(
            array(
            'order' => 'sales_flat_order'
            ),
            'order.entity_id = main_table.order_id',
            array(
            'order.state'
            )
        );
        $invoices->addFieldToFilter(
            'order.state',
            array(
            'in' => array(
                'complete',
                'canceled',
                'closed'
            )
            )
        );
        
        $completedOrderIds = array();
        foreach ($invoices as $invoice) {
            $completedOrderIds[] = $invoice->getOrderId();
        }
        
        $completedInvoices = $this->getCollection()
            ->addFieldToSelect('request_id')
            ->addFieldToFilter(
                'order_id',
                array(
                'in' => $completedOrderIds
                )
            );
        foreach ($completedInvoices as $completedInvoice) {
            $completedInvoice->delete();
        }
        
        return $this;
    }
}
