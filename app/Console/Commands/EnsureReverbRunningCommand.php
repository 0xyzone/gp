<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnsureReverbRunningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reverb:ensure-running';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if the Laravel Reverb WebSocket server is running, and starts it in the background if not.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $host = config('reverb.servers.reverb.host', '0.0.0.0');
        $port = (int) config('reverb.servers.reverb.port', 8080);

        // If configured as 0.0.0.0, connect to 127.0.0.1 for the health check
        $checkHost = ($host === '0.0.0.0') ? '127.0.0.1' : $host;

        $connection = @fsockopen($checkHost, $port, $errno, $errstr, 1.5);

        if (is_resource($connection)) {
            fclose($connection);
            $this->info("Reverb is already running and reachable at {$checkHost}:{$port}.");

            return self::SUCCESS;
        }

        $this->warn("Reverb is not responding on {$checkHost}:{$port}. Spawning background Reverb process...");

        $phpBinary = PHP_BINARY;
        $artisanPath = base_path('artisan');

        if (DIRECTORY_SEPARATOR === '\\') {
            // Windows
            pclose(popen("start /B {$phpBinary} {$artisanPath} reverb:start > NUL 2>&1", 'r'));
        } else {
            // Linux / macOS / cPanel shared hosting
            $logFile = storage_path('logs/reverb.log');
            exec("nohup {$phpBinary} {$artisanPath} reverb:start > {$logFile} 2>&1 &");
        }

        $this->info('Reverb start command dispatched in the background.');

        return self::SUCCESS;
    }
}
