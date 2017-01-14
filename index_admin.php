<?php
namespace PMVC\App\lucency;

use PMVC;

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\LucencyAdmin';
${_INIT_CONFIG}[_INIT_BUILDER] = $b;

$b->addAction('index');

class LucencyAdmin extends PMVC\Action
{
    static function index ($m, $f)
    {
        $store = \PMVC\plug('lucency_store');
        $days = $store->get_day_keys(7);
        $vDb = $store['viewDb'];
        $size = $vDb->hsize();
        $counts = [];
        foreach ($days as $d) {
            $keys = $vDb->hkeys($d, $d.'_23', $size);
            $counts[$d] = count($keys);
        }
        \PMVC\d($counts);
        $last5 = $vDb->hrscan('','',5);
        foreach($last5 as $u) {
            $u = \PMVC\fromJson($u);
            \PMVC\d(\PMVC\value($u,['server','HTTP_USER_AGENT']));
        }
    }
}
