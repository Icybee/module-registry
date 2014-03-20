<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Registry;

use ICanBoogie\ActiveRecord\Connection;

class EntryModelTest extends \PHPUnit_Framework_TestCase
{
	static private $model;

	static public function setupBeforeClass()
	{
		$descriptor = require __DIR__ . '/../descriptor.php';
		$connection = new Connection('sqlite::memory:');

		self::$model = new Model($descriptor[Module::T_MODELS]['primary'] + [

			Model::CONNECTION => $connection,
			Model::NAME => 'registry'

		]);

		self::$model->install();
	}

	public function test_set()
	{
		$model = self::$model;

		$model->set('one.one.one', 1);
		$this->assertEquals(1, $model->get('one.one.one'));

		$model->set('one.one.one', 2);
		$this->assertEquals(2, $model->get('one.one.one'));

		$model['one.one.one'] = 3;
		$this->assertEquals(3, $model->get('one.one.one'));

		$model['one.one.one'] = 4;
		$this->assertEquals(4, $model['one.one.one']);
	}

	public function test_get()
	{
		$model = self::$model;
		$this->assertNull($model['test_get']);
		$this->assertEquals('Gaga', $model->get('test_get', 'Gaga'));
		$model['test_get'] = 'Madonna';
		$this->assertEquals('Madonna', $model->get('test_get', 'Gaga'));
	}

	public function test_unset()
	{
		$model = self::$model;
		$model['unset_test'] = 1;
		$this->assertTrue($model->filter_by_name('unset_test')->exists);

		$model['unset_test'] = null;
		$this->assertFalse($model->filter_by_name('unset_test')->exists);
		$this->assertNull($model['unset_test']);

		$model['unset_test'] = 1;
		$this->assertTrue($model->filter_by_name('unset_test')->exists);

		unset($model['unset_test']);
		$this->assertFalse($model->filter_by_name('unset_test')->exists);
		$this->assertNull($model['unset_test']);
	}
}