<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\ScanResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunNucleiScan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 min per scan

    public function __construct(public Domain $domain) {}

    public function handle()
    {
        $output = shell_exec("nuclei -u {$this->domain->url} -json -silent 2>/dev/null");

        if (!$output) return;

        // Nuclei outputs one JSON object per line
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            if (empty($line)) continue;

            $result = json_decode($line, true);
            if (!$result) continue;

            ScanResult::create([
                'domain_id'     => $this->domain->id,
                'severity'      => $result['info']['severity'] ?? null,
                'template_id'   => $result['template-id'] ?? null,
                'template_name' => $result['info']['name'] ?? null,
                'matched_at'    => $result['matched-at'] ?? null,
                'raw'           => $result,
            ]);
        }
    }
}