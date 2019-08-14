<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_TaxInvoice extends Mage_Core_Model_Abstract
{

    /**
     *
     */
    public function _construct()
    {
        $this->_init('taxce/taxinvoice');
    }

    /**
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('taxce');
    }

 
    /**
     * @param unknown $entityItem
     * @param string  $event
     * @return unknown
     */
    public function prepareInvoiceData($entityItem, $event = null)
    {
        $info = array();
        $info = $this->getHelper()->addSellerInformation($info);
        
        if ($entityItem instanceof Mage_Sales_Model_Order) {
            $order = $entityItem;
        } elseif ($entityItem instanceof Mage_Sales_Model_Order_Invoice) {
            $order = $entityItem->getOrder();
        } elseif ($entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
            $order = $entityItem->getOrder();
        }
        $info['order_id'] = $order->getIncrementId();
        $info['document_number'] = $order->getIncrementId();
        $info['document_date'] = date("Y-m-d", strtotime($order->getCreatedAt()));
        $info['posting_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
        
        $customerClass = $this->getHelper()->taxClassNameByCustomerGroupId($order->getCustomerGroupId());
        $customerCode = $this->getHelper()->getCustomerCodeById($order->getCustomerId());
        
        $info['customer_class'] = $customerClass;
        $info['customer_code'] = $customerCode;
        
        if ($order->getIsVirtual()) {
            $address = $order->getBillingAddress();
        } else {
            $address = $order->getShippingAddress();
        }
        
        $info = $this->getHelper()->addAddressInformation($info, $address);
 
        $orderItems = array();
        $orderedItems = $entityItem->getAllItems();
        
        foreach ($orderedItems as $item) {
            $originalItem = $item;
            if ($entityItem instanceof Mage_Sales_Model_Order_Invoice) {
                $item = $item->getOrderItem();
            } elseif ($entityItem instanceof Mage_Sales_Model_Order_Creditmemo)
                $item = $item->getOrderItem();
            
            if ($item->getParentItem()) {
                continue;
            }
            
            if ($item->getHasChildren() && $item->getProduct()->getPriceType() !== null && (int) $item->getProduct()->getPriceType() === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                foreach ($item->getChildrenItems() as $child) {
                    if ($entityItem instanceof Mage_Sales_Model_Order_Invoice || $entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
                        $orderItems[] = $this->getHelper()->prepareItem($child, 'invoiced', $originalItem, $event);
                        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') && $child->getGwId()) {
                            $orderItems[] = $this->getHelper()->prepareGiftWrapItem($child, 'invoiced', $originalItem, $event);
                        }
                    } elseif ($entityItem instanceof Mage_Sales_Model_Order) {
                        $orderItems[] = $this->getHelper()->prepareItem($child, 'ordered', $originalItem, $event);
                        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') && $child->getGwId()) {
                            $orderItems[] = $this->getHelper()->prepareGiftWrapItem($child, 'ordered', $originalItem, $event);
                        }
                    }
                }
            } else {
                if ($entityItem instanceof Mage_Sales_Model_Order_Invoice || $entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
                    $orderItems[] = $this->getHelper()->prepareItem($item, 'invoiced', $originalItem, $event);
                    if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') && $item->getGwId()) {
                        $orderItems[] = $this->getHelper()->prepareGiftWrapItem($item, 'invoiced', $originalItem, $event);
                    }
                } elseif ($entityItem instanceof Mage_Sales_Model_Order) {
                    $orderItems[] = $this->getHelper()->prepareItem($item, 'ordered', $originalItem, $event);
                    if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') && $item->getGwId()) {
                        $orderItems[] = $this->getHelper()->prepareGiftWrapItem($item, 'ordered', $originalItem, $event);
                    }
                }
            }
        }
        
        if (! $order->getIsVirtual() && count($this->getHelper()->addShippingInfo($order, $entityItem, $event))) {
            $orderItems[] = $this->getHelper()->addShippingInfo($order, $entityItem, $event);
        }
        
        if ($entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
            $orderItems = $this->getHelper()->addRefundAdjustments($orderItems, $entityItem);
        }
        
        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true')) {
            if (count($this->getHelper()->addOrderGiftWrap($order, $entityItem, $event))) {
                $orderItems[] = $this->getHelper()->addOrderGiftWrap($order, $entityItem, $event);
            }
            if (count($this->getHelper()->addOrderPrintCard($order, $entityItem, $event))) {
                $orderItems[] = $this->getHelper()->addOrderPrintCard($order, $entityItem, $event);
            }
        }
 
        $info['request_type'] = 'InvoiceRequest';
        $info['order_items'] = $orderItems;
        $request = Mage::getModel('taxce/requestItem')->setData($info)->exportAsArray();
 
        return $request;
    }
 
    /**
     * @param unknown $data
     * @param string  $order
     * @return boolean
     */
    public function sendInvoiceRequest($data, $order = null)
    {
        if ($order == null) {
            $order = Mage::registry('current_order');
        }
        
        $requestResult = Mage::getModel('taxce/vertexSMB')->sendApiRequest($data, 'invoice', $order);
        if ($requestResult instanceof Exception) {
            Mage::log("Invoice Request Error: " . $requestResult->getMessage(), null, 'vertexsmb.log');
            Mage::getSingleton('adminhtml/session')->addError($requestResult->getMessage());
            return false;
        }
        
        $order->addStatusHistoryComment('Vertex SMB Invoice sent successfully. Amount: $' . $requestResult->InvoiceResponse->TotalTax->_, false)->save();
        return true;
    }
 
    /**
     * @param unknown $data
     * @param string  $order
     * @return boolean
     */
    public function sendRefundRequest($data, $order = null)
    {
        if ($order == null) {
            $order = Mage::registry('current_order');
        }
        $requestResult = Mage::getModel('taxce/vertexSMB')->sendApiRequest($data, 'invoice_refund', $order);
        if ($requestResult instanceof Exception) {
            Mage::log("Refund Request Error: " . $requestResult->getMessage(), null, 'vertexsmb.log');
            Mage::getSingleton('adminhtml/session')->addError($requestResult->getMessage());
            return false;
        }
        $order->addStatusHistoryComment('Vertex SMB Invoice refunded successfully. Amount: $' . $requestResult->InvoiceResponse->TotalTax->_, false)->save();
        return true;
    }
}
