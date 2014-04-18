<?php

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $camp_id = '1234';
        $to_addr = 'someemail@test.com';
        $trans_id = '123123asdf';

        $pers = array(
            'FirstName' => 'Some',
            'LastName' => 'Guy',
            'Address' => 'New York'
        );

        $client = new \Silverpop\Client(2, true);

        $msg = new \Silverpop\Message($client);

        $msg->setToEmail($to_addr);
        $msg->setCampaignId($camp_id);
        $msg->setTransactionId($trans_id);
        foreach ($pers as $k => $v){
            $msg->addPersonalization($k, $v);
        }

        // we get xml back because we're in debug mode
        $msg_xml = $msg->send();

        echo '<pre>';
        print_r($msg_xml);

        $obj = new \SimpleXMLElement($msg_xml);

        $this->assertInstanceOf('SimpleXMLElement', $obj);
        $this->assertEquals($camp_id, $obj->CAMPAIGN_ID);
        $this->assertEquals($trans_id, $obj->TRANSACTION_ID);
        $this->assertEquals($to_addr, $obj->RECIPIENT->EMAIL);

    }
}