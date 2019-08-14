<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Model_VertexSMB extends Mage_Core_Model_Abstract { 
      
   public function getHelper() {
        return Mage::helper('taxce');
    }

    
   /**
    * @param unknown $request
    * @param string $order
    * @param unknown $type
    * @return Exception|unknown
    */
   public function SendApiRequest($request,$order=null, $type) {
         
        $object_id=null;
        if (strpos($type,'invoice')===0)
            $object_id=$order->getId();
        elseif ($type=='quote')             
            $object_id=$this->getHelper()->getSession()->getQuote()->getId();
       elseif ($type=='tax_area_lookup'){
               $object_id=0;
               if (is_object($this->getHelper()->getSession()->getQuote()))
                    $object_id=$this->getHelper()->getSession()->getQuote()->getId();
       }
        try {
            $api_url=$this->getHelper()->getVertexHost();
            if ($type=='tax_area_lookup')
                $api_url=$this->getHelper()->getVertexAddressHost();
            
            $client = new SoapClient($api_url, array('connection_timeout' => 300,'trace' => true, 'soap_version' => SOAP_1_1));
            
            if ($type=='tax_area_lookup')
                $tax_request_result = $client->LookupTaxAreas60($request);
            else 
                $tax_request_result = $client->calculateTax60($request);
        } catch (Exception $e) { 
            if ($client instanceof SoapClient)
                $this->LogRequest($type,$object_id, $client->__getLastRequest(), $client->__getLastResponse());
            else
                $this->LogRequest($type,$object_id, $e->getMessage (), $e->getMessage ());
         return $e;
        }
        $Total_Tax=0;
        $tax_area_id=0;
        if (strpos($type,'invoice')===0){
            $Total_Tax=$tax_request_result->InvoiceResponse->TotalTax->_;
            $line_item=$tax_request_result->InvoiceResponse->LineItem;
            if (is_array($line_item))
               $tax_area_id=$line_item[0]->Customer->Destination->taxAreaId;                       
            else 
                $tax_area_id=$line_item->Customer->Destination->taxAreaId;  
        } elseif ($type=='quote') {
            $Total_Tax=$tax_request_result->QuotationResponse->TotalTax->_;
            $line_item=$tax_request_result->QuotationResponse->LineItem;
            if (is_array($line_item))
               $tax_area_id=$line_item[0]->Customer->Destination->taxAreaId;                       
            else 
                $tax_area_id=$line_item->Customer->Destination->taxAreaId;   
        }elseif ($type=='tax_area_lookup') {
            $tax_area_results=$tax_request_result->TaxAreaResponse->TaxAreaResult;
            if (is_array($tax_area_results)) {
                $tax_area_res_ids=array();
                foreach ($tax_area_results as $tax_area_res) {
                    $tax_area_res_ids[]=$tax_area_res->taxAreaId;
                }
                $tax_area_id=implode(',',$tax_area_res_ids);
            }else 
                $tax_area_id=$tax_area_results->taxAreaId; 
        }
                         
        $this->LogRequest($type, $object_id, $client->__getLastRequest(), $client->__getLastResponse(),$Total_Tax,$tax_area_id);
    
        return $tax_request_result;
        
   }
   
   /* Log Save Logic*/
   /* @todo expand logs and add cron job for cleaning*/
   public function LogRequest($type,$object_id,$request_xml,$response_xml, $total_tax=0, $tax_area_id=0) {
            $taxrequest = Mage::getModel('taxce/taxRequest');
            $taxrequest->setRequestType($type)->setRequestDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
            if (strpos($type,'invoice')===0)
                $taxrequest->setOrderId($object_id);
            elseif ($type=='quote' || $type='tax_area_lookup')
                 $taxrequest->setQuoteId($object_id); 
                            
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($request_xml);
            $dom->formatOutput = TRUE;
            if ($dom->saveXml())
                $request_xml=$dom->saveXml();
                         
            $dom->loadXML($response_xml);
           
            $dom->formatOutput = TRUE;
            if ($dom->saveXml())
                $response_xml=$dom->saveXml();                       
            
            $TotalNode = $dom->getElementsByTagName('Total');
            $SubTotalNode = $dom->getElementsByTagName('SubTotal');
            $LookupResultNode = $dom->getElementsByTagName('Status');
            $AddressLookupFaultNode = $dom->getElementsByTagName('exceptionType');
            $Total=0;
            $SubTotal=0;
            $LookupResult="";
            
            if ($TotalNode->length>0)
                $Total=$TotalNode->item(0)->nodeValue;

            if ($SubTotalNode->length>0)
                $SubTotal=$SubTotalNode->item(0)->nodeValue;
                        
            if ($LookupResultNode->length>0)
                $LookupResult=$LookupResultNode->item(0)->getAttribute('lookupResult');
            
            if (!$LookupResult && $AddressLookupFaultNode->length>0)
                $LookupResult=$AddressLookupFaultNode->item(0)->nodeValue;         
             
            $source_path=$this->getHelper()->getSourcePath();
            $taxrequest->setSourcePath($source_path);
            $taxrequest->setTotalTax($total_tax);
            $taxrequest->setRequestXml($request_xml);
            $taxrequest->setResponseXml($response_xml);   
            $taxrequest->setTaxAreaId($tax_area_id); 
            $taxrequest->setTotal($Total);
            $taxrequest->setSubTotal($SubTotal);
            $taxrequest->setLookupResult($LookupResult);
            $taxrequest->save();          
            Mage::log($taxrequest->getData(), null, 'vertexsmb.log');
        }

    }


