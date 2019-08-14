<?php

/**
 * Description of Vertex
 *
 * @author alukyanau
 */
class VertexSMB_TaxCE_TaxceController extends Mage_Adminhtml_Controller_Action {

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
               $this->_getSession()->addSuccess( $this->__('The Vertex invoice has been sent.'));                   
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));       
    }
    
    public function TaxAreaAction(){
        $orderCreateModel=Mage::getSingleton('adminhtml/sales_order_create');
                  
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
        /*if (  $address->getTaxAreaId()  && !isset($order_post['billing_address']) && !isset($order_post['shipping_address'])  ) {
            $result['message']='address not changed';
            echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));   
            exit();                  
        }    */                     
            
        $request_result=Mage::Helper('taxce')->LookupAreaRequest($address);
        if ($request_result instanceof Exception) {
            Mage::log("Tax Area Lookup Error: ".$request_result->getMessage(), null, 'taxce.log');
            $result['message'] = $request_result->getMessage();
        }
        
        $tax_area_results=$request_result->TaxAreaResponse->TaxAreaResult;
        if (is_array($tax_area_results) && Mage::helper('taxce')->ShowPopup()) {
             $block=Mage::app()->getLayout()->createBlock('page/html')->setTemplate('taxce/popup-content.phtml')
                 ->setData('response',$request_result)->toHtml(); 
             $result['message'] ="show_popup";
             $result['html'] =$block;
        }elseif (is_array($tax_area_results) && !Mage::helper('taxce')->ShowPopup()) { 
            $tax_area_id=$tax_area_results[0]->taxAreaId;
            $address->setTaxAreaId($tax_area_id);
            $result['message']='tax_area_id_found';
            $orderCreateModel->saveQuote();
        }else { 
          $tax_area_id=$request_result->TaxAreaResponse->TaxAreaResult->taxAreaId;
          $address->setTaxAreaId($tax_area_id);//->save();  
          $result['message']='tax_area_id_found';
        /* Chack if city differs */
        $address_changed=false;
       if (strtolower($address->getCity())!=strtolower($request_result->TaxAreaResponse->TaxAreaResult->PostalAddress->City)) {
           $old_city=$address->getCity();
           $new_city=$request_result->TaxAreaResponse->TaxAreaResult->PostalAddress->City;
           $address_changed=true;
       }
        /*Check if city differs */             
          
          if ($address_changed && !$address->getQuote()->isVirtual()) {
              $block=Mage::app()->getLayout()->createBlock('page/html')->setTemplate('taxce/addresschange-popup-content.phtml')
                 ->setOldCity($old_city)->setNewCity($new_city)->setTaxAreaId($tax_area_id)->toHtml(); 
               $result['message'] ="show_popup";
               $result['html'] =$block;
          }
          
          
           
        /*$result['billing_address']=$address->getData();
          $result['shipping_address']=$orderCreateModel->getQuote()->getShippingAddress()->getData();
          $result['same_as_billing']=$orderCreateModel->getQuote()->getData(); 
         */
          $orderCreateModel->saveQuote();
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
