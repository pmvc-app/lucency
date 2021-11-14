<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\lucency_store';

use DomainException;

class lucency_store extends \PMVC\PlugIn
{
    private $_guid;

    public function init()
    {
        $this->_guid = \PMVC\plug('guid');
        /* Possibile use in caller */
        $this['viewDb'] = $this->_guid->getModel('LucencyView');
        $this['actionDb'] = $this->_guid->getModel('LucencyAction');
        $this['mappingDb'] = $this->_guid->getModel('LucencyMapping');
    }

    private function beforeStore($f)
    {
        $data = \PMVC\get(
            $f,
            [
                'client',
                'params',
                'server'
            ]
        );
        $site = \PMVC\value($data,['params', 'SITE']);
        $pvid = \PMVC\value($data,['client', 'pvid']);
        if (empty($pvid)) {
            throw new DomainException('[Lucency store::beforeStore] pvid can\'t empty');
        }
        return [
            'data'=>$data,
            'site'=>$site,
            'pvid'=>$pvid
        ];
    }

    public function storeView($f)
    {
        $data = $this->beforeStore($f);
        $pvid = $data['pvid'];
        $vDb = $this['viewDb'];
        $mDb = $this['mappingDb'];
        $newKey = $vDb->getNewKey($data['site']);
        if (isset($mDb[$pvid])) {
            $mData = (array)\PMVC\fromJson($mDb[$pvid]);
        } else {
            $mData = [];
        }
        if (empty($mData['view'])) {
            $mData['view'] = [$newKey];
        } else {
            $mData['view'][] = $newKey;
        }
        $vDb[$newKey] = json_encode($data['data']);
        $mDb[$pvid] = json_encode($mData);
        return [
            'v' => $vDb[$newKey],
            'm' => $mDb[$pvid]
        ];
    }

    public function storeAction($f)
    {
        $data = $this->beforeStore($f);
        $pvid = $data['pvid'];
        $aDb = $this['actionDb'];
        $mDb = $this['mappingDb'];
        $newKey = $aDb->getNewKey($data['site']);
        if (!isset($mDb[$pvid])) {
            throw new DomainException('[Lucency store::storeAction] pvid not exists in mapping db');
        }
        $mData = (array)\PMVC\fromJson($mDb[$pvid]);
        $mData['action'] = \PMVC\get($mData, 'action', []);
        $mData['action'][] = $newKey; 
        $aDb[$newKey] = json_encode($data['data']);
        $mDb[$pvid] = json_encode($mData);
        return [
            'a' => $aDb[$newKey],
            'm' => $mDb[$pvid]
        ];
    }
}
