<?php
namespace PMVC\App\lucency;

use PMVC\TestCase;

class LucyTest extends TestCase
{
    private $_app;

    function pmvc_setup()
    {
        $dirs = explode('/', __DIR__);
        $app = $dirs[count($dirs) - 2];
        $this->_app = $app;
        \PMVC\unplug('controller');
        \PMVC\unplug('view');
        \PMVC\unplug(_RUN_APP);
        \PMVC\plug('view', [
            _CLASS => '\PMVC\FakeView',
        ]);
    }

    function testProcessAction()
    {
        $c = \PMVC\plug('controller');
        $c->setApp($this->_app);
        $c->setAppAction('view');
        $c->plugApp(['../']);
        $result = $c->process();
        $actual = \PMVC\get($result, '0')->get('text');
        $this->assertNull($actual);
    }

    function testDefaultActionEvent()
    {
        $c = \PMVC\plug('controller');
        $c->setApp($this->_app);
        $c->setAppAction('action');
        $c->plugApp(['../']);
        $result = $c->process();
        $actual = \PMVC\get($result, '0')->get('event');
        $this->assertEquals(LUCENCY_EVENT_ACTION, $actual);
        $action = \PMVC\get($result, '0')->get('action');
        $this->assertEquals(LUCENCY_ACTION_DEFAULT, $action);
    }

    function testDefaultViewEvent()
    {
        $c = \PMVC\plug('controller');
        $c->setApp($this->_app);
        $c->setAppAction('view');
        $c->plugApp(['../']);
        $result = $c->process();
        $actual = \PMVC\get($result, '0')->get('event');
        $this->assertEquals(LUCENCY_EVENT_VIEW, $actual);
    }
}
