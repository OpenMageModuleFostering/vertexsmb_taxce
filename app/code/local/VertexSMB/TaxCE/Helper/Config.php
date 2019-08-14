<?php
/**
 * @package     VertexSMB_TaxCE
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @author      Alex Lukyanau
 */

class VertexSMB_TaxCE_Helper_Config extends Mage_Core_Helper_Abstract {
    
    const CONFIG_XML_PATH_ENABLE_VERTEX='tax/vertexsmb_settings/enable_vertexsmb';
    const CONFIG_XML_PATH_DEFAULT_TAX_CALCULATION_ADDRESS_TYPE='tax/calculation/based_on';
    const CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE = 'tax/classes/default_customer_code';
    const VERTEX_API_HOST='tax/vertexsmb_settings/api_url';  
    const CONFIG_XML_PATH_VERTEX_API_USER = 'tax/vertexsmb_settings/login';
    const CONFIG_XML_PATH_VERTEX_API_KEY = 'tax/vertexsmb_settings/password';
    const CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID = 'tax/vertexsmb_settings/trustedId';
    const CONFIG_XML_PATH_VERTEX_COMPANY_CODE = 'tax/vertexsmb_seller_info/company';
    const CONFIG_XML_PATH_VERTEX_LOCATION_CODE = 'tax/vertexsmb_seller_info/location_code';
    const CONFIG_XML_PATH_VERTEX_STREET1 = 'tax/vertexsmb_seller_info/streetAddress1';
    const CONFIG_XML_PATH_VERTEX_STREET2 = 'tax/vertexsmb_seller_info/streetAddress2';
    const CONFIG_XML_PATH_VERTEX_CITY = 'tax/vertexsmb_seller_info/city';
    const CONFIG_XML_PATH_VERTEX_COUNTRY = 'tax/vertexsmb_seller_info/country_id';
    const CONFIG_XML_PATH_VERTEX_REGION = 'tax/vertexsmb_seller_info/region_id';
    const CONFIG_XML_PATH_VERTEX_POSTAL_CODE = 'tax/vertexsmb_seller_info/postalCode';   
    const CONFIG_XML_PATH_VERTEX_INVOICE_DATE = 'tax/vertexsmb_settings/invoice_tax_date';    
    const CONFIG_XML_PATH_VERTEX_TRANSACTION_TYPE = 'SALE'; /* SALE,RENTAl,LEASE*/     
    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER = 'tax/vertexsmb_settings/invoice_order';
    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS = 'tax/vertexsmb_settings/invoice_order_status';
    const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';
    const VERTEX_ADDRESS_API_HOST='tax/vertexsmb_settings/address_api_url';      
    const VERTEX_CREDITMEMO_ADJUSTMENT_CLASS='tax/classes/creditmemo_adjustment_class';  
    const VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE='tax/classes/creditmemo_adjustment_negative_code';          
    const VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE='tax/classes/creditmemo_adjustment_positive_code'; 
    const VERTEX_GIFTWRAP_ORDER_CLASS='tax/classes/giftwrap_order_class'; 
    const VERTEX_GIFTWRAP_ORDER_CODE='tax/classes/giftwrap_order_code'; 
    const VERTEX_GIFTWRAP_ITEM_CLASS='tax/classes/giftwrap_item_class'; 
    const VERTEX_GIFTWRAP_ITEM_CODE_PREFIX='tax/classes/giftwrap_item_code'; 
    const VERTEX_PRINTED_GIFTCARD_CLASS='tax/classes/printed_giftcard_class'; 
    const VERTEX_PRINTED_GIFTCARD_CODE='tax/classes/printed_giftcard_code'; 
    const CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE = 'tax/vertexsmb_settings/allow_cart_request';
    const CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON = 'tax/vertexsmb_settings/show_manual_button';
    const CONFIG_XML_PATH_VERTEX_SHOW_POPUP= 'tax/vertexsmb_settings/show_tarequest_popup';
     
    public function getQuoteAllowedControllers(){
        $_quote_allowed_controllers=array('onepage','multishipping','sales_order_create');
        return $_quote_allowed_controllers;
    }
}