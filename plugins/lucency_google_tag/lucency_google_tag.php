<?php

namespace PMVC\App\lucency;

use PMVC\ActionForward;
use PMVC\ActionForm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_google_tag';

class lucency_google_tag extends BaseTagPlugin
{

    private $_params;
    private $_configs;

    public function initCook(
        ActionForward $forward,
        ActionForm $form
    ) {
        $options = $this['option'];
        $defaultLabel = \PMVC\get($form, 'params', []);
        $bucketParams = \PMVC\get($form, 'buckets', []);
        $this->_params = array_merge(
            $defaultLabel,
            $bucketParams,
            [
               'label'   => $this->getLabel($defaultLabel),
               'event'   => $this['event'],
               'gaId'    => \PMVC\get($options, 'gaId'),
               'bCookie'=> $forward->get('b'),
               'pvid'   => \PMVC\get($form, 'pvid')
            ]
        );
        $this->_configs = [
            'gtagEnv'=> \PMVC\get($options, 'env'),
        ];
    }

    public function getLabel($params)
    {
        $label = \PMVC\get($params, 'lebel'); 
        if (empty($label)) {
            unset($params['ecommerce']);
            $_ = \PMVC\plug('underscore');
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
        $data = array_merge(
            $this->_configs,
            [
                'params' => $this->_params,
                'id'     => \PMVC\value($this, ['option','id']),
            ]
        );
        $this->append(
            $forward,
            $data
        );
    }

    public function cookActionForward(
        ActionForward $forward,
        ActionForm $form,
        $action
    ) {
        $params = $this->_params;
        $params['action'] = $action;
        $data = array_merge(
            $this->_configs,
            [
                'params' => $this->_params,
            ]
        );
        $this->append(
            $forward,
            $data
        );
    }
}
