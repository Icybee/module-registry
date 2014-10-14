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

use ICanBoogie\Updater\Update;
use ICanBoogie\Updater\AssertionFailed;

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

		$db("RENAME TABLE `system_registry` TO `registry`");
	}

	public function update_table_registry_node()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry__node'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__node');
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

		$db("RENAME TABLE `system_registry__site` TO `registry__site`");
	}

	public function update_table_registry_user()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_registry__user'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_registry__user');
		}

		$db("RENAME TABLE `system_registry__user` TO `registry__user`");
	}
}