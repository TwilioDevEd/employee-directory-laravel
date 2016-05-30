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

        $this->assertEquals('Wolverine\n+14155559718\nWolverine@heroes.example.com',
            strval($twilioResponse->Message));
    }
}
