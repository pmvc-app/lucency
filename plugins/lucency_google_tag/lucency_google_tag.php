<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_google_tag';

const GTAG_CUSTOM_EVENT = 'customEvent';
const GTAG_CUSTOM_VIEW = 'customView';

class lucency_google_tag extends \PMVC\Plugin
{
    public function cookViewForward($forward, $form)
    {
       $params = \PMVC\get($form, 'params', []);
       $forward->set('gtagId', \PMVC\getOption('gtagId'));
       $forward->set('gtagParams', array_merge(
            $params,
            [
               'label'=>\PMVC\get($params, 'label', json_encode($params) ),
               'event'=>\PMVC\get($params, 'event', GTAG_CUSTOM_VIEW)
            ]
       ));
    }

    public function cookActionForward($forward, $form, $action)
    {
       $params = \PMVC\get($form, 'params', []);
       $forward->set('gtagId', \PMVC\getOption('gtagId'));
       $forward->set('gtagParams', array_merge(
            $params,
            [
               'action'=>$action,
               'label'=>\PMVC\get($params, 'label', json_encode($params) ),
               'event'=>\PMVC\get($params, 'event', GTAG_CUSTOM_EVENT)
            ]
       ));
    }
}
