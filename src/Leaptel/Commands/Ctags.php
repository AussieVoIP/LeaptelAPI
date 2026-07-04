<?php

namespace Leaptel\Commands;

use Leaptel\RawDB;
use Illuminate\Console\Command;
use Leaptel\Models\CTagsMap;
use Leaptel\Models\NBNLVC;

class Ctags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaptel:ctags {--lvcid=} {--action=} {--eth=} {--type=} {--custid=} {--start=} {--end=} {--count=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add/remove/assign ctag maps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->option("action");
        if (!$action) {
            print "Need an action - did you forget 'create'?\n";
            exit;
        }
        if ($action == "summary") {
            print "Summary of ctags\n";
            return;
        }

        $lvcid = $this->option("lvcid");
        if (!$lvcid) {
            throw new \Exception("No LVCID provided");
        }
        $lvc = NBNLVC::where("lvc_id", $lvcid)->first();
        if (!$lvc) {
            throw new \Exception("No LVC ID $lvcid found in db");
        }

        print "Hi $lvcid - I want to $action\n";
        print json_encode($lvc) . "\n";
        $summary = CTagsMap::getCtagSummary($lvcid);

        $start = $this->option('start');
        $end = $this->option('end');
        $count = $this->option('count');

        $custid = $this->option('custid');
        if (!$custid) {
            $custid = 1;
        }

        if (!$end) {
            if (!$count) {
                throw new \Exception("Must provide end or count");
            }
            // 100 + 10 == 110, which is 11. Take one off.
            $end = $start + $count - 1;
        }

        if ($action == "create") {
            $eth = $this->option('eth');
            if (!$eth) {
                throw new \Exception("Must provide eth name on l2 router");
            }
            $type = $this->option("type");
            if (!$type) {
                throw new \Exception("Provide a type - either ipoe or pppoe");
            }
            print "Creating ctags between $start and $end using eth $eth\n";
            $pdo = RawDB::getPdo();
            if ($type == "ipoe") {
                $q = $pdo->prepare("insert into ctagmaps (lvc_id, ctag, desc, ipoe, customer_id) values (:lvcid, :ctag, :desc, 1, :custid)");
            } elseif ($type == "pppoe") {
                $q = $pdo->prepare("insert into ctagmaps (lvc_id, ctag, desc, pppoe, customer_id) values (:lvcid, :ctag, :desc, 1, :custid)");
            } else {
                throw new \Exception("Unknown type $type");
            }

            $ptr = $start;
            while ($ptr <= $end) {
                if (!empty($summary["ctags"][$ptr])) {
                    $current = $summary["ctags"][$ptr];
                    if ($current["customer_id"] == $custid) {
                        print "CTAG $ptr in $lvcid already exists for custid $custid, skipping\n";
                        $ptr++;
                        continue;
                    }
                    print json_encode($current) . "\n";
                    throw new \Exception("ctag $ptr already exists");
                }
                $params = [
                    "lvcid" => $lvcid,
                    "ctag" => $ptr,
                    "custid" => $custid,
                    "desc" => $eth,
                ];
                $q->execute($params);
                $ptr++;
            }
            return;
        }

        if ($action == "delete") {
            print "Deleting ctags\n";
            return;
        }

        if ($action == "assign") {
            print "Assigning ctags between $start and $end to $custid\n";
            $pdo = RawDB::getPdo();
            $q = $pdo->prepare("update ctagmaps set customer_id=:cid where lvc_id=:lvcid and ctag=:ctag");
            $params = ["lvcid" => $lvcid, "cid" => $custid];
            $ptr = $start;
            while ($ptr <= $end) {
                $current = $summary["ctags"][$ptr] ?? [];
                if (!$current) {
                    print "$ptr does not exist\n";
                    exit;
                }
                if ($current['customer_id'] == $custid) {
                    print "CTAG $ptr already assigned to $custid\n";
                    $ptr++;
                    continue;
                }

                if ($current['customer_id'] !== 1) {
                    print "CTAG $ptr not assigned to custid 1\n";
                    exit;
                }
                $params['ctag'] = $ptr;
                print json_encode($params) . "\n";
                $q->execute($params);
                $ptr++;
            }
            var_dump($summary);
            return;
        }
    }
}
