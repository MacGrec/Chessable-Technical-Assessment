<?php


namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BranchesControllerTest extends WebTestCase
{
    const CODE_400_MESSAGE_SELLER_NOT_EXIST = '{"code":400,"message":"Seller not exist"}';
    const BRANCH_NAME = "Sucursal Madagascar";

    public function testCreateBranchSuccessful()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            'api/branches/add',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{
                "name": "'. self::BRANCH_NAME .'",
                "location": {
                    "address": "calle una misma",
                    "postal_code": 11170,
                    "country": "Madagascar",
                    "province": "Selva Norte"
                }    
            }'
        );

        $json_response = json_decode($client->getResponse()->getContent());
        $this->assertSame(Response::HTTP_OK,$client->getResponse()->getStatusCode());
        $this->assertIsInt($json_response->id);
        $this->assertIsString($json_response->name);
        $this->assertSame(self::BRANCH_NAME,$json_response->name);
    }

    public function testCreateBranchFail()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/branches/add',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{
                "name": "'. self::BRANCH_NAME .'"    
            }'
        );

        $error_message = "Field address missed";
        $json_response = json_decode($client->getResponse()->getContent());
        $this->assertSame(Response::HTTP_BAD_REQUEST,$client->getResponse()->getStatusCode());
        $this->assertIsInt($json_response->code);
        $this->assertSame(Response::HTTP_BAD_REQUEST,$json_response->code);
        $this->assertIsString($json_response->message);
        $this->assertSame($error_message,$json_response->message);
    }
}