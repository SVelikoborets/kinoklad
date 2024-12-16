<?php
namespace App\Logging;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class TelegramLoggerHandler extends AbstractProcessingHandler
{
    protected $telegramToken;
    protected $chatId;

    public function __construct(array $config, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->telegramToken = $config['telegram_token'];
        $this->chatId = $config['chat_id'];

        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $message = $record['message'];

        $response = Http::post("https://api.telegram.org/bot{$this->telegramToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to send Telegram notification: ' . $response->body());
        }
    }
}
