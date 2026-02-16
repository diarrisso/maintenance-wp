<?php

namespace App\Console\Commands;

use App\Models\MaintenanceReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ArchiveOldReports extends Command
{
    protected $signature = 'reports:archive-old';

    protected $description = 'Archive sent reports older than 1 week';

    public function handle(): int
    {
        try {
            $cutoff = Carbon::now()->subWeek();

            $count = MaintenanceReport::where('status', 'sent')
                ->where('sent_at', '<=', $cutoff)
                ->update(['status' => 'archived']);

            Log::info("reports:archive-old: {$count} report(s) archived.");
            $this->info("Archived {$count} report(s) older than 1 week.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error("reports:archive-old failed: {$e->getMessage()}");
            $this->error("Failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
