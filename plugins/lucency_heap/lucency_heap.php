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
        $params = \PMVC\get($form, 'params', []);
        $data = [
            'id'=>\PMVC\value($this,['option','id']),
            'properties'=>\PMVC\get($form, 'buckets'),
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
