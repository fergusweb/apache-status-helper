<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use App\Services\ApacheStatusService;

/**
 * Command
 */
class FetchApacheStatus extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apache:fetch-status {url*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and count unique client IPs from Apache server-status';

    /**
     * Service class
     *
     * @var App\Services\ApacheStatusService
     */
    protected $apacheStatusService;

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'url' => 'Enter the Apache URL to look for (eg, https://server.you.com/server-status)'
        ];
    }

    /**
     * Constructor
     *
     * @param ApacheStatusService $apacheStatusService Service class injection
     */
    public function __construct(ApacheStatusService $apacheStatusService)
    {
        parent::__construct();
        $this->apacheStatusService = $apacheStatusService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $urls = $this->argument('url');
            if ($urls) {
                $this->apacheStatusService->setUrls($urls);
            }

            foreach ($urls as $url) {
                $this->comment("Server: $url");
            }

            // Use the service to fetch and count IPs
            $ip_count = $this->apacheStatusService->fetchAndCountIPs();

            // Output the unique IPs and their counts
            foreach ($ip_count as $ip => $count) {
                $this->info("$ip: $count");
            }
            $this->comment("Command finished...");
            return 0;

        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }


        // Set the URL of the server-status page
        //$url = 'https://server.tailored.com.au/server-status';

        try {
            foreach ($urls as $url) {
                // Fetch the server-status page
                $response = Http::timeout(5)->get($url);

                if ($response->failed()) {
                    $this->error("Failed to fetch: $url");
                    return 1;
                } else {
                    $this->comment("Fetched: $url");
                }

                // Extract all client IP addresses using regex
                $ip_regex = '/\d+\.\d+\.\d+\.\d+/';
                preg_match_all($ip_regex, $response->body(), $matches);

                $ips = $matches[0]; // The matched IP addresses

                // Count occurrences of each unique IP address
                $ip_count = array_count_values($ips);

                // Output the unique IPs and their counts
                foreach ($ip_count as $ip => $count) {
                    $this->info("$ip: $count");
                }
            }
            $this->comment("Command finished...");
            return 0;

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            return 1;
        }
    }
}
