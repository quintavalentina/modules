<?php

use Mockery as m;
use Quintavalentina\Modules\Modules;

class ModulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ModuleRepository
     */
    protected $repository;

    /**
     * @var Modules
     */
    protected $module;

    /**
     * Set up test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->app = m::mock('Illuminate\Foundation\Application');
        $this->repository = m::mock('Quintavalentina\Modules\Contracts\Repository');
        $this->module = new Modules($this->app, $this->repository);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testHasCorrectInstance()
    {
        $this->assertInstanceOf('Quintavalentina\Modules\Modules', $this->module);
    }
}
