<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */

class VertexSMB_TaxCE_VertexSMBController extends Mage_Adminhtml_Controller_Action {

    protected function _construct() {        
        $this->setUsedModuleName('VertexSMB_TaxCE');
    }

    protected function _isAllowed() {
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

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
    
    public function InvoicetaxAction() {
        if ($order = $this->_initOrder()) {                        
           $invoice_request_data=Mage::getModel('taxce/taxInvoice')->PrepareInvoiceData($order);              
           if ($invoice_request_data && Mage::getModel('taxce/taxInvoice')->SendInvoiceRequest($invoice_request_data)) 
               $this->_getSession()->addSuccess( $this->__('The Vertex SMB invoice has been sent.'));                   
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));       
    }
    
    public function TaxAreaAction(){
        $orderCreateModel=Mage::getSingleton('adminhtml/sales_order_create');
        $address_changed=false;
          
        if ($orderCreateModel->getQuote()->isVirtual() || $orderCreateModel->getQuote()->getShippingAddress()->getSameAsBilling() ) 
              $address=$orderCreateModel->getQuote()->getBillingAddress();
        else 
             $address=$orderCreateModel->getQuote()->getShippingAddress(); 
        
        if (!$address->getStreet1() || !$address->getCity() || !$address->getRegion() || !$address->getPostcode() ) {
            $result['message']='address not completed';
            echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));   
            exit();                  
        }

        $order_post=$this->getRequest()->getPost('order');                    
            
        /*Other Countries*/
        if ($address->getCountryId()!='USA') {
            $result['message']='not_usa_address';
            echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            exit();
        }
        
        $TaxAreaModel=Mage::getModel('taxce/TaxAreaRequest');
        $request_result=$TaxAreaModel->prepareRequest($address)->taxAreaLookup();
        if ($request_result instanceof Exception) {
            Mage::log("Admin Tax Area Lookup Error: ".$request_result->getMessage(), null, 'vertexsmb.log');
            $result['message'] = $request_result->getMessage();
            $result['error'] =1;
            echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            exit();
        }
        
        $TaxAreaResposeModel=$TaxAreaModel->getResponse();
         
        
        if ($TaxAreaResposeModel->getResultsCount()>1 && Mage::helper('taxce')->ShowPopup()) { 
                $block=Mage::app()->getLayout()->createBlock('page/html')->setTemplate('vertexsmb/popup-content.phtml')
                                  ->setData('response',$request_result)->toHtml();           
             $result['message'] ="show_popup";
             $result['html'] =$block;                                       
        } else {
            $FirstTaxArea=$TaxAreaResposeModel->GetFirstTaxAreaInfo();
            if ($FirstTaxArea->getCity()) {
                $result['message']='tax_area_id_found';            
                /* @todo modify template for object or address */
                if (strtolower($address->getCity())!=strtolower($FirstTaxArea->getCity())){
                    $address_changed=true;             
                     $block_address_update=Mage::app()->getLayout()->createBlock('page/html')->setTemplate('vertexsmb/addresschange-popup-content.phtml')
                        ->setOldCity($address->getCity())->setNewCity($FirstTaxArea->getCity())->setTaxAreaId($FirstTaxArea->getTaxAreaId())->toHtml();     
                }
                $address->setCity($FirstTaxArea->getCity());
            }
                $address->setTaxAreaId($FirstTaxArea->getTaxAreaId())->save();                                  
                $orderCreateModel->saveQuote();            
        }             
          
        if ($address_changed && !$address->getQuote()->isVirtual()) {
             $result['message'] ="show_popup";
             $result['html'] =$block_address_update;
        }
           
        echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));   
        exit();                  
    }
    
    public function saveTaxAreaAction() {
        $orderCreateModel=Mage::getSingleton('adminhtml/sales_order_create');
        
        if ($orderCreateModel->getQuote()->isVirtual() || $orderCreateModel->getQuote()->getShippingAddress()->getSameAsBilling() ) 
            $address=$orderCreateModel->getQuote()->getBillingAddress();
        else 
            $address=$orderCreateModel->getQuote()->getShippingAddress(); 
        
         $tax_area_id=$this->getRequest()->getParam('tax_area_id');
         $new_city = $this->getRequest()->getPost('new_city', 0);     
          
         $address->setTaxAreaId($tax_area_id);
         $old_city=$address->getCity();
         if (strtolower($old_city)!=strtolower($new_city))
                    $address->setCity($new_city);          
          
         $orderCreateModel->saveQuote();        
         $result['message']='ok';
         
         echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));   
         exit();                 
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
