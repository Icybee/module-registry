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
use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\ActiveRecord\ModelCollection;
use ICanBoogie\Updater\Update;
use ICanBoogie\Updater\AssertionFailed;

/**
 * - Move data from `local` to `primary` database.
 *
 * @module registry
 */
class Update20110101 extends Update
{
	private $models;

	private function get_models()
	{
		if ($this->models)
		{
			return $this->models;
		}

		return $this->models = new ModelCollection($this->app->connections, [

			'system_registry' => [

				Model::SCHEMA => [

					'name' => [ 'varchar', 'primary' => true ],
					'value' => 'text'

				]
			],

			'system_registry_node' => [

				Model::SCHEMA => [

					'targetid' => [ 'foreign', 'primary' => true ],
					'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
					'value' => 'text'

				]
			],

			'system_registry_user' => [

				Model::SCHEMA => [

					'targetid' => [ 'foreign', 'primary' => true ],
					'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
					'value' => 'text'

				]
			],

			'system_registry_site' => [

				Model::SCHEMA => [

					'targetid' => [ 'foreign', 'primary' => true ],
					'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
					'value' => 'text'

				]
			]

		]);

	}

	public function update_move_table()
	{
		$path = \ICanBoogie\REPOSITORY . 'lib/local.sq3';

		if (!file_exists($path))
		{
			throw new AssertionFailed('assert_file_exists', $path);
		}

		$db = new Connection('sqlite:' . $path);
		$models = $this->get_models();

		foreach ($db('SELECT * FROM SQLite_master WHERE type = "table"') as $table)
		{
			$name = $table['name'];

			switch ($name)
			{
				case 'system_registry':

					$this->copy_primary($name, $db, $models);

					break;

				case 'system_registry_node':
				case 'system_registry_user':
				case 'system_registry_site':

					$this->copy_secondary($name, $db, $models);

					break;
			}
		}
	}

	private function copy_primary($name, Connection $db, ModelCollection $models)
	{
		$model = $models[$name];
		$model->connection->exec("DROP TABLE IF EXISTS $name");
		$model->install();
		$update = $models[$name]->prepare('INSERT INTO {self} SET `name` = ?, `value` = ? ON DUPLICATE KEY UPDATE `value` = ?');

		foreach ($db("SELECT name, value, value FROM $name")->mode(\PDO::FETCH_NUM) as $row)
		{
			$update($row);
		}
	}

	private function copy_secondary($name, Connection $db, ModelCollection $models)
	{
		$model = $models[$name];
		$model->connection->exec("DROP TABLE IF EXISTS $name");
		$model->install();
		$update = $model->prepare('INSERT INTO {self} SET `targetid` = ?, `name` = ?, `value` = ? ON DUPLICATE KEY UPDATE `value` = ?');

		foreach ($db("SELECT targetid, name, value, value FROM $name")->mode(\PDO::FETCH_NUM) as $row)
		{
			$update($row);
		}
	}
}

/**
 * - Renames the `system_registry_node` table as `system_registry__node`
 * - Renames the `system_registry_site` table as `system_registry__site`
 * - Renames the `system_registry_user` table as `system_registry__user`
 *
 * @module registry
 */
class Update20120801 extends Update
{
	public function update_table_system_registry__node()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry_node'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry_node');
		}

		if ($db->table_exists('system_registry__node'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__node');
		}

		$db("RENAME TABLE `system_registry_node` TO `system_registry__node`");
	}

	public function update_table_system_registry__site()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry_site'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry_site');
		}

		if ($db->table_exists('system_registry__site'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__site');
		}

		$db("RENAME TABLE `system_registry_site` TO `system_registry__site`");
	}

	public function update_table_system_registry__user()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry_user'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry_user');
		}

		if ($db->table_exists('system_registry__user'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__user');
		}

		$db("RENAME TABLE `system_registry_user` TO `system_registry__user`");
	}
}

/**
 * - Renames the `system_registry` table as `registry`
 * - Renames the `system_registry__node` table as `registry__node`
 * - Renames the `system_registry__site` table as `registry__site`
 * - Renames the `system_registry__user` table as `registry__user`
 *
 * @module registry
 */
class Update20120922 extends Update
{
	public function update_table_registry()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry');
		}

		if ($db->table_exists('registry'))
		{
			throw new AssertionFailed('assert_not_table_exists', 'registry');
		}

		$db("RENAME TABLE `system_registry` TO `registry`");
	}

	public function update_table_registry_node()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry__node'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__node');
		}

		if ($db->table_exists('registry__node'))
		{
			throw new AssertionFailed('assert_not_table_exists', 'registry__node');
		}

		$db("RENAME TABLE `system_registry__node` TO `registry__node`");
	}

	public function update_table_registry_site()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry__site'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__site');
		}

		if ($db->table_exists('registry__site'))
		{
			throw new AssertionFailed('assert_not_table_exists', 'registry__site');
		}

		$db("RENAME TABLE `system_registry__site` TO `registry__site`");
	}

	public function update_table_registry_user()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry__user'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__user');
		}

		if ($db->table_exists('registry__user'))
		{
			throw new AssertionFailed('assert_not_table_exists', 'registry__user');
		}

		$db("RENAME TABLE `system_registry__user` TO `registry__user`");
	}
}
