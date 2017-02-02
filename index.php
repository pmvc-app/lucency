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

const LUCENCY_EVENT_VIEW = 'lucencyEventView';
const LUCENCY_EVENT_ACTION = 'lucencyEventAction';
const LUCENCY_DEFAULT_ACTION = 'ViewContent';

class Lucency extends PMVC\Action
{
    static function index ($m, $f)
    {
        return null;
    }

    static function assignBucket(array $buckets)
    {
        $results = [];
        $lucencyBuckets = \PMVC\get(\PMVC\getoption('lucency'),'buckets');
        if (empty($lucencyBuckets)) {
            return $results;
        }
        foreach ($buckets as $k=>$v) {
            $key = $lucencyBuckets[$k];
            if ($key) {
                $results[$key] = $v;
            }
        }
        return $results;
    }

    static function getTags($go, $f)
    {
       ignore_user_abort(true);
       $f['buckets'] = self::assignBucket(self::getBuckets());
       $go->set('b', \PMVC\get($_COOKIE, 'b'));
       $lucencyOption = \PMVC\getOption('lucency');
       $tags = \PMVC\get($lucencyOption,'tags', []);
       return $tags;
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
        $bucketKey = 'HTTP_X_BUCKET_TESTS';
        $buckets = \PMVC\plug('getenv')->get($bucketKey);
        $buckets = array_diff(explode(',',$buckets),['']);
        $results = [];
        foreach ($buckets as $b) {
            $bkey = preg_split('/[\d|N]/', $b);
            $results[$bkey[0]] = $b; 
        }
        return $results;
    }

    static function view ($m, $f)
    {
        $go = $m['view'];
        $tags = self::getTags($go, $f);
        $event = \PMVC\value(
            $f,
            ['params', 'event'],
            \PMVC\get(
                \PMVC\option('lucency'),
                LUCENCY_EVENT_VIEW, 
                LUCENCY_EVENT_VIEW
            )
        );
        $enabled = [];
        foreach ($tags as $tag) {
             if (empty($tag['enabled'])) {
                 continue;
             }
             $plug = \PMVC\plug('lucency_'.\PMVC\get($tag,'name'));
             $plug['option'] = $tag;
             $plug['event'] = $event;
             $plug->cookViewForward($go, $f);
             $enabled[] = $tag['name'];
        }
        $go->set('enabled', $enabled);
        $go->set('event', $enabled);
        $go->set('event', $event);
        \PMVC\plug(_RUN_APP)['type'] = 'view';
        return $go;
    }

    static function action ($m, $f)
    {
        $go = $m['action'];
        $tags = self::getTags($go, $f);
        $params =& \PMVC\ref($f->params);
        $action = \PMVC\get($params, 'action', LUCENCY_DEFAULT_ACTION);
        $event  = \PMVC\get(
            $params,
            'event',
            \PMVC\get(
                \PMVC\option('lucency'),
                LUCENCY_EVENT_ACTION, 
                LUCENCY_EVENT_ACTION
            )
        );
        $enabled = [];
        foreach ($tags as $tag) {
             if (empty($tag['enabled'])) {
                 continue;
             }
             $plug = \PMVC\plug('lucency_'.\PMVC\get($tag,'name'));
             $plug['option'] = $tag;
             $plug['event'] = $event;
             $plug->cookActionForward($go, $f, $action);
             $enabled[] = $tag['name'];
        }
        $go->set('enabled', $enabled);
        $go->set('action', $action);
        $go->set('event', $event);
        \PMVC\plug(_RUN_APP)['type'] = 'action';
        return $go;
    }
}
