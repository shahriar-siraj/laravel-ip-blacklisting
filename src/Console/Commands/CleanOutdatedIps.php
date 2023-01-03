<?php

namespace ShahriarSiraj\LaravelIpBlacklisting\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ShahriarSiraj\LaravelIpBlacklisting\Models\BlacklistedIp;

class CleanOutdatedIps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ip-blacklisting:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean outdated blacklisted IPs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cleanedIps = [];
        $blacklistedIps = BlacklistedIp::where('duration_in_minutes', '!=', 0)->whereNotNull('created_at')->get();

        foreach ($blacklistedIps as $blacklistedIp) {
            $now = Carbon::now();
            $blacklistedAt = Carbon::parse($blacklistedIp->created_at);
            $diffInMinutes = $blacklistedAt->diffInMinutes($now);

            if ($diffInMinutes > $blacklistedIp->duration_in_minutes) {
                $blacklistedIp->delete();

                $cleanedIps[] = [
                    $blacklistedIp->ip,
                    $blacklistedIp->created_at
                ];
            }
        }


        if (count($cleanedIps) === 0) {
            $message = 'No outdated IP addresses have been found';
            $this->info($message);
        }

        if (count($cleanedIps) > 0) {
            $message = sprintf('%d outdated IPs have been cleaned successfully', count($cleanedIps));

            $this->info($message);
            $this->newLine();
            $this->table(
                ['Cleaned IP Address', 'Blacklisted At'],
                $cleanedIps
            );
        }

        $cleanedIpAddresses = array_map(function ($item) {
            return $item[0];
        }, $cleanedIps);
        Log::channel('ip_blacklisting')->debug($message, $cleanedIpAddresses);

        return 0;
    }
}
