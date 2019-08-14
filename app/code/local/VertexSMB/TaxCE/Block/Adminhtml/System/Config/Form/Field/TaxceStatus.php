<?php
  
class VertexSMB_TaxCE_Block_Adminhtml_System_Config_Form_Field_TaxceStatus extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {        
        $helper=Mage::helper('taxce');
        if (!$helper->IsVertexActive()) {  
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
 
