<?php

namespace app\services;

use app\models\ProxyCountry;
use yii\helpers\ArrayHelper;

class ProxyCountryService
{
    /**
     * @var array
     */
    private static $list;

    /**
     * @return ProxyCountry[]
     */
    public function getCountriesArray()
    {
        if (!static::$list) {
            static::$list = ArrayHelper::map(ProxyCountry::find()
                ->orderBy([
                    'name' => SORT_ASC
                ])
                ->asArray()
                ->all(),
                'id',
                'name');
        }
        return static::$list;
    }

    /**
     * @param string $name
     * @return int ID
     */
    public function getCountryByName($name)
    {
        $list = array_flip($this->getCountriesArray());
        return array_key_exists($name, $list)
            ? $list[$name]
            : $this->saveOne($name);
    }

    /**
     * @param $name
     * @return int ID
     */
    public function saveOne($name)
    {
        $model = new ProxyCountry();
        $model->name = $name;
        if ($model->save()) {
            static::$list[$model->id] = $name;
            asort(static::$list);
            return $model->id;
        }
        return 0;
    }
}
