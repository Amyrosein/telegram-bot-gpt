<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * Command Name
     */
    protected string $name = 'start';

    /**
     * Command Description
     */
    protected string $description = 'Start Command To Get Started!!';

    /**
     * Handle the command execution
     */
    public function handle()
    {
        // Get the user's first name from the update object
        $fallbackFirstName = $this->getUpdate()->getMessage()->from->first_name;

        Log::info("StartCommand triggered for user: {$fallbackFirstName}");

        // Replace placeholder with the actual first name
        $this->replyWithMessage([
            'text' => "سلام  عزیز{$fallbackFirstName} به ربات خوش آمدید",
        ]);
    }
}