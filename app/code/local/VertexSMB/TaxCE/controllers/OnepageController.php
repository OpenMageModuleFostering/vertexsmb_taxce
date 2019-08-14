<?php
require Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';
class VertexSMB_TaxCE_OnepageController extends Mage_Checkout_OnepageController {
    
   /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if (!Mage::helper('taxce')->IsVertexActive()) {
            parent::saveShippingAction();
            return $this;
        }      
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $data['tax_area_id']="";
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            
             /*taxce*/            
            $address=$this->getOnepage()->getQuote()->getShippingAddress();                    
            $address->setTaxAreaId()->save();
             if ($this->saveTaxAreaId($address)){           
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
             }
        }
    }
    
        public function saveBillingAction()
    {
        if (!Mage::helper('taxce')->IsVertexActive()) {
            parent::saveBillingAction();
            return $this;
        }                
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $data['tax_area_id']="";
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            
             
            $address=$this->getOnepage()->getQuote()->getBillingAddress(); 
            $address->setTaxAreaId()->save();
             
            $tax_area_result=true;
            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {                                                            
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                    $tax_area_result=$this->saveTaxAreaId($address); 
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $tax_area_result=$this->saveTaxAreaId($address); 
                   
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }
            if ($tax_area_result) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
             
        }
    }

    
     public function saveTaxAreaAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $tax_area_id = $this->getRequest()->getPost('tax_area_id', 0);  
            $new_city = $this->getRequest()->getPost('tax_new_city', 0);     
           
            
       
            if ($this->getOnepage()->getQuote()->isVirtual()) {
                $address=$this->getOnepage()->getQuote()->getBillingAddress();  
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );                
                $shipping_address=$this->getOnepage()->getQuote()->getShippingAddress();
                $old_city=$shipping_address->getCity();
                if (strtolower($old_city)!=strtolower($new_city))
                    $shipping_address->setCity($new_city);
                
                $shipping_address->setTaxAreaId($tax_area_id)->save(); 
             } else {            
                $address=$this->getOnepage()->getQuote()->getShippingAddress();
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );          
                
                $old_city=$address->getCity();
                if (strtolower($old_city)!=strtolower($new_city))
                    $address->setCity($new_city);                
             }          
            $address->setTaxAreaId($tax_area_id)->save();       
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
    
     public function progressAction()
    {
        // previous step should never be null. We always start with billing and go forward
        $prevStep = $this->getRequest()->getParam('prevStep', false);

        if ($this->_expireAjax() || !$prevStep) {
            return null;
        }
        if ($prevStep=='selectaddress')
            $prevStep='shipping';

        if ($prevStep=='selectaddress' && $this->getOnepage()->getQuote()->isVirtual())
            $prevStep='billing';        
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        /* Load the block belonging to the current step*/
        $update->load('checkout_onepage_progress_' . $prevStep);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        $this->getResponse()->setBody($output);
        return $output;
    }
    
    public function saveTaxAreaId($address){
         $request_result=Mage::Helper('taxce')->LookupAreaRequest($address);
           if ($request_result instanceof Exception) {
               Mage::log("Tax Area Lookup Error: ".$request_result->getMessage(), null, 'taxce.log');
            if (Mage::app()->getRequest()->getControllerName()=='onepage' || Mage::app()->getRequest()->getControllerName()=='sales_order_create') {
                  Mage::log("Quote Request Error: ".$request_result->getMessage()."Controller:  ".Mage::helper('taxce')->getSourcePath(), null, 'taxce.log');
                  $result=array('error' => 1, 'message' => "Tax Calculation Request Error. Please check your address");                 
                  echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));               
                  exit();
             }            
             if (Mage::helper('taxce')->getSourcePath()=='cart_checkout_index' || Mage::helper('taxce')->getSourcePath()=='cart_checkout_couponPost') {
                 Mage::helper('taxce')->getSession()->setVertexTQ(0);
                 Mage::helper('taxce')->getSession()->addError(Mage::helper('core')->escapeHtml("Tax Calculation Request Error. Please check your address"));                 
             }             
               return false;
           }
           
           $tax_area_results=$request_result->TaxAreaResponse->TaxAreaResult;
          
           
           if (is_array($tax_area_results) && Mage::helper('taxce')->ShowPopup()) {
            $block=Mage::app()->getLayout()->createBlock('page/html')->setTemplate('taxce/popup-content.phtml')
                    ->setData('response',$request_result)->toHtml();           
                $result['goto_section'] = 'selectaddress';
                $result['update_section'] = array(
                    'name' => 'selectaddress',
                    'html' => $block
               );
               $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
               return false;           
           } elseif(is_array($tax_area_results) && !Mage::helper('taxce')->ShowPopup()){
               $tax_area_id=$tax_area_results[0]->taxAreaId;
                
               $address->setTaxAreaId($tax_area_id)->save();               
               $this->getOnepage()->getQuote()->collectTotals()->save();      
               /* When tax_area_id stored replace */
               if ($address->getAddressType()=='billing' && $this->getOnepage()->getQuote()->getShippingAddress()->getSameAsBilling())
                    $this->getOnepage()->getQuote()->getShippingAddress()->setTaxAreaId($tax_area_id)->save();                 
           } else {                   
                 $tax_area_id=$request_result->TaxAreaResponse->TaxAreaResult->taxAreaId;
 
                 $address->setTaxAreaId($tax_area_id)->save();
                 $this->getOnepage()->getQuote()->collectTotals()->save();   
                 /* When tax_area_id stored replace */
                 if ($address->getAddressType()=='billing' && $this->getOnepage()->getQuote()->getShippingAddress()->getSameAsBilling())
                    $this->getOnepage()->getQuote()->getShippingAddress()->setTaxAreaId($tax_area_id)->save();       

            /* Chack if city differs */
            $address_changed=false;
           if (strtolower($address->getCity())!=strtolower($request_result->TaxAreaResponse->TaxAreaResult->PostalAddress->City)) {
               $old_city=$address->getCity();
               $new_city=$request_result->TaxAreaResponse->TaxAreaResult->PostalAddress->City;
               $address_changed=true;
           }
            /*Check if city differs */                   
                 
                if ($address_changed && !$this->getOnepage()->getQuote()->isVirtual()) {
                    $block=Mage::app()->getLayout()->createBlock('page/html')->setTemplate('taxce/addresschange-popup-content.phtml')
                            ->setOldCity($old_city)->setNewCity($new_city)->setTaxAreaId($tax_area_id)->toHtml();           
                    $result['goto_section'] = 'selectaddress';
                    $result['update_section'] = array(
                        'name' => 'selectaddress',
                        'html' => $block
                    );
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return false;                               
                } 
            }                   
            return true;
            /*taxce*/            
    }
}