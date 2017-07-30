<?php

namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\ViewLucencyPixel';

class ViewLucencyPixel extends ViewEngine 
{
    public function setThemeFolder($val) {}

    public function process()
    {
        $lucencyData = \PMVC\get(
            $this->get('data'),
            'lucency'
        );
        $fbPixelUrl = \PMVC\value($lucencyData, [
            'facebook_pixel',
            'fbPixelUrl'
        ]);
        $bCookie = $this->get('b');
        $heapData = \PMVC\get($lucencyData, 'heap'); 
        \PMVC\plug('lucency_heap_server')->request($heapData, $bCookie);
        //header('Location: '.$fbPixelUrl);
    }
}
