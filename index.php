<?php
require __DIR__ . '/vendor/autoload.php';

use Pecee\SimpleRouter\SimpleRouter;
use eftec\bladeone\BladeOne;
use XBase\TableReader;
use Pecee\Http\Url;
use Pecee\Http\Response;
use Pecee\Http\Request;
use XBase\TableEditor;

class Pagination
{
    public static function getNext($page)
    {
        return Helper::url(null, null, ['page' => $page + 1]);
    }

    public static function getPrevious($page)
    {
        return Helper::url(null, null, ['page' => $page - 1]);
    }
}

class Helper
{
    /**
     * Get url for a route by using either name/alias, class or method name.
     *
     * The name parameter supports the following values:
     * - Route name
     * - Controller/resource name (with or without method)
     * - Controller class name
     *
     * When searching for controller/resource by name, you can use this syntax "route.name@method".
     * You can also use the same syntax when searching for a specific controller-class "MyController@home".
     * If no arguments is specified, it will return the url for the current loaded route.
     *
     * @param string|null $name
     * @param string|array|null $parameters
     * @param array|null $getParams
     * @return \Pecee\Http\Url
     * @throws \InvalidArgumentException
     */
    public static function url(?string $name = null, $parameters = null, ?array $getParams = null): Url
    {
        return SimpleRouter::getUrl($name, $parameters, $getParams);
    }

    /**
     * @return \Pecee\Http\Response
     */
    public static function response(): Response
    {
        return SimpleRouter::response();
    }

    /**
     * @return \Pecee\Http\Request
     */
    public static function request(): Request
    {
        return SimpleRouter::request();
    }

    /**
     * Get input class
     * @param string|null $index Parameter index name
     * @param string|mixed|null $defaultValue Default return value
     * @param array ...$methods Default methods
     * @return \Pecee\Http\Input\InputHandler|array|string|null
     */
    public static function input($index = null, $defaultValue = null, ...$methods)
    {
        if ($index !== null) {
            return Helper::request()->getInputHandler()->value($index, $defaultValue, ...$methods);
        }

        return Helper::request()->getInputHandler();
    }

    /**
     * @param string $url
     * @param int|null $code
     */
    public static function redirect(string $url, ?int $code = null): void
    {
        if ($code !== null) {
            Helper::response()->httpCode($code);
        }

        Helper::response()->redirect($url);
    }

    /**
     * Get current csrf-token
     * @return string|null
     */
    public static function csrf_token(): ?string
    {
        $baseVerifier = SimpleRouter::router()->getCsrfVerifier();
        if ($baseVerifier !== null) {
            return $baseVerifier->getTokenProvider()->getToken();
        }

        return null;
    }
}

function getBlade()
{
    $views = __DIR__ . '/views';
    $cache = __DIR__ . '/cache';
    $blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);
    return $blade;
}

SimpleRouter::get("/dbf", function () {
    return getBlade()->run('index', []);
});

SimpleRouter::get("/dbf/view/{file}", function ($file) {
    $max = (int) isset($_GET['max']) ? $_GET['max'] : 10;
    $page = (int) isset($_GET['page']) ? $_GET['page'] : 0;
    $table = new TableReader(base64_decode(preg_replace("/[.=]{1,2}$/", '', $file)));
    $cols = $table->getColumns();
    $rows = [];
    $amountSoFar = 0;
    $added = 0;
    $recordAmount = $table->getRecordCount();
    while ($record = $table->nextRecord()) {
        if ($max > -1) {
            if ($added >= $max) {
                break;
            }
        }

        if (($page * $max) <= $amountSoFar) {
            $cur = [];
            foreach ($cols as $c) {
                $cur = array_merge($cur, [
                    $c->getName() => $record->get($c->getName()),
                    'position' => $record->getRecordIndex()
                ]);
            }
            array_push($rows, $cur);
            $added++;
        }
        $amountSoFar++;
    }

    $maxPage = floor($recordAmount / $max);
    return getBlade()->run('view', [
        'maxPage' => $maxPage,
        'cols' => $cols,
        'page' => $page,
        'rows' => $rows,
        'file' => $file,
    ]);
})->name('view.table');

SimpleRouter::get('/dbf/view/{file}/{position}', function ($file, $position) {
    $table = new TableReader(base64_decode(preg_replace("/[.=]{1,2}$/", '', $file)));
    $record  = $table->pickRecord((int) $position);
    $cols = $table->getColumns();
    return getBlade()->run('record.view', [
        'record' => $record,
        'file' => $file,
        'cols' => $cols,
    ]);
})->name('view.record');

SimpleRouter::post('/dbf/view/{file}/{position}', function ($file, $position) {
    $table = new TableEditor(base64_decode(preg_replace("/[.=]{1,2}$/", '', $file)));
    $record  = $table->pickRecord((int) $position);
    $cols = $table->getColumns();
    foreach ($cols as $c) {
        $v = Helper::input($c);
        $record = $record->set(trim($c->getName()), $v);
    }
    $table
        ->writeRecord($record)
        ->save()
        ->close();
    return Helper::redirect(Helper::url('view.record', ['file' => $file, 'position' => $position]), 302);
})->name('update.record');


SimpleRouter::start();
