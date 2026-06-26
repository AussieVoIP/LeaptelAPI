<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Models\NBNLVC;

class LVCAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaptel:lvcadd {--lvcid=} {--name=} {--stag=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a L2 LVC. LVCID Format is LVC00000000xxxx Use leaptel:lvcmodify to assign the lvc to POIs';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $lvcid = $this->option("lvcid");
        if (!$lvcid) {
            throw new \Exception("No LVCID provided");
        }
        if (!preg_match('/^LVC0+(\d+)$/', $lvcid, $out)) {
            print "LVCID Does not match LVC\d+ format\n";
            exit;
        }
        $tag = $out[1];

        $lvc = NBNLVC::where("lvc_id", $lvcid)->first();
        if ($lvc) {
            print json_encode($lvc) . "\n";
            throw new \Exception("LVC ID $lvcid already exists");
        }
        $name = $this->option("name");
        if (!$name) {
            throw new \Exception("Provide a name");
            $name = "LVC ID $lvcid";
        }
        $stag = (int) $this->option("stag");
        if (!$stag) {
            throw new \Exception("Provide a S_TAG for the VLAN");
        }
        $lvc = new NBNLVC(["lvc_id" => $lvcid]);
        $lvc->lvc_name = $name;
        $lvc->s_tag = $stag;
        $lvc->save();
        print "Created LVC $lvcid\n";
        $result = $lvc->getDetails();
        print join("\n", $result) . "\n";
    }
}
