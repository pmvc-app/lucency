<?php

namespace PMVC\App\lucency;

use PMVC\PlugIn;
use DateTime;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_heap_server';

/**
 * @see https://docs.heapanalytics.com/v1.0/reference#track-1
 */ 

class lucency_heap_server extends PlugIn
{
    const url = 'https://heapanalytics.com/api/';

    public function request(array $data, $identity=null)
    {
        if (empty($identity)) {
            return !trigger_error('miss identity');
        }
        $heapId = \PMVC\get($data, 'id');
        $heapProperties = \PMVC\get($data, 'properties');
        $heapEvents = \PMVC\get($data, 'events', []);
        $heapParams = \PMVC\get($data, 'params');
        $this->addUserProperties($heapId, $heapProperties, $identity);
        $trackParams = $this->_cookTrackParams($heapEvents, $heapParams, $identity);
        $this->track($heapId, $trackParams);
        \PMVC\plug('curl')->process();
    }

    public function addUserProperties($heapId, $params, $identity)
    {
        $url = \PMVC\plug('url')->
            getUrl(self::url)->
            set('add_user_properties');
        $curl = \PMVC\plug('curl');
        $data = [
            'identity'=>$identity,
            'app_id'=> $heapId,
            'properties'=> $params
        ];
        $curl->post($url, function($r){
            \PMVC\dev(function() use ($r){
                return $r->body;
            }, 'heap');
        }, $data, false, true);
    }

    private function _cookTrackParams($heapEvents, $heapParams, $identity)
    {
        $data = [];
        $all = [
            'timestamp'=> date(DateTime::ISO8601),
            'identity'=> $identity
        ];
        foreach ($heapEvents as $e) {
            $item = array_replace($all, [
                'event'=> \PMVC\get($e, 'event'),
                'properties'=> $heapParams
            ]);
            $data[] = $item;
        }
        return $data;
    }

    public function track($heapId, $params)
    {
        $url = \PMVC\plug('url')->
            getUrl(self::url)->
            set('track');
        $curl = \PMVC\plug('curl');
        $data = [
            'app_id'=> $heapId,
            'events'=> $params
        ];
        $curl->post($url, function($r){
            \PMVC\dev(function() use ($r){
                return $r->body;
            }, 'heap');
        }, $data, false, true);
    }

}
