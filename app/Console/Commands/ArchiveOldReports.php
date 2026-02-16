<?php

namespace App\Console\Commands;

use App\Models\MaintenanceReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveOldReports extends Command
{
    protected $signature = 'reports:archive-old';

    protected $description = 'Archive sent reports older than 1 week';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subWeek();

        $count = MaintenanceReport::where('status', 'sent')
            ->where('sent_at', '<=', $cutoff)
            ->update(['status' => 'archived']);

        $this->info("Archived {$count} report(s) older than 1 week.");

        return self::SUCCESS;
    }
}
