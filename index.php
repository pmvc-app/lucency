<?php
namespace PMVC\App\lucency;

use PMVC;

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\Lucency';
${_INIT_CONFIG}[_INIT_BUILDER] = $b;

$b->addAction('index');
$b->addAction('view');
$b->addAction('store');
$b->addAction('action');

$b->addForward('view', [ 
    _PATH => 'lucencyView'
    ,_TYPE => 'view'
    ,_ACTION=> 'store'
]);

$b->addForward('action', [ 
    _TYPE => 'view'
    ,_ACTION=> 'store'
]);

// Third party config 
const FB_PIXEL_URL = 'https://www.facebook.com/tr?noscript=1';

class Lucency extends PMVC\Action
{
    static function index ($m, $f)
    {
        return null;
    }

    static function initFbPixel($f)
    {
       ignore_user_abort(true);
       $pixelUrl = FB_PIXEL_URL;
       $pixelUrl = \PMVC\plug('url')->getUrl($pixelUrl);
       $query = $pixelUrl->query;
       $query->id = \PMVC\getOption('fbPixel');
       $query->r = time();
       $query->dl = $f['url'];
       return $pixelUrl;
    }

    static function store ($m, $f)
    {
        $api = \PMVC\getOption('middlewareHost');
        $url = $api. '/lucency/'.\PMVC\plug(_RUN_APP)['type'];
        $env = \PMVC\plug('getenv');
        $request = \PMVC\plug('controller')->getRequest();
        $curl = \PMVC\plug('curl'); 
        $curl->post($url, function($r){
             //\PMVC\d($r->body);
        }, [
            'client'=> array_merge($_REQUEST, \PMVC\get($request)),
            'params'=> [
                'SITE'=> $env->get('SITE'),
            ],
            'server'=>$_SERVER,
        ]);
        $curl->process();
        return;
    }

    static function getBuckets()
    {

    }

    static function view ($m, $f)
    {
       $go = $m['view'];
       $pixelUrl = self::initFbPixel($f);
       $query = $pixelUrl->query;
       $query->ev = 'PageView';
       $query->cd = \PMVC\get($f, 'params');
       $go->set('fbPixelUrl', (string)$pixelUrl);
       \PMVC\plug('lucency_google_tag')
            ->cookViewForward($go, $f );
       \PMVC\plug(_RUN_APP)['type'] = 'view';
       return $go;
    }

    static function action ($m, $f)
    {
       $go = $m['action'];
       $pixelUrl = self::initFbPixel($f);
       $query = $pixelUrl->query;
       $params = \PMVC\get($f, 'params');
       $action = \PMVC\get($params, 'action', 'ViewContent');
       $query->ev = $action;
       $query->cd = $params;
       $go->set('fbPixelUrl', (string)$pixelUrl);
       \PMVC\plug('lucency_google_tag')
            ->cookActionForward($go, $f, $action);
       \PMVC\plug(_RUN_APP)['type'] = 'action';
       return $go;
    }
}
