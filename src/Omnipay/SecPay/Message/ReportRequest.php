<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Report Request
 */
class ReportRequest extends AbstractRequest
{
    protected $method = 'getReport';

    public function getData()
    {
        $requestData                    = $this->createBasicDataStructure();

        /**
         * Options for 'report_type' are as follows:
         * - CSV
         * - CSV-ALL
         * - CSV-Summary
         * - CSV-Detail
         * - CSV-Five
         * - Summary
         * - Statement
         * - Origin-Statement
         * - XML-Report
         */
        $requestData['report_type']     = $this->getReportType();

        $requestData['cond_type']       = $this->getCondType(); // Date Batch TransId

        /**
         * condition
         * By Date:
         *     2013 : All transactions for 2013
         *     201304-201305 : All transactions for April and May 2013
         *     201304- : All transactions from April 2013 to present
         * By Transaction:
         *     Use the full transaction ID, or use % to do wildcard searches, but provide at least the first four
         *         characters of the TransID
         *     For range searches, use starting ID ~ (tilde) ending ID [e.g. 12345678~12349999]
         * By Batch
         *    Use the full batch number, or dash-separated numbers for a range
         */
        $requestData['condition']       = $this->getCondition();

        $requestData['currency']        = $this->getCurrency();
        $requestData['predicate']       = ''; // 'Advanced users only'
        $requestData['html']            = $this->getHtml();
        $requestData['showErrs']        = $this->getShowErrs();

        return $requestData;
    }
}
