<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */
 
class VertexSMB_TaxCE_Block_Adminhtml_System_Config_Form_Field_VertexSMBStatus extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {        
        $helper=Mage::helper('taxce');
        if (!$helper->IsVertexSMBActive()) {  
           $status="Disabled";
           $state="critical";  
        }else{
          $status=$helper->CheckCredentials();
          if ($status=='Valid') 
              $state="notice";
          else
              $state="minor";                   
        }
                       
        return '<span class="grid-severity-'.$state.'"><span style=" background-color: #FAFAFA;">'.$status.'</span></span>';
    }
}
 
