<a href="https://www.twilio.com">
  <img src="https://static0.twilio.com/marketing/bundles/marketing/img/logos/wordmark-red.svg" alt="Twilio" width="250" />
</a>

# Employee Directory

[![Build Status](https://travis-ci.org/TwilioDevEd/employee-directory-laravel.svg?branch=master)](https://travis-ci.org/TwilioDevEd/employee-directory-laravel)

Use Twilio to accept SMS messages and turn them into queries against a database.
These are example functions on how to implement an Employee Directory, where a mobile
phone user can obtain information by sending a text message with a partial string
of a person's name, and it will return their picture and contact information
(e-mail address and phone number).

## Local Development

This project is build using [Laravel](http://laravel.com/) web framework;

1. First clone this repository and `cd` into it.

   ```bash
   $ git clone git@github.com:TwilioDevEd/employee-directory-laravel.git
   $ cd employee-directory-laravel
   ```

1. Install the dependencies with [Composer](https://getcomposer.org/).

   ```bash
   $ composer install
   ```

1. Generate an `APP_KEY`.

   ```bash
   $ php artisan key:generate
   ```

1. Generate [SQLite](https://www.sqlite.org/) database file.

   ```bash
   $ php TouchDatabase.php
   ```

1. Run the migrations.
   ```bash
   $ php artisan migrate
   ```

1. Seed the database.

   ```bash
   $ php artisan db:seed
   ```

1. Make sure the tests succeed.

   ```bash
   $ ./vendor/bin/phpunit
   ```

1. Start the server.

   ```bash
   $ php artisan serve
   ```

1. Check it out at [http://localhost:8000](http://localhost:8000).

### Expose the Application to the Wider Internet

1. Expose your application to the wider internet using [ngrok](http://ngrok.com). You can click
  [here](#expose-the-application-to-the-wider-internet) for more details. This step
  is important because the application won't work as expected if you run it through
  localhost.

  ```bash
  $ ngrok http 3000
  ```

  Once ngrok is running, open up your browser and go to your ngrok URL. It will
  look something like this: `http://9a159ccf.ngrok.io`

1. Configure Twilio to call your webhooks.

  You will also need to configure Twilio to call your application when calls are received
  on your _Twilio Number_. The **SMS & MMS Request URL** should look something like this:

  ```
  http://<sub-domain>.ngrok.io/directory/search
  ```

  ![Configure SMS](http://howtodocs.s3.amazonaws.com/twilio-number-config-all-med.gif)

### How To Demo

1. Text your twilio number the name "Thor".

1. Should get the following response:

   ```
   We found multiple people, reply with:
   1 for Thor
   2 for Frog Thor
   3 for Thor Girl
   Or start over
   ```

1. Reply with the chosen option, for example "1".

1. Should get the following response:

   ```
   Thor
   +14155559999
   thor@asgard.example.com
   [the image goes here]
   ```

## Meta

* No warranty expressed or implied. Software is as is. Diggity.
* [MIT License](http://www.opensource.org/licenses/mit-license.html)
* Lovingly crafted by Twilio Developer Education.
