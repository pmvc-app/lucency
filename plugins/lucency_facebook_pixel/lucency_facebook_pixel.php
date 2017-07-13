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

    private function _processEcommerce(&$params)
    {
        $ecommerce = \PMVC\get($params, 'ecommerce');
        if (!$ecommerce) {
            return;
        }
        $keys = ['click', 'detail', 'add'];
        foreach ($keys as $key) {
            if (isset($ecommerce[$key])) {
                $product = \PMVC\value($ecommerce, [$key, 'products', 0]);
                break;
            }
        }
        if (!isset($product)) {
            return;
        }
        unset($params['ecommerce']);
        $params['content_ids'] = \PMVC\get($product, 'id');
        $params['content_type'] = 'product';
        if (isset($product['name'])) {
            $params['content_name'] = $product['name'];
        }
    }

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
       $params['buckets'] = \PMVC\get($form, 'buckets', []);
        //product
        $this->_processEcommerce($params);
        $params['event'] = $this['event'];
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
       if (LUCENCY_ACTION_DEFAULT===$action) {
           if (empty($query->cd['content_ids']) ||
               empty($query->cd['content_type'])
           ) {
               // hack for fb pixel verify
               $action .= 'Lucency';
           }
       }
       $query->ev = $action;
       $forward->set('fbPixelUrl', (string)$pixelUrl);
    }
}
