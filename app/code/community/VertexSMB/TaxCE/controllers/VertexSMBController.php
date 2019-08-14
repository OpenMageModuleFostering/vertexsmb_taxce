<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_VertexSMBController extends Mage_Adminhtml_Controller_Action
{

    /**
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('VertexSMB_TaxCE');
    }

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/view');
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);
        
        if (! $order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     *
     */
    public function invoiceTaxAction()
    {
        if ($order = $this->_initOrder()) {
            $invoiceRequestData = Mage::getModel('taxce/taxInvoice')->PrepareInvoiceData($order);
            if ($invoiceRequestData && Mage::getModel('taxce/taxInvoice')->SendInvoiceRequest($invoiceRequestData)) {
                $this->_getSession()->addSuccess($this->__('The Vertex SMB invoice has been sent.'));
            }
        }
        $this->_redirect(
            '*/sales_order/view',
            array(
            'order_id' => $order->getId()
            )
        );
    }

    /**
     * @return boolean
     */
    public function taxAreaAction()
    {
        $orderCreateModel = Mage::getSingleton('adminhtml/sales_order_create');
        $addressChanged = false;
        
        if ($orderCreateModel->getQuote()->isVirtual() || $orderCreateModel->getQuote()
            ->getShippingAddress()
            ->getSameAsBilling()) {
            $address = $orderCreateModel->getQuote()->getBillingAddress();
        } else {
            $address = $orderCreateModel->getQuote()->getShippingAddress();
        }
        
        if (! $address->getStreet1() || ! $address->getCity() || ! $address->getRegion() || ! $address->getPostcode()) {
            $result['message'] = 'address_not_complete';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        if ($address->getCountryId() !== 'US') {
            $result['message'] = 'not_usa_address';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        $taxAreaModel = Mage::getModel('taxce/TaxAreaRequest');
        $requestResult = $taxAreaModel->prepareRequest($address)->taxAreaLookup();
        if ($requestResult instanceof Exception) {
            Mage::log("Admin Tax Area Lookup Error: " . $requestResult->getMessage(), null, 'vertexsmb.log');
            $result['message'] = $requestResult->getMessage();
            $result['error'] = 1;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        $taxAreaResposeModel = $taxAreaModel->getResponse();
        
        if ($taxAreaResposeModel->getResultsCount() > 1 && Mage::helper('taxce')->showPopup()) {
            $taxAreaInfoCollection = $taxAreaResposeModel->getTaxAreaLocationsCollection();
            
            $block = Mage::app()->getLayout()
                ->createBlock('page/html')
                ->setData('is_multiple', 1)
                ->setTemplate('vertexsmb/popup-content.phtml')
                ->setData('items_collection', $taxAreaInfoCollection)
                ->toHtml();
            $result['message'] = "show_popup";
            $result['html'] = $block;
        } else {
            $firstTaxArea = $taxAreaResposeModel->getFirstTaxAreaInfo();
            $result['message'] = 'tax_area_id_found';
            if (strtolower($address->getCity()) != strtolower($firstTaxArea->getTaxAreaCity())) {
                $addressChanged = true;
                $blockAddressUpdate = Mage::app()->getLayout()
                    ->createBlock('page/html')
                    ->setData('is_multiple', 0)
                    ->setFirstItem($firstTaxArea)
                    ->setTemplate('vertexsmb/popup-content.phtml')
                    ->toHtml();
            }
            $address->setCity($firstTaxArea->getTaxAreaCity());
            $address->setTaxAreaId($firstTaxArea->getTaxAreaId())
                ->save();
            $orderCreateModel->saveQuote();
        }
        
        if ($addressChanged && ! $address->getQuote()->isVirtual()) {
            $result['message'] = "show_popup";
            $result['html'] = $blockAddressUpdate;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        $this->setFlag('', self::FLAG_NO_DISPATCH, true);
    }

    /**
     *
     */
    public function saveTaxAreaAction()
    {
        $orderCreateModel = Mage::getSingleton('adminhtml/sales_order_create');
        
        if ($orderCreateModel->getQuote()->isVirtual() || $orderCreateModel->getQuote()
            ->getShippingAddress()
            ->getSameAsBilling()) {
            $address = $orderCreateModel->getQuote()->getBillingAddress();
        } else {
            $address = $orderCreateModel->getQuote()->getShippingAddress();
        }
        
        $taxAreaId = $this->getRequest()->getParam('tax_area_id');
        $city = $this->getRequest()->getPost('new_city', 0);
        
        $address->setTaxAreaId($taxAreaId);
        $oldCity = $address->getCity();
        if (strtolower($oldCity) != strtolower($city)) {
            $address->setCity($city);
        }
        
        $orderCreateModel->saveQuote();
        $result['message'] = 'ok';
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        $this->setFlag('', self::FLAG_NO_DISPATCH, true);
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
