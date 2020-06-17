<?php

use app\models\Proxy;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\services\ProxyCountryService;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProxySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proxies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proxy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Parse Proxies', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'address',
            'port',
            'type',
            [
                'attribute' => 'anonymity',
                'filter' => Proxy::getAnonymityList(),
                'value' => static function(Proxy $model) {
                    return $model->getAnonymityLabel();
                }
            ],
            [
                'attribute' => 'country_id',
                'filter' => (new ProxyCountryService())->getCountriesArray(),
                'value' => static function(Proxy $model) {
                    return $model->country ? $model->country->name : '-';
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
