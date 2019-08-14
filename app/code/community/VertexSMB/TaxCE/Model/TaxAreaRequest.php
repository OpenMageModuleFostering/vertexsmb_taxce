<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_TaxAreaRequest extends Mage_Core_Model_Abstract
{

    /**
     * @param unknown $address
     * @return VertexSMB_TaxCE_Model_TaxAreaRequest
     */
    public function prepareRequest($address)
    {
        $request = array(
            'Login' => array(
                'TrustedId' => $this->getHelper()->getTrustedId()
            ),
            'TaxAreaRequest' => array(
                'TaxAreaLookup' => array(
                    'PostalAddress' => array(
                        'StreetAddress1' => $address->getStreet1(),
                        'StreetAddress2' => $address->getStreet2(),
                        'City' => $address->getCity(),
                        'MainDivision' => $address->getRegionCode(),
                        'PostalCode' => $address->getPostcode()
                    )
                )
            )
        );
        
        $this->setRequest($request);
        return $this;
    }

    /**
     * @return boolean|Exception
     */
    public function taxAreaLookup()
    {
        if (! $this->getRequest()) {
            Mage::log("Tax area lookup error: request information not exist", null, 'vertexsmb.log');
            return false;
        }
        $requestData = $this->getRequest();
        
        $requestResult = Mage::getModel('taxce/vertexSMB')->sendApiRequest($requestData, 'tax_area_lookup');
        if ($requestResult instanceof Exception) {
            Mage::log("Tax Area Lookup Error: " . $requestResult->getMessage(), null, 'vertexsmb.log');
            return $requestResult;
        }
        
        $response = Mage::getModel('taxce/TaxAreaResponse')->parseResponse($requestResult);
        $response->setRequestCity($requestData['TaxAreaRequest']['TaxAreaLookup']['PostalAddress']['City']);
        $this->setResponse($response);
        
        return $requestResult;
    }

    /**
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('taxce');
    }
}
