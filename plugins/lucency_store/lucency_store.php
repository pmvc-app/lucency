<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\lucency_store';

use DomainException;

class lucency_store extends \PMVC\PlugIn
{
    private $_guid;
    private $_viewDb;

    public function init()
    {
        $this->_guid = \PMVC\plug('guid');
        /* Possibile use in caller */
        $this['viewDb'] = $this->_guid->getDb('LucencyView');
        $this['mappingDb'] = $this->_guid->getDb('LucencyMapping');
    }

    public function storeView($f)
    {
        $vData = \PMVC\get(
            $f,
            [
                'client',
                'params',
                'server'
            ]
        );
        $site = \PMVC\value($vData,['params', 'SITE']);
        $pvid = \PMVC\value($vData,['client', 'pvid']);
        if (empty($pvid)) {
            throw new DomainException('[Lucency store::storeView] pvid can\'t empty');
        }
        $newKey = $this['viewDb']->getNewKey($site);
        $vDb = $this['viewDb'];
        $mDb = $this['mappingDb'];
        if (isset($mDb[$pvid])) {
            $mData = \PMVC\fromJson($mDb[$pvid]);
        } else {
            $mData = [];
        }
        if (empty($mData['view'])) {
            $mData['view'] = [$newKey];
        } else {
            $mData['view'][] = $newKey;
        }
        $vDb[$newKey] = json_encode($vData);
        $mDb[$pvid] = json_encode($mData);
        return [
            'v' => $vDb[$newKey],
            'm' => $mDb[$pvid]
        ];
    }
}
