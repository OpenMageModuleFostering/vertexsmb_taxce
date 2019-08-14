<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Model_TaxAreaResponse extends Mage_Core_Model_Abstract {

    
    public function parseResponse($response_object){
        if (is_array($response_object->TaxAreaResponse->TaxAreaResult))
            $taxAreaResults=$response_object->TaxAreaResponse->TaxAreaResult;
        else 
            $taxAreaResults[]=$response_object->TaxAreaResponse->TaxAreaResult;
        
        $this->setTaxAreaResults($taxAreaResults);
        $this->setResultsCount(count($taxAreaResults));
        return $this;
    }
    
    public function GetFirstTaxAreaInfo(){
        $TaxAreaFirstResults=$this->getTaxAreaResults();
        $TaxAreaFirstResult=$TaxAreaFirstResults[0]; 
        $TaxAreInfoFirst=new Varien_Object;
        $TaxAreInfoFirst->setTaxAreaId($TaxAreaFirstResult->taxAreaId);
        $TaxAreInfoFirst->setCity($TaxAreaFirstResult->PostalAddress->City);
        return $TaxAreInfoFirst;
    }
}
