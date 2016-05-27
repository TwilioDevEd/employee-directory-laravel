<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectorySearchTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
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
}
