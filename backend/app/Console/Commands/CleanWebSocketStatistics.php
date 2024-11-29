<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry;

class CleanWebSocketStatistics extends Command
{
    protected $signature = 'websockets:clean-statistics';
    protected $description = 'Clean old WebSocket statistics entries';

    public function handle()
    {
        $daysToKeep = config('websockets.statistics.delete_statistics_older_than_days', 60);
        
        $deletedCount = WebSocketsStatisticsEntry::where('created_at', '<', now()->subDays($daysToKeep))->delete();
        
        $this->info("Deleted {$deletedCount} old WebSocket statistics entries.");
    }
}
