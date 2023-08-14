<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

class BotmanController extends Controller
{

    public function handle(Request $request)
    {
        $config = [
            'facebook' => [
              'token' => 'EAAJwcvgIiHYBOyZBR7u2perrTcLdWaUlKHTO0BBOK10xrLuUFNvTEkfGnJAwLuM8ZA3ZA4UgO4MoHwh3WbaWtepjhmyM4nzNzI4yd8YN9tPmjyFVw1rSybsz6ZCaRpwPaAdhscGUxB0HKk1nUrCwS7P7BPpJ3bjXdeP4vOO2mTlVBCURcOI3B18r7H4mXbYZD',
              'app_secret' => 'f2168a21ca3f76040d58901471a5c660',
              'verification'=>'demo',
          ]
        ];

        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

        // Create an instance
        $botman = BotManFactory::create($config);
  
        $botman->hears('{message}', function($botman, $message) {
  
            if ($message == 'hi') {
                $this->askName($botman);
            }else{
                $botman->reply("write 'hi' for testing...");
            }
  
        });
  
        $botman->listen();
    }

    /**
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
  
            $name = $answer->getText();
  
            $this->say('Nice to meet you '.$name);
        });
    }
    
}
