<?php

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $camp_id = '45262855';
        $to_addr = 'ashwinnn@gmail.com';
        $trans_id = '03172014131';

        $pers = array(
            'FirstName' => 'Ashwin',
            'LastName' => 'Surajbali',
            'CustomerOrderDate' => '04/07/2014',
            'MenuPlanName' => 'Chef Select Menu',
            'ShipmentAddressStreet' => '1313 Mockingbird Lane',
            'ShipmentAddressCity' => 'New York',
            'ShipmentAddressState' => 'NY',
            'ShipmentAddressPostalCode' => '12344',
            'ExpectedDeliveryDate' => '04/16/14',
            'UPSTrackingNumber' => 'UPSTRACKINGNM',
            'ReorderDate' => '04/20/1234',
            'CustomerOrderId' => '1233'
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