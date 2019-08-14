<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 **/
 
class VertexSMB_TaxCE_Model_Source_Usregions {
    
    protected $_countries;
    protected $_options;
     
    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $countriesArray = Mage::getResourceModel('directory/country_collection')->addCountryIdFilter('US')->load()
                ->toOptionArray(false);
      
            $this->_countries = array();
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = array();
            $regionsCollection = Mage::getResourceModel('directory/region_collection')->addCountryFilter('US')->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, array($this, 'sortRegionCountries'));

            $this->_options = array();
            foreach ($countryRegions as $countryId=>$regions) {
                $regionOptions = array();
                foreach ($regions as $regionId=>$regionName) {
                    $regionOptions[] = array('label'=>$regionName, 'value'=>$regionId);
                }
                $this->_options[] = array('label'=>$this->_countries[$countryId], 'value'=>$regionOptions);
            }
        }
        $options = $this->_options;         

        return $options;
    }
    
    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }
    
}