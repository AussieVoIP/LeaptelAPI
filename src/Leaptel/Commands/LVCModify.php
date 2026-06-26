<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Models\NBNLVC;

class LVCModify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaptel:lvcmodify {--lvcid=} {--addpoi=} {--delpoi=} {--desc=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modify a L2 LVC, adding or removing POIs';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $lvcid = $this->option("lvcid");
        print "I want to modify $lvcid\n";
        $lvc = NBNLVC::where("lvc_id", $lvcid)->first();
        if (!$lvc) {
            throw new \Exception("LVC ID $lvcid does not exist");
        }
        $addpoi = strtoupper($this->option("addpoi"));
        if ($addpoi) {
            $key = $pois[$addpoi] ?? null;
            if (!$key) {
                throw new \Exception("Unknown POI $addpoi");
            }
            print "Adding poi $addpoi using $key\n";
            $lvc->{$key} = 1;
            $lvc->save();
        }

        $delpoi = strtoupper($this->option("delpoi"));
        if ($delpoi) {
            $key = $pois[$addpoi] ?? null;
            if (!$key) {
                throw new \Exception("Unknown POI $addpoi");
            }
            print "Removing POI $delpoi using $key\n";
            $lvc->{$key} = null;
            $lvc->save();
        }
        $result = $lvc->getDetails();
        print join("\n", $result) . "\n";
    }
}
