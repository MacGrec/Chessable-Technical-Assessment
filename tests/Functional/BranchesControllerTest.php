<?php


namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BranchesControllerTest extends WebTestCase
{
    const CODE_400_MESSAGE_SELLER_NOT_EXIST = '{"code":400,"message":"Seller not exist"}';

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
                "name": "Sucursal Madagascar",
                "location": {
                    "address": "calle una misma",
                    "postal_code": 11170,
                    "country": "Madagascar",
                    "province": "Selva Norte"
                }    
            }'
        );

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
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
                "name": "Sucursal Madagascar"    
            }'
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}