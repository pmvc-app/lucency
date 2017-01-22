<?php
namespace PMVC\App\lucency;

${_INIT_CONFIG}[_CLASS] = '\PMVC\Action';

$yo=\PMVC\plug('yo');
$yo->post('/lucency/view', function($m, $f){
    $go = $m['dump'];
    $store = \PMVC\plug('lucency_store');
    $result = $store->storeView($f);
    $go->set('data', $result);
    $go->set('type', 'view');
    return $go;
});

$yo->post('/lucency/action', function($m, $f){
    $go = $m['dump'];
    $store = \PMVC\plug('lucency_store');
    $result = $store->storeAction($f);
    $go->set('data', $result);
    $go->set('type', 'action');
    return $go;
});
