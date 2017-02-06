<?php
namespace PMVC\App\lucency;

use PMVC\ActionForward;
use PMVC\ActionForm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_google_tag';

class lucency_google_tag extends BaseTagPlugin
{

    private $_params;
    public function initCook(
        ActionForward $forward,
        ActionForm $form
    ) {
       $params = \PMVC\get($form, 'params', []);
       $bucketParams = \PMVC\get($form, 'buckets', []);
       $forward->set('gtagEnv', \PMVC\value($this, ['option', 'env']));
       $this->_params = array_merge(
            $params,
            $bucketParams,
            [
               'label' => \PMVC\get( $params, 'label', json_encode($params) ),
               'event' => $this['event']
            ]
       );
    }

    public function cookViewForward(
        ActionForward $forward,
        ActionForm $form
    ) {
       $forward->set('gtagId', \PMVC\value($this,['option','id']));
       $forward->set('gtagParams', $this->_params);
    }

    public function cookActionForward(
        ActionForward $forward,
        ActionForm $form,
        $action
    ) {
       $params = $this->_params;
       $params['action'] = $action;
       $forward->set('gtagParams', $params);
    }
}
