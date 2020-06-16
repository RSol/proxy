<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
    const ANONYMITY_APH = 1;
    const ANONYMITY_NOA = 2;
    const ANONYMITY_ANM = 3;
    const ANONYMITY_HIA = 4;
    const ANONYMITY_UNKNOWN = 0;

    public static function getAnonymityList()
    {
        return [
            static::ANONYMITY_APH => 'A+H',
            static::ANONYMITY_NOA => 'NOA',
            static::ANONYMITY_ANM => 'ANM',
            static::ANONYMITY_HIA => 'HIA',
            static::ANONYMITY_UNKNOWN => '-',
        ];
    }

    public function getAnonymityLabel($default = '')
    {
        return ArrayHelper::getValue(static::getAnonymityList(), $this->anonymity, $default);
    }

    public static function getAnonymityByLabel($label)
    {
        return ArrayHelper::getValue(array_flip(static::getAnonymityList()), $label, static::ANONYMITY_UNKNOWN);
    }

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
            [['type'], 'string', 'max' => 20],
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
            'country_id' => 'Country',
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
