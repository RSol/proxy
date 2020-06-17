<?php

namespace app\services;

use app\components\ProxyData;
use app\models\Proxy;

class ProxyService
{
    /**
     * @param ProxyData[] $proxies
     * @return int
     */
    public function saveParsed($proxies)
    {
        $count = 0;
        foreach ($proxies as $proxy) {
            $count += (int)$this->saveOne($proxy);
        }
        return $count;
    }

    /**
     * @param ProxyData $proxy
     * @return bool
     */
    public function saveOne($proxy)
    {
        if ($this->isExists($proxy)) {
            return false;
        }
        return (new Proxy($proxy->toArray()))->save(false);
    }

    /**
     * @param ProxyData $proxy
     * @return bool
     */
    private function isExists($proxy)
    {
        return Proxy::find()
            ->where([
                'address' => $proxy->address,
                'port' => $proxy->port,
                'type' => $proxy->type,
            ])
            ->exists();
    }
}
