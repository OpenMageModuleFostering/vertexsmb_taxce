<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 **/
 
 
 class VertexSMB_TaxCE_Model_RequestItem extends Mage_Core_Model_Abstract {
       
    public function _construct()
    {
        $this->_init('taxce/requestItem');
    }
    
    public function getHelper() {
        return Mage::helper('taxce');
    }
    
    /* RequestType:
     * TaxAreaRequest
     * InvoiceRequest
     * QuotationRequest
     */
    public function exportAsArray(){
        $request=array(            
          'Login'=>array('TrustedId'=>$this->getTrustedId()),
          $this->getRequestType()=>array(
              'documentDate'=>$this->getDocumentDate(),
              'postingDate'=>$this->getPostingDate(),
              'transactionType'=>$this->getTransactionType(),
              'documentNumber' => $this->getDocumentNumber(), 
              'LineItem'=> array() 
              )   
          );
        if ($this->getDocumentNumber())
            $request[$this->getRequestType()]['documentNumber']=$this->getDocumentNumber();
        
        $order_items=$this->getOrderItems();
        $request[$this->getRequestType()]['LineItem']=$this->AddItems($order_items);
       
         return $request;
        
    }
        
     public function AddItems($items){
              
        $query_items=array();       
        $i=1; /* lineItemNumber */
        foreach($items as $key=>$item){                              
            /* $key - quote_item_id */
            $tmp_item=array('lineItemNumber'=>$i,'lineItemId'=>$key, 'locationCode'=>$this->getLocationCode(),
                 'Seller'=> array (
                     'Company'=> $this->getCompanyId(),
                     'PhysicalOrigin'=>array(
                        'StreetAddress1'=>$this->getData('company_street_1'),
                        'StreetAddress2'=>$this->getData('company_street_2'),
                        'City'=>$this->getCompanyCity(),
                        'MainDivision'=>$this->getCompanyState(),      
                        'PostalCode'=>$this->getCompanyPostcode())
                     ),  
                 'Customer'=> array('CustomerCode'=>array('classCode'=>$this->getCustomerClass(),'_'=>$this->getCustomerCode()),
                                     'Destination'=>   
                                          array(
                                            'StreetAddress1'=>$this->getCustomerStreet1(),
                                            'StreetAddress2'=>$this->getCustomerStreet2(),
                                            'City'=>$this->getCustomerCity(),
                                            'MainDivision'=>$this->getCustomerRegion(),                                        
                                            'PostalCode'=>$this->getCustomerPostcode())
                                          
                                    ),
                 'Product' => array ('productClass'=>$item['product_class'],'_'=>$item['product_code']),
                 'UnitPrice' => array('_'=>$item['price']),
                 'Quantity' => array('_'=>$item['qty']),
                 'ExtendedPrice'=> array('_'=>$item['extended_price']),
                 ); 
            
            if ($this->getTaxAreaId())
                $tmp_item['Customer']['Destination']['taxAreaId']=$this->getTaxAreaId();
                                   
            
            $query_items[]=$tmp_item;
            $i++;
        }
       return $query_items;
   }
 }