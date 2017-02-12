<?php
namespace PMVC\App\lucency;

use PMVC\ActionForward;
use PMVC\ActionForm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_heap';

class lucency_heap extends BaseTagPlugin
{
    public function initCook(
        ActionForward $forward,
        ActionForm $form
    ) {
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
        $speed = \PMVC\get($form, 'sp', []);
        $params = \PMVC\get($form, 'params', []);
        if ($speed) {
            $params['speed'] = $speed;
        }
        $properties = \PMVC\get($form, 'buckets');
        $gclid = \PMVC\value($form, ['landingUrl', 'query', 'gclid']);
        if ($gclid) {
            $properties['gclid'] = $gclid;
        }
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
        $params = \PMVC\get($form, 'params', []);
        $data = [
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
}
