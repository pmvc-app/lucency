<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_heap';

class lucency_heap extends \PMVC\Plugin
{

    public function cookViewForward($forward, $form)
    {
        $forward->set('heapId', \PMVC\value($this,['option','id']));
        $forward->set(
             'heapProperties',
             \PMVC\get($form, 'buckets') 
        );
        $params = \PMVC\get($form, 'params', []);
        $forward->set('heapParams', $params); 
    }

    public function cookActionForward($forward, $form, $action)
    {
        $params = \PMVC\get($form, 'params', []);
        $params = array_merge(
            \PMVC\get($form, 'params', []),
            [
                'action' => $action
            ]
        );
        $forward->set('heapParams', $params); 
    }
}
