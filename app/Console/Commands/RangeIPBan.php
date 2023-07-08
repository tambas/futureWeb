<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\BannedIP;

class RangeIPBan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'range_ip_ban';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shadow Ban Amazon and OVH IP ranges';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $amazonIPRanges = $this->getAmazonIPRanges();
        $OVHIPRanges = $this->getOVHIPRanges();

        foreach ($amazonIPRanges as $range) {
            $bannedRange = new BannedIP;
            $bannedRange->description = "Amazon IP";
            $bannedRange->begin       = $range[0];
            $bannedRange->end         = $range[1];

            echo "{$bannedRange->description}\t{$bannedRange->begin}\t{$bannedRange->end}\n";

            $bannedRange->save();
        }

        foreach ($OVHIPRanges as $range) {
            $bannedRange = new BannedIP;
            $bannedRange->description = "OVH IP";
            $bannedRange->begin       = $range[0];
            $bannedRange->end         = $range[1];

            echo "{$bannedRange->description}\t{$bannedRange->begin}\t{$bannedRange->end}\n";

            $bannedRange->save();
        }
    }

    private function getAmazonIPRanges()
    {
        $json = json_decode(file_get_contents("https://ip-ranges.amazonaws.com/ip-ranges.json"));
        $ranges = [];

        foreach ($json->prefixes as $prefixe) {
            $range = $this->cidrToRange($prefixe->ip_prefix);

            if ($range[0] && $range[1] && $range[0] != "0.0.0.0" && $range[1] != "0.0.0.0") {
                $ranges[] = $range;
            }
        }

        return $ranges;
    }

    private function getOVHIPRanges()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/AS16276#blocks");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36');

        $html = curl_exec($curl);

        curl_close($curl);

        preg_match_all('/<td><a href=".*">(.*)<\/a><\/td>/', $html, $matches);

        foreach ($matches[1] as $cidr) {
            $range = $this->cidrToRange($cidr);

            if ($range[0] && $range[1] && $range[0] != "0.0.0.0" && $range[1] != "0.0.0.0") {
                $ranges[] = $range;
            }
        }

        return $ranges;
    }

    private function cidrToRange($cidr)
    {
        $range = [];
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range[1] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        return $range;
    }
}
