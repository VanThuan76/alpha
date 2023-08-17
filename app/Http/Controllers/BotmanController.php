<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Msg;
use App\Models\Service;
use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;


class BotmanController extends Controller
{

    public function handle(Request $request)
    {
        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
        $config = ['facebook' => [
            'token' => config('botman.facebook.token'),
            'app_secret' => config('botman.facebook.app_secret'),
            'verification'=> config('botman.facebook.verification')
            ] 
        ];

        // Create an instance
        $botman = BotManFactory::create($config, new LaravelCache());
        $botman->hears('YOUR_PAYLOAD_TEXT', function (BotMan $botman) {
            $botman->reply("write 'h123i' for testing...");
        });

        $botman->hears('{message}', function($botman, $message) {
            $user = $botman->getUser();
            $id = $user->getId();
            $msg = Msg::where('user_id', $id)->first();
            if (is_null($msg)){
                $msg = new Msg();
                $msg->user_id = $id;
                $msg->first_name = $user->getFirstName();
                $msg->last_name = $user->getLastName();
                $msg->info = implode(',', $user->getInfo());
            }
            //$jsonMessages = json_decode($msg->txt, true);
            $jsonMessages = $msg->txt;
            $jsonMessages[Carbon::now()->format('Y-m-d h:i:s')] = $message;
            $msg->txt = json_encode($jsonMessages);
            $msg->save();

            $botman->reply("Hệ thống trị liệu Sen Tài Thu nằm trong Tập đoàn Sen Tài Thu Việt Nam. Hoạt động về lĩnh vực chăm sóc");
            $botman->startConversation(new OnboardingConversation());
        });
  
        $botman->listen();
    }
}

class OnboardingConversation extends Conversation
{

    public function askForPhoneNumber($user, $message){
        if(preg_match("/^[0-9]{10}+$/", $message)) {
            // $phone is valid
            $id = $user->getId();
            $msg = Msg::where('user_id', $id)->first();
            if (is_null($msg)){
                $msg = new Msg();
                $msg->user_id = $id;
                $msg->first_name = $user->getFirstName();
                $msg->last_name = $user->getLastName();
                $msg->info = implode(',', $user->getInfo());
            }
            //$jsonMessages = json_decode($msg->txt, true);
            $jsonMessages = $msg->txt;
            $jsonMessages[Carbon::now()->format('Y-m-d h:i:s')] = $message;
            $msg->txt = json_encode($jsonMessages);
            $msg->phone_number = $message;
            $msg->save();
    
            $this->say("Cảm ơn bạn, chúng tôi sẽ liên hệ lại sớm. $message");
        } else {
            $this->ask('Số điện thoại không chính xác, vui lòng để lại số điện thoại?', function (Answer $response) {
                $this->askForPhoneNumber($this->bot->getUser(), $response->getText());
            });
        }

    }

    public function askForRequest()
    {
        $question = Question::create('Bạn muốn tìm hiểu về?')
            ->fallback('Unable to create a new database')
            ->callbackId('create_database')
            ->addButtons([
                Button::create('Địa chỉ cơ sở ở đâu')->value('address'),
                Button::create('Tôi muốn xem những gói dịch vụ bên bạn!')->value('services'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
                if ($selectedValue == 'address'){
                    $this->ask('Cơ sở chúng tôi ở xxxx, vui lòng để lại số điện thoại?', function (Answer $response) {
                        $this->askForPhoneNumber($this->bot->getUser(), $response->getText());
                    });
                }
                if ($selectedValue == 'services'){
                    $services = Service::all();
                    $servicesMsg = "";
                    foreach($services as $i => $service){
                        $servicesMsg .= "\n$service->name giá tiền: $service->price";
                    }
                    $this->ask("Dịch vụ chúng tôi là $servicesMsg, vui lòng để lại số điện thoại?", function (Answer $response) {
                        $this->askForPhoneNumber($this->bot->getUser(), $response->getText());
                    });
                }   
            }
        });
    }

    public function run()
    {
        // This will be called immediately
        $this->askForRequest();
    }
}
