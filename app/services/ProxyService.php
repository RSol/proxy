<?php

namespace app\services;

use app\models\Proxy;
use app\models\ProxyCountry;
use yii\helpers\ArrayHelper;

class ProxyService
{
    /**
     * @var array
     */
    private $countries;

    public function __construct()
    {
        $this->countries = ArrayHelper::map(ProxyCountry::find()->all(), 'name', 'id');
    }

    /**
     * @param array $proxies
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
     * @param $proxy
     * @return bool
     */
    private function saveOne($proxy)
    {
        if ($this->isExists($proxy)) {
            return false;
        }
        $proxy['country_id'] = array_key_exists($proxy['country'], $this->countries)
            ? $this->countries[$proxy['country']]
            : $this->addCountry($proxy['country']);
        $proxy['anonymity'] = Proxy::getAnonymityByLabel($proxy['anonymity']);
        unset($proxy['country']);
        $model = new Proxy($proxy);
        return $model->save();
    }

    /**
     * @param $proxy
     * @return bool
     */
    private function isExists($proxy)
    {
        return Proxy::find()
            ->where([
                'address' => $proxy['address'],
                'port' => $proxy['port'],
                'type' => $proxy['type'],
            ])
            ->exists();
    }

    /**
     * @param $name
     * @return int
     */
    private function addCountry($name)
    {
        $model = new ProxyCountry();
        $model->name = $name;
        if ($model->save()) {
            $this->countries[$name] = $model->id;
            return $model->id;
        }
        return 0;
    }
}
