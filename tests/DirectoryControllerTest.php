<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Collection;

class DirectoryControllerTest extends TestCase
{

    public function testEmployeeNotFound()
    {
        $response = $this->call(
            'POST',
            '/directory/search/',
            ['Body' => 'Yyy']
        );
        $twilioResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
            'We did not find the employee you\'re looking for',
            strval($twilioResponse->Message)
        );
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
            "Wolverine\n"
                . "+14155559718\n"
                . "Wolverine@heroes.example.com",
            strval($twilioResponse->Message)
        );
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
            "We found multiple people, reply with:\n"
                . "1 for Thor Girl\n"
                . "2 for Frog Thor\n"
                . "3 for Thor\n"
                . "Or start over",
            strval($twilioResponse->Message)
        );
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
            "Thor\n+14155559999\nthor@asgard.example.com",
            strval($twilioResponse->Message)
        );
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
            "X-51\n+14155550804\nX-51@heroes.example.com",
            strval($twilioResponse->Message)
        );
    }
}
