<?php

namespace app\controllers;

use app\components\SpysOneParser;
use app\services\ProxyService;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Yii;
use app\models\ProxySearch;
use yii\base\InvalidConfigException;
use yii\web\Controller;

/**
 * ProxyController implements the CRUD actions for Proxy model.
 */
class ProxyController extends Controller
{

    /**
     * Lists all Proxy models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProxySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Proxy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var SpysOneParser $parser */
        $parser = Yii::$app->spysOneParser;
        if ($count = (new ProxyService())->saveParsed($parser->parse())) {
            Yii::$app->session->setFlash('success', "Add {$count} proxies");
        } else {
            Yii::$app->session->setFlash('info', "No one new proxies");
        }
        return $this->redirect(['index']);
    }
}
