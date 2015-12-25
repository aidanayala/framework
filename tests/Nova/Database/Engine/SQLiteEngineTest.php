<?php


namespace Nova\Tests\Database\Engine;

/**
 * Class SQLiteEngineTest
 * @package Nova\Tests\Database\Engine
 *
 * @coversDefaultClass \Nova\Database\Engine\SQLite
 */
class SQLiteEngineTest extends \PHPUnit_Framework_TestCase
{
    private $engine;

    private function prepareEngine()
    {
        $this->engine = \Nova\Database\Manager::getEngine('sqlite');
        $this->assertInstanceOf('\Nova\Database\Engine\SQLite', $this->engine);
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::getDriverName
     * @covers \Nova\Database\Engine\SQLite::getConfiguration
     * @covers \Nova\Database\Engine\SQLite::getConnection
     * @covers \Nova\Database\Engine\SQLite::getDriverCode
     */
    public function testEngineBasics()
    {
        $this->prepareEngine();

        $this->assertInstanceOf('\PDO', $this->engine->getConnection());

        $this->assertEquals('SQLite Driver', $this->engine->getDriverName());
        $this->assertEquals(\Nova\Database\Manager::DRIVER_SQLITE, $this->engine->getDriverCode());
        $this->assertEquals(\Nova\Config::get('database')['sqlite']['config'], $this->engine->getConfiguration());

        $this->assertGreaterThanOrEqual(0, $this->engine->getTotalQueries());
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::select
     * @covers \Nova\Database\Engine\SQLite::selectOne
     * @covers \Nova\Database\Engine\SQLite::selectAll
     */
    public function testSelecting()
    {
        $this->prepareEngine();

        // === Select All, No WHERE.

        // First, we will get ALL our cars, we will use the selectAll
        $all = $this->engine->selectAll("SELECT * FROM " . DB_PREFIX . "car");

        $this->assertGreaterThanOrEqual(2, count($all), "Select should return array of 2 or more");

        // Check one in the array
        $this->assertObjectHasAttribute('carid', $all[0]);
        $this->assertObjectHasAttribute('make', $all[0]);
        $this->assertObjectHasAttribute('model', $all[0]);
        $this->assertObjectHasAttribute('costs', $all[0]);


        // === Select One, Only get the Model S
        $model_s = $this->engine->selectOne("SELECT * FROM " . DB_PREFIX . "car WHERE model LIKE 'Model S';");

        $this->assertNotNull($model_s);
        $this->assertObjectHasAttribute('carid', $model_s);
        $this->assertObjectHasAttribute('make', $model_s);
        $this->assertObjectHasAttribute('model', $model_s);
        $this->assertObjectHasAttribute('costs', $model_s);
        $this->assertEquals(1, $model_s->carid);
        $this->assertEquals('Tesla', $model_s->make);



        // === Select All, Fetch With ASSOC
        $all_assoc = $this->engine->selectAll("SELECT * FROM " . DB_PREFIX . "car", array(), \PDO::FETCH_ASSOC);

        $this->assertGreaterThanOrEqual(2, count($all_assoc), "Select should return array of 2 or more");

        // Check one in the array
        $this->assertArrayHasKey('carid', $all_assoc[0]);
        $this->assertArrayHasKey('make', $all_assoc[0]);
        $this->assertArrayHasKey('model', $all_assoc[0]);
        $this->assertArrayHasKey('costs', $all_assoc[0]);



        // === Select One, But this doesn't exists.
        $notthere = $this->engine->selectOne("SELECT * FROM " . DB_PREFIX . "car WHERE make LIKE 'Renault';");

        $this->assertFalse($notthere);
    }
}