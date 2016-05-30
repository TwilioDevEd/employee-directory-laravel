<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectorySearchTest extends TestCase
{
    use DatabaseTransactions;

    public function testEmployeeNotFound()
    {
        $response = $this->call(
            'POST',
            '/directory/search/',
            ['Body' => 'Yyy']
        );
        $twilioResponse = new SimpleXMLElement($response->getContent());
        
        $this->assertEquals('We did not find the employee you\'re looking for',
            strval($twilioResponse->Message));
    }

    public function testOneEmployeeFound()
    {
        $response = $this->call(
            'POST',
            '/directory/search/',
            ['Body' => 'Wolverine']
        );
        $twilioResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
"Wolverine
+14155559718
Wolverine@heroes.example.com",
            strval($twilioResponse->Message));
    }

    public function testMultipleEmployeesFound()
    {
        $response = $this->call(
            'POST',
            '/directory/search/',
            ['Body' => 'Thor']
        );
        $twilioResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
"We found multiple people, reply with:
1 for Thor Girl
2 for Frog Thor
3 for Thor
Or start over",
            strval($twilioResponse->Message));
    }
}
