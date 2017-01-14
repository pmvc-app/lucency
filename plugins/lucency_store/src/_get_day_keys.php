<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\get_day_keys';

class get_day_keys {

    function __invoke($days, $timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        }
        $format = 'Y_m_d';
        $all = [];
        for ($i = $days-1; $i > 0; $i --) {
            $time = strtotime('-'.$i.' day', $timestamp);
            $all[] = date($format, $time);
        }
        $all[]= date($format, $timestamp);
        $all = array_reverse($all);
        return $all;
    }

}
