<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @return boolean
     */
    public function isVertexSMBActive()
    {
        if (Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_ENABLE_VERTEX)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getLocationCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_LOCATION_CODE);
    }

    /**
     * @return string
     */
    public function getCompanyCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_COMPANY_CODE);
    }

    /**
     * @return string
     */
    public function getCompanyStreet1()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_STREET1);
    }

    /**
     * @return string
     */
    public function getCompanyStreet2()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_STREET2);
    }

    /**
     * @return string
     */
    public function getCompanyCity()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_CITY);
    }

    /**
     * @return string
     */
    public function getCompanyCountry()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_COUNTRY);
    }

    /**
     * @return string
     */
    public function getCompanyRegionId()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_REGION);
    }

    /**
     * @return string
     */
    public function getCompanyPostalCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_POSTAL_CODE);
    }

    /**
     * @return string
     */
    public function getShippingTaxClassId()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS);
    }

    /**
     * @return string
     */
    public function getTrustedId()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_TRANSACTION_TYPE;
    }

    /**
     * @return string
     */
    public function getVertexHost()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_API_HOST);
    }

    /**
     * @return string
     */
    public function getVertexAddressHost()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_ADDRESS_API_HOST);
    }

    /**
     * @return string
     */
    public function getDefaultCustomerCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE);
    }

    /**
     * @return string
     */
    public function getCreditmemoAdjustmentFeeCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE);
    }

    /**
     * @return string
     */
    public function getCreditmemoAdjustmentFeeClass()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS);
    }

    /**
     * @return string
     */
    public function getCreditmemoAdjustmentPositiveCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE);
    }

    /**
     * @return string
     */
    public function getCreditmemoAdjustmentPositiveClass()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS);
    }

    /**
     * @return string
     */
    public function allowCartQuote()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE);
    }

    /**
     * @return string
     */
    public function getGiftWrappingOrderClass()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ORDER_CLASS);
    }

    /**
     * @return string
     */
    public function getGiftWrappingOrderCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ORDER_CODE);
    }

    public function getGiftWrappingItemClass()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ITEM_CLASS);
    }

    /**
     * @return string
     */
    public function getGiftWrappingItemCodePrefix()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ITEM_CODE_PREFIX);
    }

    /**
     * @return string
     */
    public function getPrintedGiftcardClass()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_PRINTED_GIFTCARD_CLASS);
    }

    /**
     * @return string
     */
    public function getPrintedGiftcardCode()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_PRINTED_GIFTCARD_CODE);
    }

    /**
     * @return boolean
     */
    public function isAllowedQuote()
    {
        $quoteAllowedControllers = Mage::helper('taxce/config')->getQuoteAllowedControllers();
        if ($this->allowCartQuote()) {
            $quoteAllowedControllers[] = 'cart';
        }
        
        if (in_array(Mage::app()->getRequest()->getControllerName(), $quoteAllowedControllers)) {
            return true;
        }
        
        return false;
    }

    /**
     * @return string
     */
    public function showManualInvoiceButton()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON);
    }

    /**
     * Is Popup Window Allowed
     * @return string
     */
    public function showPopup()
    {
        return Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_SHOW_POPUP);
    }

    /**
     * @return boolean
     */
    public function requestByInvoiceCreation()
    {
        $vertexInvoiceEvent = Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER);
        if ($vertexInvoiceEvent == 'invoice_created') {
            return true;
        }
        return false;
    }

    /**
     * @param unknown $status
     * @return boolean
     */
    public function requestByOrderStatus($status)
    {
        $vertexInvoiceEvent = Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER);
        $vertexInvoiceOrderStatus = Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS);
        if ($vertexInvoiceEvent == 'order_status' && $vertexInvoiceOrderStatus == $status) {
            return true;
        }
        return false;
    }

    /**
     * @param unknown $classId
     * @return string
     */
    public function taxClassNameByClassId($classId)
    {
        if (! $classId) {
            $taxclassName = "None";
        } else {
            $taxclassName = Mage::getModel('tax/class')->load($classId)->getClassName();
        }
        return $taxclassName;
    }

    /**
     * @return Ambigous <string, unknown>
     */
    public function getSourcePath()
    {
        $controller = Mage::app()->getRequest()->getControllerName();
        $module = Mage::app()->getRequest()->getModuleName();
        $action = Mage::app()->getRequest()->getActionName();
        $sourcePath = "";
        if ($controller) {
            $sourcePath .= $controller;
        }
        if ($module) {
            $sourcePath .= "_" . $module;
        }
        if ($action) {
            $sourcePath .= "_" . $action;
        }
        
        return $sourcePath;
    }

    /**
     * @param int $groupId
     * @return string
     */
    public function taxClassNameByCustomerGroupId($groupId)
    {
        $classId = Mage::getModel('customer/group')->getTaxClassId($groupId);
        return $this->taxClassNameByClassId($classId);
    }

    /**
     * @param unknown $customerId
     * @return unknown
     */
    public function getCustomerCodeById($customerId)
    {
        $customerCode = $this->getDefaultCustomerCode();
        if ($customerId) {
            $customerCode = Mage::getModel('customer/customer')->load($customerId)->getCustomerCode();
        }
        
        if (empty($customerCode)) {
            $customerCode = $this->getDefaultCustomerCode();
        }
        
        return $customerCode;
    }

    /**
     * @return string
     */
    public function checkCredentials()
    {
        if (! $this->getVertexHost()) {
            return "Not Valid: Missing Api Url";
        }
        if (! $this->getVertexAddressHost()) {
            return "Not Valid: Missing Address Validation Api Url";
        }
        if (! $this->getTrustedId()) {
            return "Not Valid: Missing Trusted Id";
        }
        if (! $this->getCompanyRegionId()) {
            return "Not Valid: Missing Company State";
        }
        if (! $this->getCompanyCountry()) {
            return "Not Valid: Missing Company Country";
        }
        if (! $this->getCompanyStreet1()) {
            return "Not Valid: Missing Company Street Address";
        }
        if (! $this->getCompanyCity()) {
            return "Not Valid: Missing Company City";
        }
        if (! $this->getCompanyPostalCode()) {
            return "Not Valid: Missing Company Postal Code";
        }
        
        $regionId = $this->getCompanyRegionId();
        if (is_int($regionId)) {
            $regionModel = Mage::getModel('directory/region')->load($regionId);
            $companyState = $regionModel->getCode();
        } else {
            $companyState = $regionId;
        }
        
        $countryModel = Mage::getModel('directory/country')->load($this->getCompanyCountry());
        $countryName = $countryModel->getIso3Code();
        
        $address = new Varien_Object();
        $address->setStreet1($this->getCompanyStreet1());
        $address->setStreet2($this->getCompanyStreet2());
        $address->setCity($this->getCompanyCity());
        $address->setRegionCode($companyState);
        $address->setPostcode($this->getCompanyPostalCode());
        
        if ($countryName != 'USA') {
            return "Valid";
        }
        
        $requestResult = Mage::getModel('taxce/TaxAreaRequest')->prepareRequest($address)->taxAreaLookup();
        if ($requestResult instanceof Exception) {
            return "Address Validation Error: Please check settings";
        }
        return "Valid";
    }

    /**
     * Company Information
     *
     * @param array $data
     * @return unknown
     */
    public function addSellerInformation($data)
    {
        $regionId = $this->getCompanyRegionId();
        if (is_int($regionId)) {
            $regionModel = Mage::getModel('directory/region')->load($regionId);
            $companyState = $regionModel->getCode();
        } else {
            $companyState = $regionId;
        }
        
        $countryModel = Mage::getModel('directory/country')->load($this->getCompanyCountry());
        $countryName = $countryModel->getIso3Code();
        $data['location_code'] = $this->getLocationCode();
        $data['transaction_type'] = $this->getTransactionType();
        $data['company_id'] = $this->getCompanyCode();
        $data['company_street_1'] = $this->getCompanyStreet1();
        $data['company_street_2'] = $this->getCompanyStreet2();
        $data['company_city'] = $this->getCompanyCity();
        $data['company_state'] = $companyState;
        $data['company_postcode'] = $this->getCompanyPostalCode();
        $data['company_country'] = $countryName;
        $data['trusted_id'] = $this->getTrustedId();
        return $data;
    }

    /**
     *
     * @param array   $data
     * @param unknown $address
     * @return unknown
     */
    public function addAddressInformation($data, $address)
    {
        $data['customer_street1'] = $address->getStreet1();
        $data['customer_street2'] = $address->getStreet2();
        $data['customer_city'] = $address->getCity();
        $data['customer_region'] = $address->getRegionCode();
        $data['customer_postcode'] = $address->getPostcode();
        $countryModel = Mage::getModel('directory/country')->load($address->getCountryId());
        $countryName = $countryModel->getIso3Code();
        $data['customer_country'] = $countryName;
        $data['tax_area_id'] = $address->getTaxAreaId();
        return $data;
    }

    /**
     * @param unknown $originalEntity
     * @return boolean
     */
    public function isFirstOfPartial($originalEntity)
    {
        if ($originalEntity instanceof Mage_Sales_Model_Order_Invoice) {
            if (! $originalEntity->getShippingTaxAmount()) {
                return false;
            }
        }
        if ($this->requestByInvoiceCreation() && $originalEntity instanceof Mage_Sales_Model_Order && $originalEntity->getShippingInvoiced()) {
            return false;
        }
        if ($originalEntity instanceof Mage_Sales_Model_Order_Creditmemo) {
            if (! $originalEntity->getShippingAMount()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param unknown $info
     * @param unknown $creditmemoModel
     * @return Ambigous <multitype:number NULL , multitype:number NULL string >
     */
    public function addRefundAdjustments($info, $creditmemoModel)
    {
        if ($creditmemoModel->getAdjustmentPositive()) {
            $itemData = array();
            $itemData['product_class'] = $this->taxClassNameByClassId($this->getCreditmemoAdjustmentPositiveClass());
            $itemData['product_code'] = $this->getCreditmemoAdjustmentPositiveCode();
            $itemData['qty'] = 1;
            $itemData['price'] = - 1 * $creditmemoModel->getAdjustmentPositive();
            $itemData['extended_price'] = - 1 * $creditmemoModel->getAdjustmentPositive();
            $info[] = $itemData;
        }
        if ($creditmemoModel->getAdjustmentNegative()) {
            $itemData = array();
            $itemData['product_class'] = $this->taxClassNameByClassId($this->getCreditmemoAdjustmentFeeClass());
            $itemData['product_code'] = $this->getCreditmemoAdjustmentFeeCode();
            $itemData['qty'] = 1;
            $itemData['price'] = $creditmemoModel->getAdjustmentNegative();
            $itemData['extended_price'] = $creditmemoModel->getAdjustmentNegative();
            $info[] = $itemData;
        }
        return $info;
    }

    /**
     * @param unknown $orderAddress
     * @param string  $originalEntity
     * @param string  $event
     * @return multitype:|multitype:number NULL string
     */
    public function addOrderGiftWrap($orderAddress, $originalEntity = null, $event = null)
    {
        $itemData = array();
        if (! $this->isFirstOfPartial($originalEntity)) {
            return $itemData;
        }
        
        $itemData['product_class'] = $this->taxClassNameByClassId($this->getGiftWrappingOrderClass());
        $itemData['product_code'] = $this->getGiftWrappingOrderCode();
        $itemData['qty'] = 1;
        $itemData['price'] = $orderAddress->getGwPrice();
        $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];
        
        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = - 1 * $itemData['price'];
            $itemData['extended_price'] = - 1 * $itemData['extended_price'];
        }
        
        return $itemData;
    }

    /**
     * @param unknown $orderAddress
     * @param string  $originalEntity
     * @param string  $event
     * @return multitype:|multitype:number NULL string
     */
    public function addOrderPrintCard($orderAddress, $originalEntity = null, $event = null)
    {
        $itemData = array();
        if (! $this->isFirstOfPartial($originalEntity)) {
            return $itemData;
        }
        $itemData['product_class'] = $this->taxClassNameByClassId($this->getPrintedGiftcardClass());
        $itemData['product_code'] = $this->getPrintedGiftcardCode();
        $itemData['qty'] = 1;
        $itemData['price'] = $orderAddress->getGwCardPrice();
        $itemData['extended_price'] = $orderAddress->getGwCardPrice();
        
        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = - 1 * $itemData['price'];
            $itemData['extended_price'] = - 1 * $itemData['extended_price'];
        }
        
        return $itemData;
    }

    /**
     * @param unknown $orderAddress
     * @param string  $originalEntity
     * @param string  $event
     * @return multitype:number Ambigous <number> Ambigous <number, Ambigous <number>> NULL string
     */
    public function addShippingInfo($orderAddress, $originalEntity = null, $event = null)
    {
        $itemData = array();
        if ($orderAddress->getShippingMethod() && $this->isFirstOfPartial($originalEntity)) {
            $itemData['product_class'] = $this->taxClassNameByClassId($this->getShippingTaxClassId());
            $itemData['product_code'] = $orderAddress->getShippingMethod();
            $itemData['price'] = $orderAddress->getShippingAmount() - $orderAddress->getShippingDiscountAmount();
            $itemData['qty'] = 1;
            $itemData['extended_price'] = $itemData['price'];
            
            if ($originalEntity instanceof Mage_Sales_Model_Order_Creditmemo) {
                $itemData['price'] = $originalEntity->getShippingAmount();
                $itemData['extended_price'] = $itemData['price'];
            }
            if ($event == 'cancel' || $event == 'refund') {
                $itemData['price'] = - 1 * $itemData['price'];
                $itemData['extended_price'] = - 1 * $itemData['extended_price'];
            }
        }
        return $itemData;
    }

    /**
     * @param unknown $address
     * @return unknown
     */
    public function taxQuoteItems($address)
    {
        $informationArray = Mage::getModel('taxce/taxQuote')->collectQuotedata($address);
        $information = new Varien_Object($informationArray);
        $information->setTaxAreaId();
        $taxedItemsInfo = Mage::getModel('taxce/taxQuote')->getTaxQuote($informationArray);
        return $taxedItemsInfo;
    }

    /**
     * @return boolean
     */
    public function canQuoteTax()
    {
        if (! $this->isAllowedQuote()) {
            return false;
        }
        if ($this->getSourcePath() == 'onepage_checkout_index') {
            return false;
        }
        return true;
    }

    /**
     * Common function for item preparation
     *
     * @uses Always send discounted. Discount on TotalRowAmount
     * @param unknown $item
     * @param string  $type
     * @param string  $originalEntityItem
     * @param string  $event
     * @return multitype:number NULL unknown string
     */
    public function prepareItem($item, $type = 'ordered', $originalEntityItem = null, $event = null)
    {
        $itemData = array();
        
        $itemData['product_class'] = $this->taxClassNameByClassId(
            $item->getProduct()
                ->getTaxClassId()
        );
        $itemData['product_code'] = $item->getSku();
        $itemData['item_id'] = $item->getId();
        
        if ($type == 'invoiced') {
            $price = $originalEntityItem->getPrice();
        } else {
            $price = $item->getPrice();
        }
        
        $itemData['price'] = $price;
        if ($type == 'ordered' && $this->requestByInvoiceCreation()) {
            $itemData['qty'] = $item->getQtyOrdered() - $item->getQtyInvoiced();
        } elseif ($type == 'ordered')
            $itemData['qty'] = $item->getQtyOrdered();
        elseif ($type == 'invoiced')
            $itemData['qty'] = $originalEntityItem->getQty();
        elseif ($type == 'quote')
            $itemData['qty'] = $item->getQty();
        
        if ($type == 'invoiced') {
            $itemData['extended_price'] = $originalEntityItem->getRowTotal() - $originalEntityItem->getDiscountAmount();
        } elseif ($type == 'ordered' && $this->requestByInvoiceCreation()) {
                $itemData['extended_price'] = $item->getRowTotal() - $item->getRowInvoiced() - $item->getDiscountAmount() + $item->getDiscountInvoiced();
        } else {
            $itemData['extended_price'] = $item->getRowTotal() - $item->getDiscountAmount();
        }
        
        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = - 1 * $itemData['price'];
            $itemData['extended_price'] = - 1 * $itemData['extended_price'];
        }
        return $itemData;
    }

    /**
     *
     * @param unknown $item
     * @param string  $type
     * @param string  $originalEntityItem
     * @param string  $event
     * @return multitype:string number NULL unknown
     */
    public function prepareGiftWrapItem($item, $type = 'ordered', $originalEntityItem = null, $event = null)
    {
        $itemData = array();
        
        $itemData['product_class'] = $this->taxClassNameByClassId($this->getGiftWrappingItemClass());
        $itemData['product_code'] = $this->getGiftWrappingItemCodePrefix() . '-' . $item->getSku();
        
        if ($type == 'invoiced') {
            $price = $item->getGwPriceInvoiced();
        } else {
            $price = $item->getGwPrice();
        }
        
        $itemData['price'] = $price;
        if ($type == 'ordered' && $this->requestByInvoiceCreation()) {
            $itemData['qty'] = $item->getQtyOrdered() - $item->getQtyInvoiced();
        } elseif ($type == 'ordered')
            $itemData['qty'] = $item->getQtyOrdered();
        elseif ($type == 'invoiced')
            $itemData['qty'] = $originalEntityItem->getQty();
        elseif ($type == 'quote')
            $itemData['qty'] = $item->getQty();
        
        if ($type == 'invoiced') {
            $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];
        } elseif ($type == 'ordered' && $this->requestByInvoiceCreation()) {
                $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];
        } else {
            $itemData['extended_price'] = $itemData['qty'] * ($item->getGwPrice());
        }
        
        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = - 1 * $itemData['price'];
            $itemData['extended_price'] = - 1 * $itemData['extended_price'];
        }
        
        return $itemData;
    }

    /**
     * @return Mage_Checkout_Model_Session | Mage_Adminhtml_Model_Session_Quote
     */
    public function getSession()
    {
        if (Mage::app()->getRequest()->getControllerName() != 'sales_order_create') {
            return Mage::getSingleton('checkout/session');
        } else {
            return Mage::getSingleton('adminhtml/session_quote');
        }
    }
}
