<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_Source_Taxinvoice
{

    protected $_options;

    /**
     * @return multitype:string NULL
     */
    public function toOptionArray()
    {
        if (! $this->_options) {
            $this->_options[] = array(
                'label' => Mage::helper('taxce')->__("When Invoice Created"),
                'value' => 'invoice_created'
            );
            $this->_options[] = array(
                'label' => Mage::helper('taxce')->__("When Order Status Is"),
                'value' => 'order_status'
            );
        }
        $options = $this->_options;
        return $options;
    }
}
