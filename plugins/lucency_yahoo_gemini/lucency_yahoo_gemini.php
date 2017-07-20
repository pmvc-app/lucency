<?php

namespace PMVC\App\lucency;

use PMVC\ActionForward;
use PMVC\ActionForm;

${_INIT_CONFIG
}[_CLASS] = __NAMESPACE__.
    '\lucency_yahoo_gemini';

class lucency_yahoo_gemini extends BaseTagPlugin
{
    const TAG_URL = 'https://sp.analytics.yahoo.com/spp.pl';
    private $_tagUrl;

    public function initCook(
        ActionForward $forward,
        ActionForm $form
    ) {
       $this->_tagUrl =
        \PMVC\plug('url')->
        getUrl(self::TAG_URL);
    }

    public function cookViewForward(
        ActionForward $forward,
        ActionForm $form
    ) {
        $query = $this->_tagUrl->query;
        $query->a = \PMVC\value($this, ['option', 'projectId']);
        $query['.yp'] = \PMVC\value($this, ['option', 'id']);
        $data = ['tagUrl'=>(string)$this->_tagUrl];
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

    }
}
