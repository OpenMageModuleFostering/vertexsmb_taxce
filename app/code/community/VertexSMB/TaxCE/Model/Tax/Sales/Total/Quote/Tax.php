<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_Tax_Sales_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{

    /**
     * Collect tax totals for quote address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Tax_Model_Sales_Total_Quote
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (! Mage::helper('taxce')->isVertexSMBActive()) {
            parent::collect($address);
            return $this;
        }
        $addressType = $address->getAddressType();
        
        if ($address->getQuote()->isVirtual() && $addressType == 'shipping') {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address Type: " . $addressType . ", Order is virtual.", null, 'vertexsmb.log');
            return $this;
        }
        if (! $address->getQuote()->isVirtual() && ! $address->getShippingMethod()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Order is not virtual and doesnt have shipping method.", null, 'vertexsmb.log');
            return $this;
        }
        
        if (! $address->getStreet1() && ! Mage::helper('taxce')->allowCartQuote()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Street not specified.", null, 'vertexsmb.log');
            return $this;
        }
        
        if (! $address->getCountryId() || ! $address->getRegion() || ! $address->getPostcode() || ! count($address->getAllNonNominalItems())) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent. Address not specified. ", null, 'vertexsmb.log');
            return $this;
        }
        
        if (Mage::app()->getRequest()->getControllerName() == 'cart' && ! Mage::helper('taxce')->allowCartQuote()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request was not sent.It is not allowed in the quote ", null, 'vertexsmb.log');
            return $this;
        }
        
        if (! Mage::helper('taxce')->canQuoteTax()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            Mage::log("Quote request not have enought information", null, 'vertexsmb.log');
            return $this;
        }
        
        Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
        $this->_roundingDeltas = array();
        $this->_baseRoundingDeltas = array();
        $this->_hiddenTaxes = array();
        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);
        
        $this->_store = $address->getQuote()->getStore();
        $customer = $address->getQuote()->getCustomer();
        if ($customer) {
            $this->_calculator->setCustomer($customer);
        }
        
        if (! $address->getAppliedTaxesReset()) {
            $address->setAppliedTaxes(array());
        }
        
        $items = $this->_getAddressItems($address);
        if (! count($items)) {
            return $this;
        }
        
        $request = new Varien_Object();
        
        if ($this->_config->priceIncludesTax($this->_store)) {
            $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests($this->_calculator->getRateOriginRequest($this->_store), $request);
        }
        
        $itemsVertexTaxes = Mage::helper('taxce')->taxQuoteItems($address);
        $request->setItemsVertexTax($itemsVertexTaxes);
        
        $this->_rowBaseCalculation($address, $request);
        $this->_addAmount($address->getExtraTaxAmount());
        $this->_addBaseAmount($address->getBaseExtraTaxAmount());
        $this->_calculateShippingTax($address, $request);
        
        $this->_processHiddenTaxes();
        $this->_roundTotals($address);
        return $this;
    }
 
    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param unknown                        $taxRateRequest
     * @return VertexSMB_TaxCE_Model_Tax_Sales_Total_Quote_Tax
     */
    protected function _rowBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (! Mage::helper('taxce')->isVertexSMBActive()) {
            parent::_rowBaseCalculation($address, $taxRateRequest);
            return $this;
        }
        
        $items = $this->_getAddressItems($address);
        $itemTaxGroups = array();
        
        $itemsVertexTaxes = $taxRateRequest->getItemsVertexTax();
        
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId(
                        $child->getProduct()
                            ->getTaxClassId()
                    );
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcRowTaxAmount($child, $itemsVertexTaxes);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }
                    $this->_saveAppliedTaxes($address, $applied, $child->getTaxAmount(), $child->getBaseTaxAmount(), $rate);
                    $child->setTaxRates($applied);
                }
                $this->_recalculateParent($item);
            } else {
                $taxRateRequest->setProductClassId(
                    $item->getProduct()
                        ->getTaxClassId()
                );
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcRowTaxAmount($item, $itemsVertexTaxes);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }
                $this->_saveAppliedTaxes($address, $applied, $item->getTaxAmount(), $item->getBaseTaxAmount(), $rate);
                $item->setTaxRates($applied);
            }
        }
        
        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

 
    /**
     * @param unknown $item
     * @param unknown $rate
     * @param string  $taxGroups
     * @param string  $taxId
     * @param string  $recalculateRowTotalInclTax
     * @return VertexSMB_TaxCE_Model_Tax_Sales_Total_Quote_Tax
     */
    protected function _calcRowTaxAmount($item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false)
    {
        if (! Mage::helper('taxce')->isVertexSMBActive()) {
            parent::_calcRowTaxAmount($item, $rate);
            return $this;
        }
        
        $subtotal = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal = $baseTaxSubtotal = $item->getBaseTaxableAmount();
        $rowTax = 0;
        $baseRowTax = 0;
        $taxRate = 0;
        $itemTax = $rate[$item->getId()];
        if ($itemTax instanceof Varien_Object) {
            $rowTax = $itemTax->getTaxAmount();
            $baseRowTax = $itemTax->getBaseTaxAmount();
            $taxRate = $itemTax->getTaxPercent();
        } else {
            Mage::log("ItemTax is not instance of Varien_Object. ", null, 'vertexsmb.log');
        }
            
        $item->setTaxPercent($taxRate);
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));
        $rowTotalInclTax = $item->getRowTotalInclTax();
        
        if (! isset($rowTotalInclTax)) {
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
 
    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param unknown                        $taxRateRequest
     * @return VertexSMB_TaxCE_Model_Tax_Sales_Total_Quote_Tax
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (! Mage::helper('taxce')->isVertexSMBActive()) {
            parent::_calculateShippingTax($address, $taxRateRequest);
            return $this;
        }
        
        $itemsVertexTaxes = $taxRateRequest->getItemsVertexTax();
        
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        $tax = 0;
        $baseTax = 0;
        $rate = 0;
        $shippingTax = 0;
        
        if (is_array($itemsVertexTaxes) && array_key_exists('shipping', $itemsVertexTaxes)) {
            $shippingTax = $itemsVertexTaxes['shipping'];
        }
        
        Mage::log($itemsVertexTaxes, null, 'vertexsmb.log');
        
        if ($shippingTax instanceof Varien_Object) {
            $tax = $shippingTax->getTaxAmount();
            $baseTax = $shippingTax->getBaseTaxAmount();
            $rate = $shippingTax->getTaxPercent();
        } else {
            Mage::log("calculateShippingTax::shippingTax is not instance of Varien_Object. ", null, 'vertexsmb.log');
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
