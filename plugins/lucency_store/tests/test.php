<?php
namespace PMVC\PlugIn\lucency_store;

use PHPUnit_Framework_TestCase;

class Lucency_storeTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'lucency_store';
    function testPlugin()
    {
        ob_start();
        print_r(\PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

}
