<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.
    '\lucency_facebook_pixel';

const FB_PIXEL_URL = 'https://www.facebook.com/tr?noscript=1';

class lucency_facebook_pixel extends \PMVC\Plugin
{
    public function initPixel($form)
    {
       $pixelUrl = FB_PIXEL_URL;
       $pixelUrl = \PMVC\plug('url')->getUrl($pixelUrl);
       $query = $pixelUrl->query;
       $query->id = \PMVC\value($this,['option','id']);
       $query->r = time();
       $query->dl = $form['url'];
       $params = \PMVC\get($form, 'params', []);
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
        return $pixelUrl;
    }

    public function cookViewForward($forward, $form)
    {
       $pixelUrl = $this->initPixel($form);
       $query = $pixelUrl->query;
       $query->ev = 'PageView';
       $forward->set('fbPixelUrl', (string)$pixelUrl);
    }

    public function cookActionForward($forward, $form, $action)
    {
       $pixelUrl = $this->initPixel($form);
       $query = $pixelUrl->query;
       $query->ev = $action;
       $forward->set('fbPixelUrl', (string)$pixelUrl);
    }
}
