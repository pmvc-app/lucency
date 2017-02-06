<?php
namespace PMVC\App\lucency;

use PMVC\Plugin;
use PMVC\ActionForward;
use PMVC\ActionForm;

abstract class BaseTagPlugin extends \PMVC\PlugIn
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
}
