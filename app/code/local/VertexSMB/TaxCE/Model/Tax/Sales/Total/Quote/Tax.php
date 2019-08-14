<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 **/
 
class VertexSMB_TaxCE_Model_Tax_Sales_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax {                  
     
    /**
     * Collect tax totals for quote address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {                         
        if (!Mage::helper('taxce')->IsVertexSMBActive()) {
            parent::collect($address);
            return $this; 
        }            
        /*Limit tax quote amounts*/                
       $address_type=$address->getAddressType(); 
                

       if ($address->getQuote()->isVirtual() && $address_type=='shipping'){
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address Type: ".$address_type.", Order is virtual. ", null, 'vertexsmb.log');
           return $this;
       }               
       if (!$address->getQuote()->isVirtual() && !$address->getShippingMethod()) {
           Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
           Mage::log("Quote request was not sent. Order is not virtual and doesnt have shipping address. ", null, 'vertexsmb.log');
           return $this;
       }
      /* Commented before */
       /*if($address_type!=$based_on  && ($address_type=='billing' && !$address->getQuote()->isVirtual())  ){
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            return $this;
       }     */       
       if ( !$address->getStreet1() && !Mage::helper('taxce')->AllowCartQuote()  ) {
           Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
           Mage::log("Quote request was not sent. Address Street not specified. ", null, 'vertexsmb.log');
           return $this;           
       }
       
       if (!$address->getCountryId() || !$address->getRegionId() || !$address->getPostcode()  || !count($address->getAllNonNominalItems()))  {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address not specified. ", null, 'vertexsmb.log');
           return $this;
       }    
       
       
       
       if (!$address->getTaxAreaId() && !Mage::helper('taxce')->AllowCartQuote() )  {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address area id not specified yet. ", null, 'vertexsmb.log');
           return $this;
       } 
                               
