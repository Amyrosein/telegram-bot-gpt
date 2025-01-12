<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $lastUpdateId = null;

    $updates = Telegram::getUpdates();

    foreach ($updates as $update) {
        $lastUpdateId = $update['update_id'];

        $message   = $update['message']['text'] ?? null;
        $chatId    = $update['message']['chat']['id'] ?? null;
        $firstName = $update['message']['from']['first_name'] ?? 'User';

        if ( ! $message || ! $chatId) {
            continue; // Skip invalid updates
        }

        // Delegate logic to command handlers
        if ($message === '/start') {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text'    => "سلام {$firstName} به ربات خوش آمدید",
            ]);
        } else {
            $client   = new \GuzzleHttp\Client();
            $response = $client->post('https://api.groq.com/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer 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',
                    'Content-Type: application/json',
                ],
                'json'    => [
                    'model'    => 'llama-3.3-70b-versatile',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $message,
                        ]
                    ] ,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            $responseMessage = $result['choices'][0]['message']['content'];

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text'    => $responseMessage,
            ]);
        }
    }


    if ($lastUpdateId) {
        Telegram::getUpdates(['offset' => $lastUpdateId + 1]);
    }

    return true;
});
