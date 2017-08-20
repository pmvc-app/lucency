<?php
namespace PMVC\App\lucency;

use PMVC\ActionForward;
use PMVC\ActionForm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_heap';

class lucency_heap extends BaseTagPlugin
{

    private $_params;

    public function initCook(
        ActionForward $forward,
        ActionForm $form
    ) {
        $pEnv = \PMVC\plug('getenv');
        $params = \PMVC\get($form, 'params', []);
        $params = array_merge(
            $params,
            [
                'pvid' => \PMVC\get($form, 'pvid', []),
                'hour' => date('H'),
                'week' => date('w'),
                'cdn' => $pEnv->get('CDN'),
                'country' => $pEnv->get('COUNTRY'),
            ]
        );

        $this->_params = $params;
    }

    public function cookViewForward(
        ActionForward $forward,
        ActionForm $form
    ) {
        $events = [
            [
                'event'=>$this['event']
            ]
        ];
        $params = $this->_params;
        $speed = \PMVC\get($form, 'sp', []);
        if ($speed) {
            $params['speed'] = $speed;
        }
        $properties = \PMVC\get($form, 'buckets');
        $gclid = \PMVC\value($form, ['landingUrl', 'query', 'gclid']);
        if ($gclid) {
            $properties['gclid'] = $gclid;
        }
        
        //<!--- Set UTM
        $utm = \PMVC\get($params, 'UTM');
        if (!empty($utm)) {
            $properties['utm'] = $utm;
        }
        // Set UTM -->

        $data = [
            'id'=>\PMVC\value($this,['option','id']),
            'properties'=>$properties,
            'params'=>$params,
            'events'=>\PMVC\get($params, 'events', $events)
        ];
        $forward->append([
            'data'=> [
                'lucency'=> [
                    $this['option']['name']=>
                    $data
                ]
            ]
        ]);
    }

    public function cookActionForward(
        ActionForward $forward,
        ActionForm $form,
        $action
    ) {
        $events = [
            [
                'event'=>$action
            ]
        ];
        $params = $this->_params;
        $data = [
            'params'=>$params,
            'events'=>\PMVC\get($params, 'events', $events)
        ];
        $this->append(
            $forward,
            $data
        );
    }
}
