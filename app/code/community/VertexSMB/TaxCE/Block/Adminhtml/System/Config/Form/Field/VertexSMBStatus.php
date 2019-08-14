<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Block_Adminhtml_System_Config_Form_Field_VertexSMBStatus extends Mage_Adminhtml_Block_System_Config_Form_Field
{

 
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('taxce');
        if (! $helper->isVertexSMBActive()) {
            $status = "Disabled";
            $state = "critical";
        } else {
            $status = $helper->checkCredentials();
            if ($status == 'Valid') {
                $state = "notice";
            } else {
                $state = "minor";
            }
        }
        
        return '<span class="grid-severity-' . $state . '"><span style=" background-color: #FAFAFA;">' . $status . '</span></span>';
    }
}
