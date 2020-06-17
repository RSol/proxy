<?php


namespace app\components;


use app\models\Proxy;
use app\services\ProxyCountryService;

class ProxyData
{
    public $address;
    public $port;
    public $type;
    public $anonymity;
    public $country;

    public function toArray()
    {
        return [
            'address' => $this->address,
            'port' => $this->port,
            'type' => $this->type,
            'anonymity' => Proxy::getAnonymityByLabel($this->anonymity),
            'country_id' => (new ProxyCountryService())->getCountryByName($this->country),
        ];
    }
}
