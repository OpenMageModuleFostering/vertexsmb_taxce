<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_TaxAreaResponse extends Mage_Core_Model_Abstract
{

    /**
     * @param unknown $responseObject
     * @return VertexSMB_TaxCE_Model_TaxAreaResponse
     */
    public function parseResponse($responseObject)
    {
        if (is_array($responseObject->TaxAreaResponse->TaxAreaResult)) {
            $taxAreaResults = $responseObject->TaxAreaResponse->TaxAreaResult;
        } else {
            $taxAreaResults[] = $responseObject->TaxAreaResponse->TaxAreaResult;
        }
        
        $this->setTaxAreaResults($taxAreaResults);
        $this->setResultsCount(count($taxAreaResults));
        return $this;
    }

    /**
     * @return Varien_Data_Collection_Element
     */
    public function getFirstTaxAreaInfo()
    {
        $collection = $this->getTaxAreaLocationsCollection();
        return $collection->getFirstItem();
    }

    /**
     * Used for popup window frontend/adminhtml
     *
     * @return Varien_Data_Collection|unknown
     */
    public function getTaxAreaLocationsCollection()
    {
        $taxAreaInfoCollection = new Varien_Data_Collection();
        
        if (! $this->getTaxAreaResults()) {
            return $taxAreaInfoCollection;
        }
        
        $taxAreaResults = $this->getTaxAreaResults();
        
        foreach ($taxAreaResults as $taxResponse) {
            $taxJurisdictions = $taxResponse->Jurisdiction;
            krsort($taxJurisdictions);
            $areaNames = array();
            $areaName = "";
            foreach ($taxJurisdictions as $areaJursdiction) {
                $areaNames[] = $areaJursdiction->_;
            }
            $areaName = ucwords(strtolower(implode(', ', $areaNames)));
            
            $taxAreaInfo = new Varien_Object();
            $taxAreaInfo->setAreaName($areaName);
            $taxAreaInfo->setTaxAreaId($taxResponse->taxAreaId);
            if (property_exists($taxResponse, "PostalAddress")) {
                $taxAreaInfo->setTaxAreaCity($taxResponse->PostalAddress->City);
            } else {
                $taxAreaInfo->setTaxAreaCity($this->getRequestCity());
            }
            
            $taxAreaInfo->setRequestCity($this->getRequestCity());
            $taxAreaInfoCollection->addItem($taxAreaInfo);
        }
        
        return $taxAreaInfoCollection;
    }
}
