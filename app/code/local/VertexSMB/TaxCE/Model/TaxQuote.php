<?php

 class VertexSMB_TaxCE_Model_TaxQuote extends Mage_Core_Model_Abstract {
       
    private $_ItemsTaxLines=array();
    private $_ItemsTaxQuoteId=array();
    
    public function _construct()
    {
        $this->_init('taxce/taxquote');
    }        
    
    public function getHelper() {
        return Mage::helper('taxce');
    }
     
    public function getTaxQuote($information) {    
        $session=$this->getHelper()->getSession();
        $this->_ItemsTaxQuoteId=array();           
        $this->_ItemsTaxLines=array(); 
        
         /* Prevent multiple query when id updated */
        if (!$information['tax_area_id'] && !$this->getHelper()->AllowCartQuote())
            return false;
        /* Automatically decide tax area id */
        
        if ($this->getHelper()->getSourcePath()=='cart_checkout_index' || $this->getHelper()->getSourcePath()=='cart_checkout_couponPost') { 
            $information['tax_area_id']='';
            $information['customer_street1']='';
            $information['customer_street2']='';
        }
        
       /* Quotation Request Array*/
       $information['request_type']='QuotationRequest';                
       $request=Mage::getModel('taxce/requestItem')->setData($information)->exportAsArray();
       /* Quotation Request Array*/
       
       /*Some special magic for quote*/
          $i=1; /* lineItemNumber */
        foreach($information['order_items'] as $key=>$item){                                          
                $item_tax_info=array();            
                $item_tax_info['lineItemNumber']=$i;
                $item_tax_info['quote_item_id']=$key;
                $this->_ItemsTaxLines[$i]= new Varien_Object($item_tax_info);
                $i++;
        }            
        /*Some special magic for quote*/
        
       
        $tax_quote_result=Mage::getModel('taxce/taxce')->SendApiRequest($request,null,'quote');
        if ($tax_quote_result instanceof Exception) {               
            /*@info error handles for different page types */
             if (Mage::app()->getRequest()->getControllerName()=='onepage' || Mage::app()->getRequest()->getControllerName()=='sales_order_create') {
                  Mage::log("Quote Request Error: ".$tax_quote_result->getMessage()."Controller:  ".$this->getHelper()->getSourcePath(), null, 'taxce.log');
                  $result=array('error' => 1, 'message' => "Tax Calculation Request Error. Please check your address");                 
                  echo Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));               
                  exit();
             }
             if ($this->getHelper()->getSourcePath()=='cart_checkout_index' || $this->getHelper()->getSourcePath()=='cart_checkout_couponPost') {
                 $this->getHelper()->getSession()->setVertexTQ(0);
                 $this->getHelper()->getSession()->addError(Mage::helper('core')->escapeHtml("Tax Calculation Request Error. Please check your address"));                 
             }
             $session->setItemsTaxQuoteId();
             return false;
        }
                 
        if (is_array($tax_quote_result->QuotationResponse->LineItem))
             $items_tax=$tax_quote_result->QuotationResponse->LineItem;
         else 
             $items_tax[]=$tax_quote_result->QuotationResponse->LineItem;


      foreach ($items_tax as $item) {             
          $lineItemNumber=$item->lineItemNumber;
          $ItemTotaltax=$item->TotalTax->_;    
          /* SUMM Percents For Jurisdictions */
          $TaxPercent=0;
          foreach ($item->Taxes  as $key=>$tax_value) 
              if ($key=="EffectiveRate")
                $TaxPercent+=$tax_value;
           
          $TaxPercent=$TaxPercent*100;     
         
          /* SUMM Percents For Jurisdictions */
          $items_tax_lines_data=$this->_ItemsTaxLines[$lineItemNumber]->getData();
          $items_tax_lines_data['tax_amount']=$ItemTotaltax;
          $items_tax_lines_data['base_tax_amount']=$ItemTotaltax;
          $items_tax_lines_data['tax_percent']=$TaxPercent;
          $this->_ItemsTaxLines[$lineItemNumber]->setData($items_tax_lines_data);
          
          $quote_item_id=$this->_ItemsTaxLines[$lineItemNumber]->getQuoteItemId();
          $this->_ItemsTaxQuoteId[$quote_item_id]=$this->_ItemsTaxLines[$lineItemNumber];
      }                           
        $session->setItemsTaxQuoteId($this->_ItemsTaxQuoteId);
   }
   
    /* Collect Quote Information */
    public function collectQuotedata(Mage_Sales_Model_Quote_Address $address) {
                 
        $information = array();
        $information=$this->getHelper()->AddSellerInformation($information);
        
        $customer_class_name =$this->getHelper()->TaxClassNameByClassId($address->getQuote()->getCustomerTaxClassId());
        $customer_code = $this->getHelper()->getCustomerCodeById($address->getQuote()->getCustomer()->getId());            
        
        $information['customer_code'] = $customer_code;
        $information['customer_class'] = $customer_class_name;        
 
        $information=$this->getHelper()->AddAddressInformation($information, $address);
       
        $information['store_id'] = $address->getQuote()->getStore()->getId();
        $date=date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
        $information['posting_date']=$date;
        $information['document_date']=$date;
        
        $information['order_items'] = array();
        $items = $address->getAllNonNominalItems(); 
        foreach ($items as $item) {
             if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_CatalogPermissions' )
                     && Mage::getConfig ()->getModuleConfig ( 'Enterprise_CatalogPermissions' )->is('active', 'true'))
             if ($item->getDisableAddToCart() && !$item->isDeleted()) 
                 continue;                          
             
            if ($item->getParentItem()) 
                continue;
            
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                 foreach ($item->getChildren() as $child) {
                      $information['order_items'][$child->getId()] = $this->getHelper()->PrepareItem($child,'quote');
                      if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
                          &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') && $child->getGwId()) 
                              $information['order_items']['gift_wrap_'.$child->getId()] = $this->getHelper()->PrepareGiftWrapItem($child,'quote');                              
                 }
             } else {
                  $information['order_items'][$item->getId()] = $this->getHelper()->PrepareItem($item,'quote');
                  if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
                    &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') && $item->getGwId()) 
                        $information['order_items']['gift_wrap_'.$item->getId()] = $this->getHelper()->PrepareGiftWrapItem($item,'quote');                   
             }                       
        }
        if(count($this->getHelper()->AddShippingInfo($address)))
            $information['order_items']['shipping']=$this->getHelper()->AddShippingInfo($address);
        
        if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
            &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true')) { 
            if ($address->getGwPrice())
                $information['order_items']['gift_wrapping']=$this->getHelper()->AddOrderGiftWrap($address);
            if ($address->getGwCardPrice())
                $information['order_items']['gift_print_card']=$this->getHelper()->AddOrderPrintCard($address);                        
        }
        
        /* return  new Varien_Object($information); */
        return $information;
    }   
    
    
    
 }