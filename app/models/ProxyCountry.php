<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "proxy_country".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property Proxy[] $proxies
 */
class ProxyCountry extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proxy_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Proxies]].
     *
     * @return ActiveQuery|ProxyQuery
     */
    public function getProxies()
    {
        return $this->hasMany(Proxy::class, ['country_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ProxyCountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProxyCountryQuery(static::class);
    }
}
