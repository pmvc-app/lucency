<?php
namespace PMVC\App\lucy;

use PMVC;

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\Lucency';
${_INIT_CONFIG}[_INIT_BUILDER] = $b;

$b->addAction('index', array(
    _FUNCTION => array(
        ${_INIT_CONFIG}[_CLASS],
        'index'
    )
));


$b->addForward('home', array(
    _PATH => 'hello'
    ,_TYPE => 'view'
));


class Lucency extends PMVC\Action
{
    static function index($m, $f){
       $go = $m['home'];
       $go->set('text',' world---'.microtime());
       return $go;
    }
}
