<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Block_Adminhtml_System_Config_Form_Field_ShippingCodes extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('taxce');
        $html = '<table cellspacing="0" class="form-list"><colgroup class="label"></colgroup><colgroup class="value"></colgroup><tbody>';
        $html .= '<tr><td class="label">Shipping Method</td><td class="value">Product Code</td></tr>';
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach ($methods as $cCode => $_carrier) {
            $_methodOptions = array();
            if ($_methods = $_carrier->getAllowedMethods()) {
                if (! $_title = Mage::getStoreConfig("carriers/$cCode/title")) {
                    $_title = $cCode;
                }
                $html .= '<tr><td class="label"   colspan="2"><b>' . $_title . '</b></td></tr>';
                foreach ($_methods as $_mcode => $_method) {
                    $_code = $cCode . '_' . $_mcode;
                    $_methodOptions[] = array(
                        'value' => $_code,
                        'label' => $_method
                    );
                }
                $html .= '<tr><td class="label">' . $_method . ': </td><td class="value"> ' . $_code . '</td></tr>';
            }
            if ($cCode == 'ups' && $ups = Mage::getSingleton('usa/shipping_carrier_ups')) {
                foreach ($ups->getCode('method') as $k => $v) {
                    $html .= '<tr><td class="label">' . Mage::helper('usa')->__($v) . ': </td><td class="value"> ' . $cCode . '_' . $k . '</td></tr>';
                }
            }
            
            if ($cCode == 'usps' && $usps = Mage::getSingleton('usa/shipping_carrier_usps')) {
                foreach ($usps->getCode('method') as $k => $v) {
                    $html .= '<tr><td class="label">' . $usps->getMethodLabel($v) . ': </td><td class="value"> ' . $cCode . '_' . $k . '</td></tr>';
                }
            }
            
            if ($cCode == 'fedex' && $fedex = Mage::getSingleton('usa/shipping_carrier_fedex')) {
                foreach ($fedex->getCode('method') as $k => $v) {
                    $html .= '<tr><td class="label">' . $v . ': </td><td class="value"> ' . $cCode . '_' . $k . '</td></tr>';
                }
            }
            
            if ($cCode == 'dhl' && $dhl = Mage::getSingleton('usa/shipping_carrier_dhl')) {
                foreach ($dhl->getCode('service') as $k => $v) {
                    $html .= '<tr><td class="label">' . $v . ': </td><td class="value"> ' . $cCode . '_' . $k . '</td></tr>';
                }
            }
            
            if ($cCode == 'dhlint' && $dhlint = Mage::getSingleton('usa/shipping_carrier_dhl_international')) {
                foreach ($dhlint->getDhlProducts($this->_contentType) as $k => $v) {
                    $html .= '<tr><td class="label">' . $v . ': </td><td class="value"> ' . $cCode . '_' . $k . '</td></tr>';
                }
            }
        }
        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        $html = '<td>';
        $html .= $this->_getElementHtml($element);
        $html .= '</td>';
        return $this->_decorateRowHtml($element, $html);
    }
}
