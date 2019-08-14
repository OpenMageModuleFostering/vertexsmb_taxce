<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Model_TaxQuoteResponse extends Mage_Core_Model_Abstract {

    
    public function parseResponse($response_object){
        if (is_array($response_object->QuotationResponse->LineItem))
            $TaxLineItems=$response_object->QuotationResponse->LineItem;
        else 
            $TaxLineItems[]=$response_object->QuotationResponse->LineItem;
        
        $this->setTaxLineItems($TaxLineItems);
        $this->setLineItemsCount(count($TaxLineItems));
        $this->prepareQuoteTaxedItems($TaxLineItems);
        
        return $this;
    }
    
    public function prepareQuoteTaxedItems($items_tax){
        $quote_taxed_items=array();
        
        foreach ($items_tax as $item) {             
            $lineItemNumber=$item->lineItemNumber;
            $ItemTotaltax=$item->TotalTax->_;    
            /* SUMM Percents For Jurisdictions */
            $TaxPercent=0;
            foreach ($item->Taxes  as $key=>$tax_value)                 
                if (is_object($tax_value) && property_exists($tax_value, "EffectiveRate"))                
                    $TaxPercent+=(float)$tax_value->EffectiveRate;   
                elseif ($key=="EffectiveRate")
                    $TaxPercent+=(float)$tax_value;                     
                
                
            $TaxPercent=$TaxPercent*100;     
 
            $quoite_item_id=$item->lineItemId;  
            $TaxItemInfo=new Varien_Object;
            $TaxItemInfo->setProductClass($item->Product->productClass);
            $TaxItemInfo->setProductSku($item->Product->_);
            $TaxItemInfo->setProductQty($item->Quantity->_);
            $TaxItemInfo->setUnitPrice($item->UnitPrice->_);
            $TaxItemInfo->setTaxPercent($TaxPercent);
            $TaxItemInfo->setBaseTaxAmount($ItemTotaltax);
            $TaxItemInfo->setTaxAmount($ItemTotaltax);
            $quote_taxed_items[$quoite_item_id]=$TaxItemInfo;
        }
        
        $this->setQuoteTaxedItems($quote_taxed_items);
    }
}
