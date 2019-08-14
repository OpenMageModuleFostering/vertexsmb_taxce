<?php
 

class VertexSMB_TaxCE_Model_Tax_Sales_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax {                  
     
    /**
     * Collect tax totals for quote address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {                         
        if (!Mage::helper('taxce')->IsVertexActive()) {
            parent::collect($address);
            return $this; 
        }            
        /*Limit tax quote amounts*/                
       $address_type=$address->getAddressType(); 
                

       if ($address->getQuote()->isVirtual() && $address_type=='shipping'){
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address Type: ".$address_type.", Order is virtual. ", null, 'taxce.log');
           return $this;
       }               
       if (!$address->getQuote()->isVirtual() && !$address->getShippingMethod()) {
           Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
           Mage::log("Quote request was not sent. Order is not virtual and doesnt have shipping address. ", null, 'taxce.log');
           return $this;
       }
      /* Commented before */
       /*if($address_type!=$based_on  && ($address_type=='billing' && !$address->getQuote()->isVirtual())  ){
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            return $this;
       }     */       
       if ( !$address->getStreet1() && !Mage::helper('taxce')->AllowCartQuote()  ) {
           Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
           Mage::log("Quote request was not sent. Address Street not specified. ", null, 'taxce.log');
           return $this;           
       }
       
       if (!$address->getCountryId() || !$address->getRegionId() || !$address->getPostcode()  || !count($address->getAllNonNominalItems()))  {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address not specified. ", null, 'taxce.log');
           return $this;
       }    
       
       if (!$address->getTaxAreaId() && !Mage::helper('taxce')->AllowCartQuote())  {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address area id not specified yet. ", null, 'taxce.log');
           return $this;
       } 
                               
        /*Vertex*/   
        if (!Mage::helper('taxce')->UpdateQuote($address)) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Items Information not changed. ", null, 'taxce.log');
            return $this;
        }           
        /*Vertex*/         
        
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

            
        protected function _rowBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (!Mage::helper('taxce')->IsVertexActive()) {
            parent::_rowBaseCalculation( $address, $taxRateRequest);
            return $this;
        }
                                    
        $items = $this->_getAddressItems($address);
        $itemTaxGroups  = array();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcRowTaxAmount($child, $rate);
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
                $this->_calcRowTaxAmount($item, $rate);
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
            
    
       protected function _calcRowTaxAmount($item, $rate)
    {
        /* Vertex */   
        if (!Mage::helper('taxce')->IsVertexActive()) {
            parent::_calcRowTaxAmount($item, $rate);
            return $this;
        }
       
        $subtotal       = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal   = $baseTaxSubtotal = $item->getBaseTaxableAmount(); 
      
        $ItemTax= Mage::helper('taxce')->getTaxByQuoteItemId($item->getId()); 
        if ($ItemTax instanceof Varien_Object ){
            $rowTax=$ItemTax->getTaxAmount();
            $baseRowTax=$ItemTax->getBaseTaxAmount();   
            $rate=$ItemTax->getTaxPercent();            
             
        }else {
            Mage::log("ItemTax is not instance of Varien_Object. ", null, 'taxce.log');
            $rowTax=0;
            $baseRowTax=0;
            $rate=0;
        }
        /* Vertex */             
           
        $item->setTaxPercent($rate);     
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
    
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (!Mage::helper('taxce')->IsVertexActive()) {
            parent::_calculateShippingTax($address, $taxRateRequest);
            return $this;
        }  
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));                                
        $shipping_tax=Mage::helper('taxce')->getTaxByQuoteItemId('shipping');             
        if ($shipping_tax instanceof Varien_Object ){
            $tax=$shipping_tax->getTaxAmount();
            $baseTax=$shipping_tax->getBaseTaxAmount();
            $rate=$shipping_tax->getTaxPercent();            
        }else {
            Mage::log("shipping_tax is not instance of Varien_Object. ", null, 'taxce.log');
            $tax=0;
            $baseTax=0;
            $rate=0;
        }                                            
        $this->_addAmount(max(0, $tax));
        $this->_addBaseAmount(max(0, $baseTax));            
        $address->setShippingTaxAmount(max(0, $tax));
        $address->setBaseShippingTaxAmount(max(0, $baseTax));
        
         
        
        $applied = $this->_calculator->getAppliedRates($taxRateRequest);
        $this->_saveAppliedTaxes($address, $applied, $tax, $baseTax, $rate);

        return $this;
    }
        
}