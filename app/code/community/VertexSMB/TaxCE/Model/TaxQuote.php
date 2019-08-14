<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_TaxQuote extends Mage_Core_Model_Abstract
{

    /**
     *
     */
    public function _construct()
    {
        $this->_init('taxce/taxquote');
    }

    /**
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('taxce');
    }

    /**
     *
     * @param unknown $information
     * @return boolean|unknown
     */
    public function getTaxQuote($information)
    {
        Mage::log("VertexSMB_TaxCE_Model_TaxQuote::getTaxQuote", null, 'vertexsmb.log');
        
        if ($this->getHelper()->getSourcePath() == 'cart_checkout_index' || $this->getHelper()->getSourcePath() == 'cart_checkout_couponPost') {
            $information['tax_area_id'] = '';
            $information['customer_street1'] = '';
            $information['customer_street2'] = '';
        }
        
        $information['request_type'] = 'QuotationRequest';
        $request = Mage::getModel('taxce/requestItem')->setData($information)->exportAsArray();
        
        $taxQuoteResult = Mage::getModel('taxce/vertexSMB')->sendApiRequest($request, 'quote');
        if ($taxQuoteResult instanceof Exception) {
            if (Mage::app()->getRequest()->getControllerName() == 'onepage' || Mage::app()->getRequest()->getControllerName() == 'sales_order_create') {
                Mage::log("Quote Request Error: " . $taxQuoteResult->getMessage() . "Controller:  " . $this->getHelper()->getSourcePath(), null, 'vertexsmb.log');
                $result = array(
                    'error' => 1,
                    'message' => "Tax calculation request error. Please check your address"
                );
                Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                $action = Mage::app()->getRequest()->getActionName();
                Mage::log("Controller action to dispatch " . $action, null, 'vertexsmb.log');
                Mage::app()->getFrontController()
                    ->getAction()
                    ->setFlag($action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                return false;
            }
            if ($this->getHelper()->getSourcePath() == 'cart_checkout_index' || $this->getHelper()->getSourcePath() == 'cart_checkout_couponPost') {
                $this->getHelper()
                    ->getSession()
                    ->addError(Mage::helper('core')->escapeHtml("Tax Calculation Request Error. Please check your address"));
            }
            
            return false;
        }
        
        $responseModel = Mage::getModel('taxce/TaxQuoteResponse')->parseResponse($taxQuoteResult);
        $this->setResponse($responseModel);
        
        $itemsTax = $responseModel->getTaxLineItems();
        $quoteTaxedItems = $responseModel->getQuoteTaxedItems();
        
        return $quoteTaxedItems;
    }

    /**
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return unknown
     */
    public function collectQuotedata(Mage_Sales_Model_Quote_Address $address)
    {
        $information = array();
        $information = $this->getHelper()->addSellerInformation($information);
        
        $customerClassName = $this->getHelper()->taxClassNameByClassId(
            $address->getQuote()
                ->getCustomerTaxClassId()
        );
        $customerCode = $this->getHelper()->getCustomerCodeById(
            $address->getQuote()
                ->getCustomer()
                ->getId()
        );
        
        $information['customer_code'] = $customerCode;
        $information['customer_class'] = $customerClassName;
        
        $information = $this->getHelper()->addAddressInformation($information, $address);
        
        $information['store_id'] = $address->getQuote()
            ->getStore()
            ->getId();
        $date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
        $information['posting_date'] = $date;
        $information['document_date'] = $date;
        
        $information['order_items'] = array();
        $items = $address->getAllNonNominalItems();
        foreach ($items as $item) {
            if (Mage::getConfig()->getModuleConfig('Enterprise_CatalogPermissions') && Mage::getConfig()->getModuleConfig('Enterprise_CatalogPermissions')->is('active', 'true')) {
                if ($item->getDisableAddToCart() && ! $item->isDeleted()) {
                    continue;
                }
            }
            
            if ($item->getParentItem()) {
                continue;
            }
            
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $information['order_items'][$child->getId()] = $this->getHelper()->prepareItem($child, 'quote');
                    if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') && $child->getGwId()) {
                        $information['order_items']['gift_wrap_' . $child->getId()] = $this->getHelper()->prepareGiftWrapItem($child, 'quote');
                    }
                }
            } else {
                $information['order_items'][$item->getId()] = $this->getHelper()->prepareItem($item, 'quote');
                if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') && $item->getGwId()) {
                    $information['order_items']['gift_wrap_' . $item->getId()] = $this->getHelper()->prepareGiftWrapItem($item, 'quote');
                }
            }
        }
        if (count($this->getHelper()->addShippingInfo($address))) {
            $information['order_items']['shipping'] = $this->getHelper()->addShippingInfo($address);
        }
        
        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') && Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true')) {
            if ($address->getGwPrice()) {
                $information['order_items']['gift_wrapping'] = $this->getHelper()->addOrderGiftWrap($address);
            }
            if ($address->getGwCardPrice()) {
                $information['order_items']['gift_print_card'] = $this->getHelper()->addOrderPrintCard($address);
            }
        }
        
        return $information;
    }
}
