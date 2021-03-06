<?php
namespace PMVC\App\lucency;

use PMVC;
use PMVC\ActionForward;
use PMVC\ActionForm;
use PMVC\MappingBuilder;

\PMVC\l(__DIR__.'/src/BaseTagPlugin.php');

$b = new MappingBuilder();
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
    _PATH => 'lucencyAction'
    ,_TYPE => 'view'
    ,_ACTION=> 'store'
]);

/* Events */
const LUCENCY_EVENT_VIEW = 'lucencyEventView';
const LUCENCY_EVENT_ACTION = 'lucencyEventAction';
/* Actions */
const LUCENCY_ACTION_DEFAULT = 'ViewContent';
const LUCENCY_ACTION_SEARCH = 'Search';
const LUCENCY_ACTION_ADD_TO_CART = 'AddToCart';
const LUCENCY_ACTION_ADD_TO_WISHLIST = 'AddToWishlist';
const LUCENCY_ACTION_INITIATE_CHECKOUT = 'InitiateCheckout';
const LUCENCY_ACTION_ADD_PAYMENT_INFO = 'AddPaymentInfo';
const LUCENCY_ACTION_PURCHASE = 'Purchase';
const LUCENCY_ACTION_LEAD = 'Lead';
const LUCENCY_ACTION_COMPLETE_REGISTRATION = 'CompleteRegistration';

class Lucency extends PMVC\Action
{
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

    /**
     * What different between assign value to $go and $f?
     * $go -> will use in template only.
     * $f -> will use in lucency plugin
     */
    static function getTags(
        ActionForward $go,
        ActionForm $f
    ) {
        ignore_user_abort(true);
        $pCookie = \PMVC\plug('cookie');
        $f['buckets'] = self::assignBucket(self::getBuckets());
        $f['landingUrl'] = \PMVC\plug('url')->getUrl($f['url']);
        if (!is_array($f['params'])) {
            $f['params'] = \PMVC\toArray($f['params']);
        }

        // <!-- Get UTM
        $utm = $pCookie->get('UTM');
        if (!empty($utm)) {
            $f['params']['UTM'] = $utm;
        }
        // Get UTM -->

        $go->set('b', $pCookie->get('b'));
        $go->set('disableIframe', \PMVC\get($f, 'if', false));
        $app = \PMVC\plug(_RUN_APP);
        $tags = \PMVC\get($app,'tags', []);
        return $tags;
    }

    static function index ($m, $f)
    {
        return null;
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

    static function view ($m, $f)
    {
        if ('pixel'===$f['o']) {
            $view =& \PMVC\getOption('view');
            $view['engine']['lucency'] = 'lucency_pixel';
        }
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
             $plug->initCook($go, $f);
             $plug->cookViewForward($go, $f);
             $enabled[] = $tag['name'];
        }
        $go->set('enabled', $enabled);
        $go->set('event', $event);
        $go->set('vpvid', \PMVC\plug('getenv')->get('UNIQUE_ID'));
        \PMVC\plug(_RUN_APP)['type'] = 'view';
        return $go;
    }

    static function action ($m, $f)
    {
        if ('pixel'===$f['o']) {
            \PMVC\plug('controller')[_VIEW_ENGINE] = 
                'lucency_view_pixel';
        }
        $go = $m['action'];
        $tags = self::getTags($go, $f);
        $params =& \PMVC\ref($f->params);
        $action = \PMVC\get($params, 'action', LUCENCY_ACTION_DEFAULT);
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
             $plug->initCook($go, $f);
             $plug->cookActionForward($go, $f, $action);
             $enabled[] = $tag['name'];
        }
        $go->set('enabled', $enabled);
        $go->set('event', $event);
        $go->set('action', $action);
        \PMVC\plug(_RUN_APP)['type'] = 'action';
        return $go;
    }
}
