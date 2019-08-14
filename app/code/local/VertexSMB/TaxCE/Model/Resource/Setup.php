<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 **/
 
 
class VertexSMB_TaxCE_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup {

    /**
     * @param unknown $entityTypeId
     * @param unknown $attributeId
     * @return boolean
     */
    public function attributeExists($entityTypeId, $attributeId) {
        try {
            $entityTypeId = $this->getEntityTypeId($entityTypeId);
            $attributeId = $this->getAttributeId($entityTypeId, $attributeId);
            return !empty($attributeId);
        } catch (Exception $e) {
            return FALSE;
        }
    }
}
    