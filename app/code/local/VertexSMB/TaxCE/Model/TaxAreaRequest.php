<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Model_TaxAreaRequest extends Mage_Core_Model_Abstract {
          
     public function prepareRequest($address) {
         $request=array(            
         'Login'=>array('TrustedId'=>$this->getHelper()->getTrustedId()),
         'TaxAreaRequest'=>array(              
             'TaxAreaLookup'=> array(
                 'PostalAddress'=>array(
                        'StreetAddress1'=>$address->getStreet1(),
                        'StreetAddress2'=>$address->getStreet2(),
                        'City'=>$address->getCity(),
                        'MainDivision'=>$address->getRegionCode(),   
                        'PostalCode'=>$address->getPostcode(),  
                    )
                 ) 
             )   
         );      
          
       $this->setRequest($request);
       return $this;
     }
     
     public function taxAreaLookup(){
         
        if (!$this->getRequest()) {
            Mage::log("Tax area lookup error: request information not exist", null, 'vertexsmb.log');
            return false;
        }
        
        $request_result=Mage::getModel('taxce/vertexSMB')->SendApiRequest($this->getRequest(),null, 'tax_area_lookup');
        if ($request_result instanceof Exception) {
            Mage::log("Tax Area Lookup Error: ".$request_result->getMessage(), null, 'vertexsmb.log');
            return $request_result;
        }
        
        $response=Mage::getModel('taxce/TaxAreaResponse')->parseResponse($request_result);        
        $this->setResponse($response);
        
        return $request_result;                    
     }
  
    public function getHelper() {
        return Mage::helper('taxce');
    }
    
 }