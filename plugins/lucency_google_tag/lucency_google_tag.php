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
        $options = $this['option'];
        $params = \PMVC\get($form, 'params', []);
        $params['pvid'] = \PMVC\get($form, 'pvid');
        $bucketParams = \PMVC\get($form, 'buckets', []);
        $forward->set('gtagEnv', \PMVC\get($options, 'env'));
        $this->_params = array_merge(
            $params,
            $bucketParams,
            [
               'label' => $this->getLabel($params),
               'event' => $this['event'],
               'gaId'  => \PMVC\get($options, 'gaId'),
            ]
        );
    }

    public function getLabel($params)
    {
        $label = \PMVC\get($params, 'lebel'); 
        if (empty($label)) {
            $_ = \PMVC\plug('underscore');
            unset($params['pvid']);
            $querys = $_->array()->toQuery($params);
            $label = join('&', array_map(
                function ($k, $v) {
                    return $k.'='.$v;
                },
                array_keys($querys),
                $querys
            ));
        }
        return $label;
    }

    public function cookViewForward(
        ActionForward $forward,
        ActionForm $form
    ) {
        $forward->set('gtagId', \PMVC\value($this, ['option','id']));
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
