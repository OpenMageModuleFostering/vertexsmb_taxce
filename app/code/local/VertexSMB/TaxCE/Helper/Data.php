<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function IsVertexSMBActive(){      
         if (Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_ENABLE_VERTEX, Mage::app()->getStore()->getId())) 
             return true;       
         return false;
    }    
    public function getLocationCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_LOCATION_CODE, Mage::app()->getStore()->getId());       
    }    
    public function getCompanyCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_COMPANY_CODE, Mage::app()->getStore()->getId());       
    }
    public function getCompanyStreet1(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_STREET1, Mage::app()->getStore()->getId());       
    }    
    public function getCompanyStreet2(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_STREET2, Mage::app()->getStore()->getId());       
    } 
    public function getCompanyCity(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_CITY, Mage::app()->getStore()->getId());       
    }       
    public function getCompanyCountry(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_COUNTRY, Mage::app()->getStore()->getId());
    }
    public function getCompanyRegionId(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_REGION, Mage::app()->getStore()->getId());       
    }
    public function getCompanyPostalCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_POSTAL_CODE, Mage::app()->getStore()->getId());       
    } 
    public function getShippingTaxClassId(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, Mage::app()->getStore()->getId());       
    }             
    public function getTrustedId(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID, Mage::app()->getStore()->getId());       
    }      
    public function getTransactionType(){
        return  VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_TRANSACTION_TYPE;       
    }     
    public function getVertexHost(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_API_HOST, Mage::app()->getStore()->getId());     
    }
    public function getVertexAddressHost(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_ADDRESS_API_HOST, Mage::app()->getStore()->getId());     
    }    
    public function getDefaultCustomerCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE, Mage::app()->getStore()->getId());         
    }      
    public function getCreditmemoAdjustmentFeeCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE, Mage::app()->getStore()->getId());         
    }  
    public function getCreditmemoAdjustmentFeeClass(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, Mage::app()->getStore()->getId());         
    }         
    public function getCreditmemoAdjustmentPositiveCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE, Mage::app()->getStore()->getId());         
    }   
    public function getCreditmemoAdjustmentPositiveClass(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, Mage::app()->getStore()->getId());         
    }  
    public function AllowCartQuote(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE, Mage::app()->getStore()->getId());         
    }        
    public function getGiftWrappingOrderClass(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ORDER_CLASS, Mage::app()->getStore()->getId());         
    }    
    public function getGiftWrappingOrderCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ORDER_CODE, Mage::app()->getStore()->getId());         
    }        
    public function getGiftWrappingItemClass(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ITEM_CLASS, Mage::app()->getStore()->getId());         
    }    
    public function getGiftWrappingItemCodePrefix(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_GIFTWRAP_ITEM_CODE_PREFIX, Mage::app()->getStore()->getId());         
    }     
    public function getPrintedGiftcardClass(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_PRINTED_GIFTCARD_CLASS, Mage::app()->getStore()->getId());         
    }    
    public function getPrintedGiftcardCode(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::VERTEX_PRINTED_GIFTCARD_CODE, Mage::app()->getStore()->getId());         
    }         
    public function IsAllowedQuote() {
        $quote_allowed_controllers=Mage::helper('taxce/config')->getQuoteAllowedControllers();
        if ($this->AllowCartQuote()) 
            $quote_allowed_controllers[]='cart';
        
        if (in_array( Mage::app()->getRequest()->getControllerName(), $quote_allowed_controllers))
                return true;
        
        return false;
    }
    
    public function ShowManualInvoiceButton(){         
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON, Mage::app()->getStore()->getId()); 
    }
 
    public function ShowPopup(){
        return  Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_SHOW_POPUP, Mage::app()->getStore()->getId()); 
    }
    
    public function RequestbyInvoiceCreation(){
        $vertex_invoice_event=Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER, Mage::app()->getStore()->getId());    
        if ($vertex_invoice_event=='invoice_created')
            return true;
        return false;
    } 
    
    public function RequestbyOrderStatus($status){
        $vertex_invoice_event=Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER, Mage::app()->getStore()->getId()); 
        $vertex_invoice_order_status=Mage::getStoreConfig(VertexSMB_TaxCE_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS, Mage::app()->getStore()->getId()); 
        if ($vertex_invoice_event=='order_status' && $vertex_invoice_order_status==$status)
            return true;
        return false;
    } 
    
    public function TaxClassNameByClassId($class_id) {
         
        $tax_class_name = Mage::getModel('tax/class')->load($class_id)->getClassName();
         if ($class_id == 0 && !$tax_class_name)
            $tax_class_name = "None";
        return $tax_class_name;
    }
    
    public function getSourcePath(){
        $controller=Mage::app()->getRequest()->getControllerName();
        $module=Mage::app()->getRequest()->getModuleName();
        $action=Mage::app()->getRequest()->getActionName();        
        $source_path="";        
        if ($controller)
            $source_path.=$controller;
        if ($module)
            $source_path.="_".$module;
        if ($action)
            $source_path.="_".$action;        
        
        return $source_path;
    }
    
    public function TaxClassNameByCustomerGroupId($group_id) {
        $classId = Mage::getModel('customer/group')->getTaxClassId($group_id);
        return $this->TaxClassNameByClassId($classId);
    }
      
    public function getCustomerCodeById($customer_id=0) {        
       $customer_code = $this->getDefaultCustomerCode();
       if ($customer_id) 
            $customer_code = Mage::getModel('customer/customer')->load($customer_id)->getCustomerCode();
       
       if (empty($customer_code))  
           $customer_code=$this->getDefaultCustomerCode();
       
        return $customer_code;
    }
    
    public function CheckCredentials() {
          
         if (!$this->getVertexHost()) 
            return "Not Valid: Missing Api Url";
         if (!$this->getVertexAddressHost()) 
            return "Not Valid: Missing Address Validation Api Url";                    
        if (!$this->getTrustedId()) 
            return "Not Valid: Missing Trusted Id";
         if (!$this->getCompanyRegionId()) 
            return "Not Valid: Missing Company State";
         if (!$this->getCompanyCountry())
             return "Not Valid: Missing Company Country";         
         if (!$this->getCompanyStreet1())             
            return "Not Valid: Missing Company Street Address";
         if (!$this->getCompanyCity()) 
            return "Not Valid: Missing Company City";   
         if (!$this->getCompanyPostalCode()) 
            return "Not Valid: Missing Company Postal Code";             
         
         $region_id=$this->getCompanyRegionId();
         if (is_int($region_id)) {
             $regionModel = Mage::getModel('directory/region')->load($region_id);
             $company_state=$regionModel->getCode();
         } else {
             $company_state=$region_id;
         }         
         
         $countryModel=Mage::getModel('directory/country')->load($this->getCompanyCountry());
         $countryName = $countryModel->getIso3Code();
         
         /*Admin API verification*/
         $address=new Varien_Object();
         $address->setStreet1($this->getCompanyStreet1());
         $address->setStreet2($this->getCompanyStreet2());
         $address->setCity($this->getCompanyCity());
         $address->setRegionCode($company_state);
         $address->setPostcode($this->getCompanyPostalCode());
                                             
        if ($countryName!='USA')
            return "Valid";
        
        $request_result=Mage::getModel('taxce/TaxAreaRequest')->prepareRequest($address)->taxAreaLookup();
        if ($request_result instanceof Exception) {            
            return "Address Validation Error: Please check settings";
        }
        return "Valid";
    }
    
   
    /* Company Information */
   public function AddSellerInformation($data){      
    
       $region_id=$this->getCompanyRegionId();
       if (is_int($region_id)) {
            $regionModel = Mage::getModel('directory/region')->load($region_id);
            $company_state=$regionModel->getCode();
       } else {
           $company_state=$region_id;
       }        
             
       $countryModel=Mage::getModel('directory/country')->load($this->getCompanyCountry());
       $countryName = $countryModel->getIso3Code();
       
       $data['location_code']=$this->getLocationCode();      
       $data['transaction_type']=$this->getTransactionType();
       $data['company_id']=$this->getCompanyCode();
       $data['company_street_1']=$this->getCompanyStreet1();
       $data['company_street_2']=$this->getCompanyStreet2();
       $data['company_city']=$this->getCompanyCity();
       $data['company_state']=$company_state;
       $data['company_postcode']=$this->getCompanyPostalCode();
        
       $data['company_country'] =$countryName;
       
       $data['trusted_id']= $this->getTrustedId();        
       
       return $data;
   }
   
    public function AddAddressInformation($data,$address) {                  
        $data['customer_street1'] = $address->getStreet1();
        $data['customer_street2'] = $address->getStreet2();
        $data['customer_city'] = $address->getCity();
        $data['customer_region'] = $address->getRegionCode();       
        $data['customer_postcode'] = $address->getPostcode();
                
        $countryModel=Mage::getModel('directory/country')->load($address->getCountryId());        
        $countryName = $countryModel->getIso3Code();        
        $data['customer_country'] =$countryName;
        
        $data['tax_area_id'] = $address->getTaxAreaId();
        return $data;
    }
    
    public function IsFirstOfPartial($order_address,$original_entity){
        
        /* Invoice Shipping with first partial invoice */
        if ($original_entity instanceof Mage_Sales_Model_Order_Invoice) {
            if (!$original_entity->getShippingTaxAmount())
                return false;
        }         
        /* Not invoice shipping if there is partial invoice */
        if ($this->RequestbyInvoiceCreation() && $original_entity instanceof Mage_Sales_Model_Order && $original_entity->getShippingInvoiced())
              return false;
        
        if ($original_entity instanceof  Mage_Sales_Model_Order_Creditmemo) {
            if (!$original_entity->getShippingAMount())
                return false;
        }
        
        return true;
    }
    
    public function AddRefundAdjustments($info, $creditmemo_model){
        
        if ($creditmemo_model->getAdjustmentPositive()) {
            $item_data=array();     
            $item_data['product_class']=$this->TaxClassNameByClassId($this->getCreditmemoAdjustmentPositiveClass());        
            $item_data['product_code']=$this->getCreditmemoAdjustmentPositiveCode();   
            $item_data['qty']=1; 
            $item_data['price']=-1*$creditmemo_model->getAdjustmentPositive(); 
            $item_data['extended_price']=-1*$creditmemo_model->getAdjustmentPositive(); 
            $info[]=$item_data;
        }
        if ($creditmemo_model->getAdjustmentNegative()) {
            $item_data=array();  
            $item_data['product_class']=$this->TaxClassNameByClassId($this->getCreditmemoAdjustmentFeeClass());       
            $item_data['product_code']=$this->getCreditmemoAdjustmentFeeCode();   
            $item_data['qty']=1; 
            $item_data['price']=$creditmemo_model->getAdjustmentNegative(); 
            $item_data['extended_price']=$creditmemo_model->getAdjustmentNegative();             
            $info[]=$item_data;
        }
        return $info;
    }
     
    public function AddOrderGiftWrap($order_address, $original_entity=null,$event=null) {
         $item_data=array();     
        if (!$this->IsFirstOfPartial($order_address,$original_entity)) {
            return $item_data;
        }
        
        $item_data['product_class']=$this->TaxClassNameByClassId($this->getGiftWrappingOrderClass());      
        $item_data['product_code']=$this->getGiftWrappingOrderCode();
        $item_data['qty']=1;   
        $item_data['price']=$order_address->getGwPrice(); 
        $item_data['extended_price']=$item_data['qty']*$item_data['price'];             
        
         /* Negative amounts */
        if ($event=='cancel' || $event=='refund' ){
            $item_data['price']=-1*$item_data['price'];
            $item_data['extended_price']=-1*$item_data['extended_price'];
        }
        /* Negative amounts */    
        
        return $item_data;
    }    
    
    public function AddOrderPrintCard($order_address, $original_entity=null,$event=null) {
         $item_data=array();   
        if (!$this->IsFirstOfPartial($order_address,$original_entity)) {
            return $item_data;
        }                         
        $item_data['product_class']=$this->TaxClassNameByClassId($this->getPrintedGiftcardClass());      
        $item_data['product_code']=$this->getPrintedGiftcardCode();
        $item_data['qty']=1;   
        $item_data['price']=$order_address->getGwCardPrice(); 
        $item_data['extended_price']=$order_address->getGwCardPrice();             
        
         /* Negative amounts */
        if ($event=='cancel' || $event=='refund' ){
            $item_data['price']=-1*$item_data['price'];
            $item_data['extended_price']=-1*$item_data['extended_price'];
        }
        /* Negative amounts */    
        
        return $item_data;
    } 
    
    
    
    public function AddShippingInfo($order_address, $original_entity=null,$event=null) {
        $item_data=array();          
        if ($order_address->getShippingMethod() && $this->IsFirstOfPartial($order_address,$original_entity)) {
             
            $item_data['product_class']=$this->TaxClassNameByClassId($this->getShippingTaxClassId());       
            $item_data['product_code']=$order_address->getShippingMethod();             
            $item_data['price']=$order_address->getShippingAmount()-$order_address->getShippingDiscountAmount();
            $item_data['qty']=1;        
            $item_data['extended_price']=$item_data['price'];  

         if ($original_entity instanceof  Mage_Sales_Model_Order_Creditmemo) {
             $item_data['price']=$original_entity->getShippingAmount();
             $item_data['extended_price']=$item_data['price'];  
         }
        /* Negative amounts */
        if ($event=='cancel' || $event=='refund' ){
            $item_data['price']=-1*$item_data['price'];
            $item_data['extended_price']=-1*$item_data['extended_price'];
        }
        /* Negative amounts */    
        }
        return $item_data;
    }
     
    /*beta*/
    /* Tax Quote calculation*/
    public function TaxQuoteItems($address){
        $information_array = Mage::getModel('taxce/taxQuote')->collectQuotedata($address); 
        $information= new Varien_Object($information_array);
        $information->setTaxAreaId();
        $taxed_items_info=Mage::getModel('taxce/taxQuote')->getTaxQuote($information_array);
        
        return $taxed_items_info;
    }
    
    public function CanQuoteTax(){
       if (!$this->IsAllowedQuote() )  
               return false;      
       /* disable for index page. */
       if ($this->getSourcePath()=='onepage_checkout_index')
           return false;
       
       return true;
    }
    /*beta*/
     
              
    /* Common function for item preparation  */
    public function PrepareItem($item, $type='ordered', $original_entity_item=null,$event=null){
        $item_data=array(); 
              
        $item_data['product_class']=$this->TaxClassNameByClassId($item->getProduct()->getTaxClassId());       
        $item_data['product_code']=$item->getSku();
        $item_data['item_id']=$item->getId();
        
        /* Price */
         if  ($type=='invoiced') 
            $price=$original_entity_item->getPrice();                        
         else
             $price=$item->getPrice();//-$item->getDiscountAmount();  
        /* Price */
        
        $item_data['price']=$price;
        if ($type=='ordered' && $this->RequestbyInvoiceCreation() ) /*In case order partically being invoiced*/
             $item_data['qty']=$item->getQtyOrdered()-$item->getQtyInvoiced();
        elseif ($type=='ordered')
            $item_data['qty']=$item->getQtyOrdered();
        elseif ($type=='invoiced')
            $item_data['qty']=$original_entity_item->getQty();
        elseif ($type=='quote') 
            $item_data['qty']=$item->getQty();
        
        /* Always send discounted. Discount on TotalRowAmount*/
        if  ($type=='invoiced')
              $item_data['extended_price']=$original_entity_item->getRowTotal()-$original_entity_item->getDiscountAmount();    
        else if ($type=='ordered' && $this->RequestbyInvoiceCreation() )  /*In case order partically being invoiced*/
            $item_data['extended_price']=$item->getRowTotal()-$item->getRowInvoiced()-$item->getDiscountAmount()+$item->getDiscountInvoiced();            
        else                
            $item_data['extended_price']=$item->getRowTotal()-$item->getDiscountAmount(); 
                        
        /* Negative amounts */
        if ($event=='cancel' || $event=='refund'){
            $item_data['price']=-1*$item_data['price'];
            $item_data['extended_price']=-1*$item_data['extended_price'];
        }        
        return $item_data;             
   }
   
       public function PrepareGiftWrapItem($item, $type='ordered', $original_entity_item=null,$event=null){
        $item_data=array(); 
        
        /* @todo move to config */
        $item_data['product_class']=$this->TaxClassNameByClassId($this->getGiftWrappingItemClass());
        $item_data['product_code']=$this->getGiftWrappingItemCodePrefix().'-'.$item->getSku();
        
        /* Price */
         if  ($type=='invoiced') 
            $price=$item->getGwPriceInvoiced();                        
         else
             $price=$item->getGwPrice();  
        /* Price */
        
        $item_data['price']=$price;        
        if ($type=='ordered' && $this->RequestbyInvoiceCreation() ) /*In case order partically being invoiced*/
             $item_data['qty']=$item->getQtyOrdered()-$item->getQtyInvoiced();
        elseif ($type=='ordered')
            $item_data['qty']=$item->getQtyOrdered();
        elseif ($type=='invoiced')
            $item_data['qty']=$original_entity_item->getQty();
        elseif ($type=='quote') 
            $item_data['qty']=$item->getQty();             
      
        if  ($type=='invoiced')
              $item_data['extended_price']= $item_data['qty']*$item_data['price'];    
        else if ($type=='ordered' && $this->RequestbyInvoiceCreation() )  /*In case order partically being invoiced*/
            $item_data['extended_price']= $item_data['qty']*$item_data['price'];            
        else                
            $item_data['extended_price']= $item_data['qty']*($item->getGwPrice()); 
                        
        /* Negative amounts */
        if ($event=='cancel' || $event=='refund'){
            $item_data['price']=-1*$item_data['price'];
            $item_data['extended_price']=-1*$item_data['extended_price'];
        }
        
        return $item_data;             
   }
   
   /* Used in other files for corrent session & access to quote*/
   public function getSession(){
         if (Mage::app()->getRequest()->getControllerName()!='sales_order_create')
            return Mage::getSingleton('checkout/session'); 
        else 
            return Mage::getSingleton('adminhtml/session_quote'); 
    }

    
}
