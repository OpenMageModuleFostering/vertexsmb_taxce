<?php

/**
 * @category    VertexSMB
 * @package     VertexSMB_TaxCE
 * @author      Alex Lukyanau <alukyanau@netatwork.com>
 * @license     http://opensource.org/licenses/OSL-3.0  The Open Software License 3.0 (OSL 3.0)
 * @link        http://www.magentocommerce.com/magento-connect/sales-tax-extension-for-vertex-smb-1.html
 */
class VertexSMB_TaxCE_Model_VertexSMB extends Mage_Core_Model_Abstract
{

    /**
     * @return VertexSMB_TaxCE_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('taxce');
    }

 
    /**
     * @param unknown $request
     * @param unknown $type
     * @param string  $order
     * @return Exception|unknown
     */
    public function sendApiRequest($request, $type, $order = null)
    {
        $objectId = null;
        if (strpos($type, 'invoice') === 0) {
            $objectId = $order->getId();
        } elseif ($type == 'quote')
            $objectId = $this->getHelper()
                ->getSession()
                ->getQuote()
                ->getId();
        elseif ($type == 'tax_area_lookup') {
            $objectId = 0;
            if (is_object(
                $this->getHelper()
                    ->getSession()
                    ->getQuote()
            )) {
                $objectId = $this->getHelper()
                    ->getSession()
                    ->getQuote()
                    ->getId();
            }
        }
        try {
            $apiUrl = $this->getHelper()->getVertexHost();
            if ($type == 'tax_area_lookup') {
                $apiUrl = $this->getHelper()->getVertexAddressHost();
            }
            
            $client = new SoapClient(
                $apiUrl,
                array(
                'connection_timeout' => 300,
                'trace' => true,
                'soap_version' => SOAP_1_1
                )
            );
            
            if ($type == 'tax_area_lookup') {
                $taxRequestResult = $client->LookupTaxAreas60($request);
            } else {
                $taxRequestResult = $client->calculateTax60($request);
            }
        } catch (Exception $e) {
            if ($client instanceof SoapClient) {
                $this->logRequest($type, $objectId, $client->__getLastRequest(), $client->__getLastResponse());
            } else {
                $this->logRequest($type, $objectId, $e->getMessage(), $e->getMessage());
            }
            return $e;
        }
        $totalTax = 0;
        $taxAreaId = 0;
        if (strpos($type, 'invoice') === 0) {
            $totalTax = $taxRequestResult->InvoiceResponse->TotalTax->_;
            $lineItem = $taxRequestResult->InvoiceResponse->LineItem;
            if (is_array($lineItem)) {
                $taxAreaId = $lineItem[0]->Customer->Destination->taxAreaId;
            } else {
                $taxAreaId = $lineItem->Customer->Destination->taxAreaId;
            }
        } elseif ($type == 'quote') {
            $totalTax = $taxRequestResult->QuotationResponse->TotalTax->_;
            $lineItem = $taxRequestResult->QuotationResponse->LineItem;
            if (is_array($lineItem)) {
                $taxAreaId = $lineItem[0]->Customer->Destination->taxAreaId;
            } else {
                $taxAreaId = $lineItem->Customer->Destination->taxAreaId;
            }
        } elseif ($type == 'tax_area_lookup') {
            $taxAreaResults = $taxRequestResult->TaxAreaResponse->TaxAreaResult;
            if (is_array($taxAreaResults)) {
                $taxAreaResIds = array();
                foreach ($taxAreaResults as $taxAreaResult) {
                    $taxAreaResIds[] = $taxAreaResult->taxAreaId;
                }
                $taxAreaId = implode(',', $taxAreaResIds);
            } else {
                $taxAreaId = $taxAreaResults->taxAreaId;
            }
        }
        
        $this->logRequest($type, $objectId, $client->__getLastRequest(), $client->__getLastResponse(), $totalTax, $taxAreaId);
        
        return $taxRequestResult;
    }
 
    /**
     * @param unknown $type
     * @param unknown $objectId
     * @param unknown $requestXml
     * @param unknown $responseXml
     * @param number  $totalTax
     * @param number  $taxAreaId
     * @return void
     */
    public function logRequest($type, $objectId, $requestXml, $responseXml, $totalTax = 0, $taxAreaId = 0)
    {
        $taxRequest = Mage::getModel('taxce/taxRequest');
        $taxRequest->setRequestType($type)->setRequestDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
        if (strpos($type, 'invoice') === 0) {
            $taxRequest->setOrderId($objectId);
        } elseif ($type == 'quote' || $type = 'tax_area_lookup')
            $taxRequest->setQuoteId($objectId);
        
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($requestXml);
        $dom->formatOutput = true;
        if ($dom->saveXml()) {
            $requestXml = $dom->saveXml();
        }
        
        $dom->loadXML($responseXml);
        
        $dom->formatOutput = true;
        if ($dom->saveXml()) {
            $responseXml = $dom->saveXml();
        }
        
        $totalNode = $dom->getElementsByTagName('Total');
        $subtotalNode = $dom->getElementsByTagName('SubTotal');
        $lookupResultNode = $dom->getElementsByTagName('Status');
        $addressLookupFaultNode = $dom->getElementsByTagName('exceptionType');
        $total = 0;
        $subtotal = 0;
        $lookupResult = "";
        
        if ($totalNode->length > 0) {
            $total = $totalNode->item(0)->nodeValue;
        }
        
        if ($subtotalNode->length > 0) {
            $subtotal = $subtotalNode->item(0)->nodeValue;
        }
        
        if ($lookupResultNode->length > 0) {
            $lookupResult = $lookupResultNode->item(0)->getAttribute('lookupResult');
        }
        
        if (! $lookupResult && $addressLookupFaultNode->length > 0) {
            $lookupResult = $addressLookupFaultNode->item(0)->nodeValue;
        }
        
        $sourcePath = $this->getHelper()->getSourcePath();
        $taxRequest->setSourcePath($sourcePath);
        $taxRequest->setTotalTax($totalTax);
        $taxRequest->setRequestXml($requestXml);
        $taxRequest->setResponseXml($responseXml);
        $taxRequest->setTaxAreaId($taxAreaId);
        $taxRequest->setTotal($total);
        $taxRequest->setSubTotal($subtotal);
        $taxRequest->setLookupResult($lookupResult);
        $taxRequest->save();
        /** Mage::log($taxRequest->getData(), null, 'vertexsmb.log'); */
    }
}
