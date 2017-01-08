<?php
namespace PMVC\App\lucency;

use PMVC;

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\Lucency';
${_INIT_CONFIG}[_INIT_BUILDER] = $b;

$b->addAction('index');
$b->addAction('view');
$b->addAction('storeView');


$b->addForward('view', array(
    _PATH => 'lucencyView'
    ,_TYPE => 'view'
    ,_ACTION=> 'storeView'
));


class Lucency extends PMVC\Action
{
    static function index ($m, $f) {
       ignore_user_abort(true);
       $go = $m['view'];
       return $go;
    }

    static function storeView ($m, $f) {
        $api = \PMVC\getOption('middlewareHost');
        $url = $api. '/lucency/view';
        $env = \PMVC\plug('getenv');
        $request = \PMVC\plug('controller')->getRequest();
        $curl = \PMVC\plug('curl'); 
        $curl->post($url, function($r){
            // \PMVC\d($r->body);
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

}