        /*Vertex SMB */   
        if (!Mage::helper('taxce')->CanQuoteTax()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request not have enought information", null, 'vertexsmb.log');
            return $this;
        }           
        /*Vertex SMB*/         
        
        Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
        $this->_roundingDeltas      = array();
        $this->_baseRoundingDeltas  = array();
        $this->_hiddenTaxes         = array();
        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);

        $this->_store = $address->getQuote()->getStore();
        $customer = $address->getQuote()->getCustomer();
        if ($customer) {
            $this->_calculator->setCustomer($customer);
        }
            
        if (!$address->getAppliedTaxesReset()) {
            $address->setAppliedTaxes(array());
        }

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }
               
       $request = new Varien_Object();
       /* Removed to not match any of rules */
     /*  $request->setCountryId($address->getCountryId())
           ->setRegionId($address->getRegionId())
           ->setPostcode($address->getPostcode())
           ->setStore($address->getQuote()->getStore())
           ->setCustomerClassId($address->getQuote()->getCustomerTaxClassId());
           */           
        if ($this->_config->priceIncludesTax($this->_store)) {
            $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests(
                $this->_calculator->getRateOriginRequest($this->_store),
                $request
            );
        }
        
        /*beta*/
        $itemsVertexTaxes=Mage::helper('taxce')->TaxQuoteItems($address);    
        $request->setItemsVertexTax($itemsVertexTaxes);
        /*beta*/        
        
        $this->_rowBaseCalculation($address, $request);                                    
        $this->_addAmount($address->getExtraTaxAmount());
        $this->_addBaseAmount($address->getBaseExtraTaxAmount());
        /* Shipping Tax */            
        $this->_calculateShippingTax($address,$request);         
        /*  Shipping Tax */
 
        $this->_processHiddenTaxes();
        //round total amounts in address
        $this->_roundTotals($address);
        return $this;
    }

            
        /* (non-PHPdoc)
         * @see Mage_Tax_Model_Sales_Total_Quote_Tax::_rowBaseCalculation()
         */
        protected function _rowBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (!Mage::helper('taxce')->IsVertexSMBActive()) {
            parent::_rowBaseCalculation( $address, $taxRateRequest);
            return $this;
        }
                                    
        $items = $this->_getAddressItems($address);
        $itemTaxGroups  = array();
         /*beta*/
        $itemsVertexTaxes=$taxRateRequest->getItemsVertexTax();
        /*beta*/
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcRowTaxAmount($child, $itemsVertexTaxes);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }
                    $this->_saveAppliedTaxes(
                        $address,
                        $applied,
                        $child->getTaxAmount(),
                        $child->getBaseTaxAmount(),
                        $rate
                    );  
                    $child->setTaxRates($applied);
                }
                $this->_recalculateParent($item);
            }
            else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcRowTaxAmount($item, $itemsVertexTaxes);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }
                $this->_saveAppliedTaxes(
                    $address,
                    $applied,
                    $item->getTaxAmount(),
                    $item->getBaseTaxAmount(),
                    $rate
                );
                $item->setTaxRates($applied);
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }
            
    
 
       /* (non-PHPdoc)
        * @see Mage_Tax_Model_Sales_Total_Quote_Tax::_calcRowTaxAmount()
        */
       protected function _calcRowTaxAmount(  $item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false )
    {
        /* Vertex SMB*/   
        if (!Mage::helper('taxce')->IsVertexSMBActive()) {
            parent::_calcRowTaxAmount($item, $rate);
            return $this;
        }
       
        $subtotal       = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal   = $baseTaxSubtotal = $item->getBaseTaxableAmount(); 
        $rowTax=0;
        $baseRowTax=0;
        $taxRate=0;      
        $ItemTax= $rate[$item->getId()]; 
        if ($ItemTax instanceof Varien_Object ){
            $rowTax=$ItemTax->getTaxAmount();
            $baseRowTax=$ItemTax->getBaseTaxAmount();   
            $taxRate=$ItemTax->getTaxPercent();                         
        } else 
            Mage::log("ItemTax is not instance of Varien_Object. ", null, 'vertexsmb.log');                   
        /* Vertex */             
           
        $item->setTaxPercent($taxRate);     
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));                        
        $rowTotalInclTax = $item->getRowTotalInclTax();
        
        if (!isset($rowTotalInclTax)) {
            $weeeTaxBeforeDiscount = 0;
            $baseWeeeTaxBeforeDiscount = 0;
            
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal + $weeeTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseWeeeTaxBeforeDiscount);
            } else {
                $taxCompensation = $item->getDiscountTaxCompensation() ? $item->getDiscountTaxCompensation() : 0;
                $item->setRowTotalInclTax($subtotal + $rowTax + $taxCompensation);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseRowTax + $item->getBaseDiscountTaxCompensation());
            }
        }

        return $this;
    }
    
    /* (non-PHPdoc)
     * @see Mage_Tax_Model_Sales_Total_Quote_Tax::_calculateShippingTax()
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (!Mage::helper('taxce')->IsVertexSMBActive()) {
            parent::_calculateShippingTax($address, $taxRateRequest);
            return $this;
        }  
         /*beta*/
        $itemsVertexTaxes=$taxRateRequest->getItemsVertexTax();
        /*beta*/
        
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));                                
        $tax=0;
        $baseTax=0;
        $rate=0;    
        $shipping_tax=0;
        
        if (is_array($itemsVertexTaxes) && array_key_exists('shipping',$itemsVertexTaxes))
            $shipping_tax= $itemsVertexTaxes['shipping'] ;
                  
        Mage::log($itemsVertexTaxes, null, 'vertexsmb.log');
        
        if ($shipping_tax instanceof Varien_Object ){
            $tax=$shipping_tax->getTaxAmount();
            $baseTax=$shipping_tax->getBaseTaxAmount();
            $rate=$shipping_tax->getTaxPercent();            
        }else 
            Mage::log("shipping_tax is not instance of Varien_Object. ", null, 'vertexsmb.log'); 
                                                   
        $this->_addAmount(max(0, $tax));
        $this->_addBaseAmount(max(0, $baseTax));            
        $address->setShippingTaxAmount(max(0, $tax));
        $address->setBaseShippingTaxAmount(max(0, $baseTax));
        
         
        
        $applied = $this->_calculator->getAppliedRates($taxRateRequest);
        $this->_saveAppliedTaxes($address, $applied, $tax, $baseTax, $rate);

        return $this;
    }
        
}