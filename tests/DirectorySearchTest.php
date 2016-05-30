<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Collection;

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

    public function testMultipleEmployeesStoreNameOnSession()
    {
        $response = $this->call(
            'POST',
            '/directory/search/',
            ['Body' => 'Thor']
        );

        $expected = new Collection;
        $expected->push('ThorGirl@heroes.example.com');
        $expected->push('FrogThor@heroes.example.com');
        $expected->push('thor@asgard.example.com');

        $this->assertSessionHas('employees', $expected);
    }

    public function testUserChoosesAnOption()
    {
        $employees = new Collection;
        $employees->push('ThorGirl@heroes.example.com');
        $employees->push('FrogThor@heroes.example.com');
        $employees->push('thor@asgard.example.com');

        $response = $this
            ->withSession(['employees' => $employees])
            ->call(
            'POST',
            '/directory/search/',
            ['Body' => '3']
        );

        $twilioResponse = new SimpleXMLElement($response->getContent());
        $this->assertEquals(
"Thor
+14155559999
thor@asgard.example.com",
            strval($twilioResponse->Message));
    }

    public function testUserChoosesInvalidOption()
    {
        $employees = new Collection;
        $employees->push('ThorGirl@heroes.example.com');
        $employees->push('FrogThor@heroes.example.com');
        $employees->push('thor@asgard.example.com');

        $response = $this
            ->withSession(['employees' => $employees])
            ->call(
            'POST',
            '/directory/search/',
            ['Body' => '51']
        );

        $twilioResponse = new SimpleXMLElement($response->getContent());
        $this->assertEquals(
"X-51
+14155550804
X-51@heroes.example.com",
            strval($twilioResponse->Message));
    }
}
