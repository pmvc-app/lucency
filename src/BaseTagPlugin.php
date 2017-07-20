<?php
namespace PMVC\App\lucency;

use PMVC\Plugin;
use PMVC\ActionForward;
use PMVC\ActionForm;

abstract class BaseTagPlugin extends PlugIn
{
    abstract public function initCook(
        ActionForward $forward,
        ActionForm $form
    );
    abstract public function cookViewForward(
        ActionForward $forward,
        ActionForm $form
    );
    abstract public function cookActionForward(
        ActionForward $forward,
        ActionForm $form,
        $action
    );

    protected function append(
        ActionForward $forward,
        array $data
    ) {
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
