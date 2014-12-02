<?php

use Orchestra\Testbench\TestCase;
use Dimsav\Nested\Test\Model\Category;

class TestsBase extends TestCase {

    protected $queriesCount;

    public function setUp()
    {
        parent::setUp();
        $artisan = $this->app->make('artisan');

        $this->resetDatabase($artisan);
        $this->countQueries();
    }

    public function testRunningMigration()
    {
        $country = Category::find(1);
        $this->assertEquals('Sciences', $country->name);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__ . '/../Nested';

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', array(
            'driver'   => 'mysql',
            'host' => 'localhost',
            'database' => 'nested_test',
            'username' => 'homestead',
            'password' => 'secret',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ));
    }

    protected function getPackageAliases()
    {
        return array('Eloquent' => 'Illuminate\Database\Eloquent\Model');
    }

    protected function countQueries() {
        $that = $this;
        $event = App::make('events');
        $event->listen('illuminate.query', function() use ($that) {
            $that->queriesCount++;
        });
    }

    /**
     * @param $artisan
     */
    private function resetDatabase($artisan)
    {
        // This creates the "migrations" table if not existing
        $artisan->call('migrate', [
            '--database' => 'mysql',
            '--path'     => '../tests/migrations',
        ]);
        // We empty the tables
        $artisan->call('migrate:reset', [
            '--database' => 'mysql',
        ]);
        // We fill the tables
        $artisan->call('migrate', [
            '--database' => 'mysql',
            '--path'     => '../tests/migrations',
        ]);
    }
}