<?php
 

/**
 * Description of TaxInvoice
 *
 * @author alukyanau
 */
 class VertexSMB_TaxCE_Model_TaxInvoice extends Mage_Core_Model_Abstract {

    public function _construct()
    {
        $this->_init('taxce/taxinvoice');
    }        
    
    public function getHelper() {
        return Mage::helper('taxce');
    }
    
   /* Prepare Invoice Request Data (Order|Invoice) 
    * @todo Add event names
    * 
    */ 
   public function PrepareInvoiceData($entity_item, $event=null){
       
        $info=array();
        $info=$this->getHelper()->AddSellerInformation($info);

        if ($entity_item instanceof Mage_Sales_Model_Order) {
            $order=$entity_item;
        }elseif($entity_item instanceof Mage_Sales_Model_Order_Invoice) {
            $order=$entity_item->getOrder();
        }elseif($entity_item instanceof Mage_Sales_Model_Order_Creditmemo) {
            $order=$entity_item->getOrder();
        }
        $info['order_id']=$order->getIncrementId();
        $info['document_number']=$order->getIncrementId();
        $info['document_date']=date("Y-m-d", strtotime($order->getCreatedAt()));
        $info['posting_date']=date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
                                
        $customer_class = $this->getHelper()->TaxClassNameByCustomerGroupId($order->getCustomerGroupId());        
        $customer_code = $this->getHelper()->getCustomerCodeById($order->getCustomerId());                     
        
        $info['customer_class']=$customer_class;
        $info['customer_code']=$customer_code;   
        
         if ($order->getIsVirtual()) 
            $address=$order->getBillingAddress();
         else 
            $address=$order->getShippingAddress();
         
        $info=$this->getHelper()->AddAddressInformation($info,$address);
      
        /* Get Items Information*/
        $order_items=array();
        $ordered_items = $entity_item->getAllItems(); 
        
        foreach($ordered_items as $item){            
            $original_item=$item;
            if ($entity_item instanceof Mage_Sales_Model_Order_Invoice)
                $item=$item->getOrderItem(); 
            elseif ($entity_item instanceof Mage_Sales_Model_Order_Creditmemo)
                 $item=$item->getOrderItem(); 
            
            if ($item->getParentItem())  
                continue;
                           
            if ($item->getHasChildren() &&  $item->getProduct()->getPriceType()!==null 
                && (int)$item->getProduct()->getPriceType()===Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) 
                foreach ($item->getChildrenItems() as $child) {                  
                    if ( $entity_item instanceof Mage_Sales_Model_Order_Invoice || $entity_item instanceof Mage_Sales_Model_Order_Creditmemo  ) {
                        $order_items[]=$this->getHelper()->PrepareItem($child,'invoiced',$original_item,$event);                     
                        if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
                            &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') && $child->getGwId()) 
                            $order_items[] = $this->getHelper()->PrepareGiftWrapItem($child,'invoiced',$original_item,$event);
                    }elseif ($entity_item instanceof Mage_Sales_Model_Order) {
                        $order_items[]=$this->getHelper()->PrepareItem($child,'ordered',$original_item,$event);
                        if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
                            &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') && $child->getGwId()) 
                            $order_items[] = $this->getHelper()->PrepareGiftWrapItem($child,'ordered',$original_item,$event);
                    }
                }
              else {
                    if ( $entity_item instanceof Mage_Sales_Model_Order_Invoice  || $entity_item instanceof Mage_Sales_Model_Order_Creditmemo ) {
                        $order_items[]=$this->getHelper()->PrepareItem($item,'invoiced',$original_item,$event);                             
                        if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
                            &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') && $item->getGwId()) 
                            $order_items[] = $this->getHelper()->PrepareGiftWrapItem($item,'invoiced',$original_item,$event);
                    }elseif ($entity_item instanceof Mage_Sales_Model_Order) {
                        $order_items[]=$this->getHelper()->PrepareItem($item,'ordered',$original_item,$event);   
                        if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
                            &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') && $item->getGwId()) 
                            $order_items[] = $this->getHelper()->PrepareGiftWrapItem($item,'ordered',$original_item,$event);
                    }
              }
        }
                
        if (!$order->getIsVirtual() && count($this->getHelper()->AddShippingInfo($order,$entity_item,$event)))
            $order_items[]=$this->getHelper()->AddShippingInfo($order,$entity_item,$event);
        
        if ($entity_item instanceof Mage_Sales_Model_Order_Creditmemo )
            $order_items=$this->getHelper()->AddRefundAdjustments($order_items,$entity_item);
        
        if (Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )
            &&  Mage::getConfig ()->getModuleConfig ( 'Enterprise_GiftWrapping' )->is('active', 'true') ) { 
            if ($original_item->getGwPrice() && count($this->getHelper()->AddOrderGiftWrap($order,$entity_item,$event)))
                $order_items[]=$this->getHelper()->AddOrderGiftWrap($order,$entity_item,$event);
            if ($original_item->getGwCardPrice() && count($this->getHelper()->AddOrderPrintCard($order,$entity_item,$event)))
                $order_items[]=$this->getHelper()->AddOrderPrintCard($order,$entity_item,$event);                        
        }
        /* Get Items Information*/
        /* Making Request Array*/
         $info['request_type']='InvoiceRequest';         
         $info['order_items']=$order_items;
         $request=Mage::getModel('taxce/requestItem')->setData($info)->exportAsArray();  
            
        /* Making Request Array*/        
        return $request;                
   }    
   
   /* Invoice Request Preparation*/
   public function SendInvoiceRequest($data,$order=null){
       if ($order==null)
           $order=Mage::registry('current_order');     
       
       $request_result=Mage::getModel('taxce/taxce')->SendApiRequest($data,$order,'invoice');        
       if ($request_result instanceof Exception) {
            Mage::log("Invoice Request Error: ".$request_result->getMessage(), null, 'taxce.log');
            Mage::getSingleton('adminhtml/session')->addError($request_result->getMessage());
           return false;
       }
       
       $order->addStatusHistoryComment('Vertex Invoice sent successfully. Amount: $'.$request_result->InvoiceResponse->TotalTax->_,  false)->save();
       return true;
   }

   /* Cancel Request Preparation*/
   public function SendCancelRequest($data,$order=null){
       if ($order==null)
           $order=Mage::registry('current_order');     
       
       $request_result=Mage::getModel('taxce/taxce')->SendApiRequest($data,$order,'invoice_cancel');        
       if ($request_result instanceof Exception) {
            Mage::log("Cancel Request Error: ".$request_result->getMessage(), null, 'taxce.log');
            Mage::getSingleton('adminhtml/session')->addError($request_result->getMessage());
           return false;
       }
       
       $order->addStatusHistoryComment('Vertex Invoice canceled successfully. Amount: $'.$request_result->InvoiceResponse->TotalTax->_,  false)->save();
       return true;
   }
   /* Cancel Request Preparation*/
   public function SendRefundRequest($data,$order=null){
       if ($order==null)
           $order=Mage::registry('current_order');            
       $request_result=Mage::getModel('taxce/taxce')->SendApiRequest($data,$order,'invoice_refund');        
       if ($request_result instanceof Exception) {
            Mage::log("Refund Request Error: ".$request_result->getMessage(), null, 'taxce.log');
            Mage::getSingleton('adminhtml/session')->addError($request_result->getMessage());
           return false;
       }       
       $order->addStatusHistoryComment('Vertex Invoice refunded successfully. Amount: $'.$request_result->InvoiceResponse->TotalTax->_,  false)->save();
       return true;
   }   
      
    
}
