<?php

namespace Leaptel\Actions;

use Leaptel\API\Customers\GetAllServicesForCustomer;
use Leaptel\API\Service\GetServiceDetails;
use Leaptel\Models\NBNService;

class CleanupServices
{
    private ?array $removedservices = null;

    public function __construct(
        public string $custid
    ) {}

    public function go(bool $dryrun = true): array
    {
        $changed = [];
        $todel = $this->getRemovedServices();
        if ($todel) {
            // We need to delete some records
            foreach ($todel as $s => $sobj) {
                $changed[$s] = $s;
                if ($dryrun) {
                    print "I would delete $s\n";
                } else {
                    $sobj->delete();
                }
            }
        }
        return $changed;
    }

    public function getRemovedServices()
    {
        if ($this->removedservices == null) {
            $known = [];
            foreach (NBNService::where("customer_id", $this->custid)->get() as $record) {
                $known[$record->service_id] = $record;
            }
            $services = (new GetAllServicesForCustomer($this->custid))->go();
            foreach ($services as $id => $s) {
                unset($known[$id]);
            }
            $this->removedservices = $known;
        }
        return $this->removedservices;
    }
}
