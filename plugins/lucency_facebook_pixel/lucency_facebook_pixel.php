<?php
namespace PMVC\App\lucency;

use PMVC\ActionForward;
use PMVC\ActionForm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_facebook_pixel';

const FB_PIXEL_URL = 'https://www.facebook.com/tr?noscript=1';

class lucency_facebook_pixel extends BaseTagPlugin
{
    private $_pixelUrl;

    public function initCook(
        ActionForward $forward,
        ActionForm $form
    ) {
       $this->_pixelUrl = \PMVC\plug('url')->getUrl(FB_PIXEL_URL);
       $query = $this->_pixelUrl->query;
       $query->id = \PMVC\value($this,['option','id']);
       $query->r = time();
       $query->dl = $form['url'];
       $params = \PMVC\get($form, 'params', []);
       $params['pvid'] = \PMVC\get($form, 'pvid', []);
       $params['event'] = $this['event'];

        //product
        $product = \PMVC\value($params, [
            'ecommerce',
            'click',
            'products',
            0
        ]);
        if ($product) {
            unset($params['ecommerce']);
            $params['content_ids'] = \PMVC\get($product, 'id');
            $params['content_type'] = 'product';
        }
        $query->cd = $params;
    }

    public function cookViewForward(
        ActionForward $forward,
        ActionForm $form
    ) {
       $pixelUrl = $this->_pixelUrl;
       $query = $pixelUrl->query;
       $query->ev = 'PageView';
       $forward->set('fbPixelUrl', (string)$pixelUrl);
    }

    public function cookActionForward(
        ActionForward $forward,
        ActionForm $form,
        $action
    ) {
       $pixelUrl = $this->_pixelUrl;
       $query = $pixelUrl->query;
       $query->ev = $action;
       $forward->set('fbPixelUrl', (string)$pixelUrl);
    }
}
