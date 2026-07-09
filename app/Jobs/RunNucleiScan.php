<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\ScanResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class RunNucleiScan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 min per scan

    public function __construct(public Domain $domain) {}

    public function handle()
    {
        $url = trim($this->domain->url);

        // Domains are stored as either a bare hostname ("example.com") or a full URL.
        // Reject anything that isn't one of those shapes, and anything starting with
        // "-" (which nuclei's own arg parser would otherwise treat as a flag).
        if (! preg_match('/^(https?:\/\/)?[a-zA-Z0-9]([a-zA-Z0-9\-.]*[a-zA-Z0-9])?(:\d{1,5})?(\/[^\s]*)?$/i', $url)) {
            Log::warning('Skipped Nuclei scan for domain with invalid URL', ['domain_id' => $this->domain->id]);

            return;
        }

        // Pass arguments as an array so they are never interpreted by a shell.
        $result = Process::timeout($this->timeout)->run(['nuclei', '-u', $url, '-json', '-silent']);

        $output = $result->output();

        if (! $output) return;

        // Nuclei outputs one JSON object per line
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            if (empty($line)) continue;

            $decoded = json_decode($line, true);
            if (!$decoded) continue;

            ScanResult::create([
                'domain_id'     => $this->domain->id,
                'severity'      => $decoded['info']['severity'] ?? null,
                'template_id'   => $decoded['template-id'] ?? null,
                'template_name' => $decoded['info']['name'] ?? null,
                'matched_at'    => $decoded['matched-at'] ?? null,
                'raw'           => $decoded,
            ]);
        }
    }
}