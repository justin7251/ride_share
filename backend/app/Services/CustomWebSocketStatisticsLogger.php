<?php
namespace App\Services;

use BeyondCode\LaravelWebSockets\Statistics\Logger\StatisticsLogger;
use BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry;

class CustomWebSocketStatisticsLogger implements StatisticsLogger
{
    public function webSocketConnection(string $appId): void
    {
        $this->saveStatistics($appId, 'connection');
    }

    public function apiMessage(string $appId): void
    {
        $this->saveStatistics($appId, 'api_message');
    }

    public function webSocketMessage(string $appId): void
    {
        $this->saveStatistics($appId, 'websocket_message');
    }

    protected function saveStatistics(string $appId, string $type)
    {
        $entry = WebSocketsStatisticsEntry::where('app_id', $appId)
            ->whereDate('event_time', today())
            ->first();

        if (!$entry) {
            $entry = new WebSocketsStatisticsEntry([
                'app_id' => $appId,
                'event_time' => now()
            ]);
        }

        switch ($type) {
            case 'connection':
                $entry->peak_connection_count++;
                break;
            case 'api_message':
                $entry->api_message_count++;
                break;
            case 'websocket_message':
                $entry->websocket_message_count++;
                break;
        }

        $entry->save();
    }
}
