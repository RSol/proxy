<?php

namespace app\commands;

use app\components\SpysOneParser;
use app\services\ProxyService;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\console\ExitCode;

class ProxyController extends Controller
{
    /**
     * Parse proxies from http://spys.one/free-proxy-list/ALL/
     * @return int Exit code
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        /** @var SpysOneParser $parser */
        $parser = Yii::$app->spysOneParser;
        if ($count = (new ProxyService())->saveParsed($parser->parse())) {
            echo "Add {$count} proxies\n";
        } else {
            echo "No one new proxies\n";
        }

        return ExitCode::OK;
    }
}
