<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_google_tag';

class lucency_google_tag extends \PMVC\Plugin
{
    public function initCook($forward, $form)
    {
       $params = \PMVC\get($form, 'params', []);
       $bucketParams = \PMVC\get($form, 'buckets', []);
       $forward->set('gtagEnv', \PMVC\value($this, ['option', 'env']));
       return array_merge(
            $params,
            $bucketParams,
            [
               'label'=>\PMVC\get($params, 'label', json_encode($params) )
            ]
       );
    }

    public function cookViewForward($forward, $form)
    {
       $params = $this->initCook($forward, $form);
       $forward->set('gtagId', \PMVC\value($this,['option','id']));
       $forward->set('gtagParams', $params);
    }

    public function cookActionForward($forward, $form, $action)
    {
       $params = $this->initCook($forward, $form);
       $params['action'] = $action;
       $forward->set('gtagParams', $params);
    }
}
