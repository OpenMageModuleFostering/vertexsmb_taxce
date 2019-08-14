<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_Resource_TaxRequest extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('taxce/taxrequest', 'request_id');
    }
}
