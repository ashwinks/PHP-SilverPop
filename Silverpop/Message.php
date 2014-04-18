<?php
namespace Silverpop;

class Message
{
    protected $_to_email;
    protected $_campaign_id;
    protected $_transaction_id;

    protected $_show_all_send_details = 'true';
    protected $_send_as_batch = 'false';
    protected $_no_retry_on_failure = 'false';

    protected $_body_type = 'HTML';

    protected $_personalizations = array();

    protected $_xml;
    protected $_client;

    public function __construct(\Silverpop\Client $client)
    {
        $this->_client = $client;
    }

    public function setToEmail($to_email)
    {
        $this->_to_email = trim($to_email);

        return $this;
    }

    public function setCampaignId($campaign_id)
    {
        $this->_campaign_id = trim($campaign_id);

        return $this;
    }

    public function setTransactionId($transaction_id)
    {
        $this->_transaction_id = trim($transaction_id);

        return $this;
    }

    public function setBodyType($body_type)
    {
        $this->_body_type = trim($body_type);

        return $this;
    }

    public function showAllSendDetails($boolean)
    {
        if (!is_bool($boolean)){
            throw new \InvalidArgumentException('Invalid flag - only true/false is valid');
        }

        if ($boolean === true){
            $this->_show_all_send_details = 'true';
        }else{
            $this->_show_all_send_details = 'false';
        }

        return $this;
    }

    public function sendAsBatch($boolean)
    {
        if (!is_bool($boolean)){
            throw new \InvalidArgumentException('Invalid flag - only true/false is valid');
        }

        if ($boolean === true){
            $this->_send_as_batch = 'true';
        }else{
            $this->_send_as_batch = 'false';
        }

        return $this;
    }

    public function noRetryOnFailure($boolean)
    {
        if (!is_bool($boolean)){
            throw new \InvalidArgumentException('Invalid flag - only true/false is valid');
        }

        if ($boolean === true){
            $this->_no_retry_on_failure = 'true';
        }else{
            $this->_no_retry_on_failure = 'false';
        }

        return $this;
    }

    public function addPersonalization($tag_name, $value)
    {
        $this->_personalizations[$tag_name] = $value;

        return $this;
    }

    public function send()
    {
        if (empty($this->_to_email)){
            throw new \RuntimeException('Invalid to email address');
        }

        if (empty($this->_campaign_id)){
            throw new \RuntimeException('Invalid campaign id');
        }

        if (empty($this->_transaction_id)){
            throw new \RuntimeException('Invalid transaction id');
        }

        $payload = $this->buildXml();
        if ($this->_client->isDebug()){
            return $this->getAssembledXml();
        }

        $response = $this->_client->makeRequest($payload);

        return $response;

    }

    protected function buildXml()
    {
        $xml = new \DOMDocument();
        $root = $xml->appendChild($xml->createElement('XTMAILING'));

        $root->appendChild($xml->createElement('CAMPAIGN_ID', $this->_campaign_id));
        $root->appendChild($xml->createElement('TRANSACTION_ID', $this->_transaction_id));
        $root->appendChild($xml->createElement('SHOW_ALL_DETAILS', $this->_show_all_send_details));
        $root->appendChild($xml->createElement('NO_RETRY_ON_FAILURE', $this->_no_retry_on_failure));

        $recipient = $xml->createElement('RECIPIENT');
        $recipient->appendChild($xml->createElement('EMAIL', $this->_to_email));
        $recipient->appendChild($xml->createElement('BODY_TYPE', $this->_body_type));

        foreach ($this->_personalizations as $tag_name => $value){
            $tmp = $xml->createElement('PERSONALIZATION');
            $tmp->appendChild($xml->createElement('TAG_NAME', $tag_name));
            $tmp->appendChild($xml->createElement('VALUE', $value));
            $recipient->appendChild($tmp);
        }

        $root->appendChild($recipient);

        $this->_xml = $xml->saveXML();

        return $this->_xml;
    }

    public function getAssembledXml()
    {
        return $this->_xml;
    }

}