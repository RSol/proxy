<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "proxy".
 *
 * @property int $id
 * @property string|null $address
 * @property int|null $port
 * @property string|null $type
 * @property int|null $anonymity
 * @property int|null $country_id
 *
 * @property ProxyCountry $country
 */
class Proxy extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proxy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['port', 'anonymity', 'country_id'], 'integer'],
            [['address'], 'string', 'max' => 17],
            [['type'], 'string', 'max' => 10],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProxyCountry::class, 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Address',
            'port' => 'Port',
            'type' => 'Type',
            'anonymity' => 'Anonymity',
            'country_id' => 'Country ID',
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return ActiveQuery|ProxyCountryQuery
     */
    public function getCountry()
    {
        return $this->hasOne(ProxyCountry::class, ['id' => 'country_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProxyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProxyQuery(static::class);
    }
}
