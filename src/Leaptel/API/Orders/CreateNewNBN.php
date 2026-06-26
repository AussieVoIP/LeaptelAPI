<?php

namespace Leaptel\API\Orders;

use App\Models\Product;
use Leaptel\API\APIBase;
use Leaptel\API\Components\NBNPortRecord;
use Leaptel\API\Components\OrderContact;
use Leaptel\API\Request\OrderRequest;
use Leaptel\API\Response\NBNSQResponse;
use Leaptel\API\Response\WholesalerProduct;
use Override;

/** @package Leaptel\API */
class CreateNewNBN extends APIBase
{
    protected string $path = '/orders';
    protected string $custid;
    protected OrderRequest $order;
    protected ?NBNSQResponse $sq = null;
    protected ?WholesalerProduct $plan = null;
    protected ?Product $product = null;
    protected array $avalableports = [];
    protected ?NBNPortRecord $port = null;
    protected ?OrderContact $oc = null;

    public function __construct(string $custid)
    {
        $this->custid = $custid;
        $this->order = new OrderRequest();
        $this->order->customer_id = $this->custid;
        $this->order->carrier = "nbn";
        $this->order->order_type = "data";
        $this->order->connection_type = "new";
    }

    /**
     * Required Location from Service Qualification
     *
     * @param NBNSQResponse $sq
     * @return CreateNewNBN
     */
    public function usingLocation(NBNSQResponse $sq): CreateNewNBN
    {
        $this->sq = $sq;
        $this->order->location_id = $this->sq->location_id;
        // $this->order->ntd_type = $this->sq->ntd_type;
        $this->avalableports = [];
        foreach ($this->sq->ntd_ports as $id => $p) {
            if ($p->Available) {
                $this->avalableports[$id] = $p;
                if ($this->port === null) {
                    $this->port = $p;
                }
            }
        }
        $this->updatePortSettings();
        return $this;
    }

    public function setOrderContact(OrderContact $oc)
    {
        $this->oc = $oc;
        $fields = [
            "contact_first_name",
            "contact_last_name",
            "contact_phone",
            "contact_email",
            "contact_address",
            "contact_suburb",
            "contact_state",
            "contact_postcode"
        ];
        foreach ($fields as $f) {
            $this->order->{$f} = $oc->{$f};
        }
        if (!$this->order->contact_phone && !$this->order->contact_email) {
            throw new \Exception("Need phone or email");
        }
    }

    /** @return array<\Leaptel\API\Models\Component\NBNPortRecord>  */
    public function getAvailablePorts()
    {
        return $this->avalableports;
    }

    /** @return null|NBNPortRecord  */
    public function getSelectedPort(): ?NBNPortRecord
    {
        return $this->port;
    }

    /**
     * Change the port from the default
     *
     * @param string $portname
     * @return CreateNewNBN
     * @throws \Exception
     */
    public function updateSelectedPortByName(string $portname): CreateNewNBN
    {
        if (empty($this->avalableports[$portname])) {
            throw new \Exception("No port name $portname on this device");
        }
        $this->port = $this->avalableports[$portname];
        $this->updatePortSettings();
        return $this;
    }

    /**
     * Called when port changes are done
     *
     * @return void
     */
    private function updatePortSettings()
    {
        $this->order->ntd_port = $this->port->PortNumber;
        $this->order->ntd_id = $this->port->NTDID;
    }

    /**
     * The plan to use for this order
     *
     * @param WholesalerProduct $plan
     * @return CreateNewNBN
     */
    public function usingPlan(WholesalerProduct $plan): CreateNewNBN
    {
        $this->plan = $plan;
        $this->order->product_id = $this->plan->product_id;
        return $this;
    }

    public function usingProduct(Product $product): CreateNewNBN
    {
        $this->product = $product;
        // $this->order->product_id = $this->product->product_id;
        $this->order->plan_id = $this->product->plan_id;
        return $this;
    }

    /**
     * Schedule this order for the future
     *
     * @param \DateTimeInterface $oa
     * @return CreateNewNBN
     */
    public function setOrderAfter(\DateTimeInterface $oa): CreateNewNBN
    {
        /** @var \DateTimeImmutable $oa */
        $melb = $oa->setTimezone(new \DateTimeZone('Australia/Melbourne'));
        $f = $melb->format('Y-m-d\TH:i:s');
        $this->order->order_after = $f;
        return $this;
    }

    public function setAuthDetails(string $username, string $password, string $realm)
    {
        $this->order->username = $username;
        $this->order->password = $password;
        $this->order->realm = $realm;
    }

    /** @return OrderRequest  */
    public function getOrderRequest(): OrderRequest
    {
        return $this->order;
    }

    #[Override]
    public function getFormParams()
    {
        return $this->order->toArray();
    }

    public function go()
    {
        print "Creating an order!\n";
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $params['debug'] = true;
        var_dump($params['form_params']);
        $resp = $c->request('POST', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        var_dump($body);
        exit;
    }
}
