<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
require Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';
 
class VertexSMB_TaxCE_OnepageController extends Mage_Checkout_OnepageController {
    
   /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if (!Mage::helper('taxce')->IsVertexSMBActive()) {
            parent::saveShippingAction();
            return $this;
        }      
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);

            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            
             /*VertexSMB*/            
            $address=$this->getOnepage()->getQuote()->getShippingAddress();      
            /* Save Tax Area & Correct City | Show popup window */
            if (!$this->saveTaxAreaId($address))
                return $this;            
       
            $result['goto_section'] = 'shipping_method';
            $result['update_section'] = array(
                'name' => 'shipping-method',
                'html' => $this->_getShippingMethodsHtml()
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));             
        }        
    }
    
        public function saveBillingAction()
    {
        if (!Mage::helper('taxce')->IsVertexSMBActive()) {
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
             
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);            
 
            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {                                                            
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                    $address=$this->getOnepage()->getQuote()->getBillingAddress(); 
                    /* Save Tax Area & Correct City | Show popup window */
                    if (!$this->saveTaxAreaId($address))
                        return $this;                    
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    
                   
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                    $address=$this->getOnepage()->getQuote()->getShippingAddress();
                    /* Save Tax Area & Correct City | Show popup window */
                    if (!$this->saveTaxAreaId($address))
                        return $this;                    
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }                       
         
           $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));                        
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
        
        /*Other Countries*/
        if ($address->getCountryId()!='USA') {
            return true;   
        }
        /*Other Countries*/
        
         $TaxAreaModel=Mage::getModel('taxce/TaxAreaRequest');
         $request_result=$TaxAreaModel->prepareRequest($address)->taxAreaLookup();
         $address_changed=false;
         if ($request_result instanceof Exception) {
              
            if (Mage::app()->getRequest()->getControllerName()=='onepage' || Mage::app()->getRequest()->getControllerName()=='sales_order_create') {
                  Mage::log("Quote Request Error: ".$request_result->getMessage()."Controller:  ".Mage::helper('taxce')->getSourcePath(), null, 'vertexsmb.log');
                  $result=array('error' => 1, 'message' => "Tax Calculation Request Error. Please check your address");                 
                  echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));               
                  exit();
             }           
             
             /*@todo check cart page if we use tax_area_id there*/
             if (Mage::helper('taxce')->getSourcePath()=='cart_checkout_index' || Mage::helper('taxce')->getSourcePath()=='cart_checkout_couponPost') {                 
                 Mage::helper('taxce')->getSession()->addError(Mage::helper('core')->escapeHtml("Tax Calculation Request Error. Please check your address"));                 
             }             
               return false;
           }
           $TaxAreaResposeModel=$TaxAreaModel->getResponse();
                    
           /*beta*/   
          
           if ($TaxAreaResposeModel->getResultsCount()>1 && Mage::helper('taxce')->ShowPopup()) {               
                $block=Mage::app()->getLayout()->createBlock('core/template')->setTemplate('vertexsmb/popup-content.phtml')
                                  ->setData('response',$request_result)->toHtml();           
                $result['goto_section'] = 'selectaddress';
                $result['update_section'] = array(
                    'name' => 'selectaddress',
                    'html' => $block
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return false;                      
           }else {                
                $FirstTaxArea=$TaxAreaResposeModel->GetFirstTaxAreaInfo();
                                                           
                    /* @todo modify template for object or address */
                if ($FirstTaxArea->getCity()) {
                    if (strcmp(strtolower($address->getCity()),strtolower($FirstTaxArea->getCity())) !==0){
                        Mage::log("Original City: ".$address->getCity()." - New City: ".$FirstTaxArea->getCity(), null, 'vertexsmb.log');
                        $address_changed=true;             
                         $block_address_update=Mage::app()->getLayout()->createBlock('core/template')->setTemplate('vertexsmb/addresschange-popup-content.phtml')
                            ->setOldCity($address->getCity())->setNewCity($FirstTaxArea->getCity())->setTaxAreaId($FirstTaxArea->getTaxAreaId())->toHtml();     
                    }
                    $address->setCity($FirstTaxArea->getCity());                   
                }
                $address->setTaxAreaId($FirstTaxArea->getTaxAreaId())->save();     
                $this->getOnepage()->getQuote()->collectTotals()->save();                                   
           }
            
           /* @todo find out why should be virtual only*/
           if ($address_changed /*&& !$this->getOnepage()->getQuote()->isVirtual()*/ ) {                      
                $result['goto_section'] = 'selectaddress';
                $result['update_section'] = array(
                    'name' => 'selectaddress',
                    'html' => $block_address_update
                );
                
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return false;     
                       
           }
           /*beta*/
                               
            return true;          
    }
}