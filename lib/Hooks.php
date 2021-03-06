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

use ICanBoogie\ActiveRecord;
use ICanBoogie\Operation;

class Hooks
{
	/*
	 * Events
	 */

	static private function resolve_type($source)
	{
		if ($source instanceof \Icybee\Modules\Nodes\Module
		|| $source instanceof \Icybee\Modules\Nodes\Operation\SaveOperation)
		{
			return 'node';
		}

		if ($source instanceof \Icybee\Modules\Users\Module
		|| $source instanceof \Icybee\Modules\Users\Operation\SaveOperation)
		{
			return 'user';
		}

		if ($source instanceof \Icybee\Modules\Sites\Module
		|| $source instanceof \Icybee\Modules\Sites\Operation\SaveOperation)
		{
			return 'site';
		}

		throw new \Exception(\ICanBoogie\format('Metadatas are not supported for instances of the given class: %class', [

			'%class' => get_class($source)

		]));
	}

	/**
	 * This callback alters the edit block of the "nodes", "users" and "sites" modules, adding
	 * support for metadatas by loading the metadatas associated with the edited object and
	 * merging them with the current properties.
	 *
	 * @param \Icybee\Block\EditBlock\AlterValuesEvent $event
	 * @param \Icybee\Block\EditBlock $target
	 *
	 * @throw \Exception
	 */
	static public function on_editblock_alter_values(\Icybee\Block\EditBlock\AlterValuesEvent $event, \Icybee\Block\EditBlock $target)
	{
		if (!$event->key)
		{
			return;
		}

		/* @var $model ActiveRecord\Model */

		$module = $event->module;
		$type = self::resolve_type($module);
		$model = ActiveRecord\get_model('registry/' . $type);

		$metas = $model
		->select('name, value')
		->filter_by_targetid($event->key)
		->pairs;

		$values = &$event->values;

		if (isset($values['metas']))
		{
			if ($values['metas'] instanceof MetaCollection)
			{
				$values['metas'] = $values['metas']->to_aray();
			}

			$values['metas'] += $metas;
		}
		else
		{
			$values['metas'] = $metas;
		}
	}

	/**
	 * This callback saves the metadatas associated with the object targeted by the operation.
	 *
	 * @param Operation\ProcessEvent $event
	 * @param \ICanBoogie\Module\Operation\SaveOperation $target
	 *
	 * @throws \Exception
	 */
	static public function on_operation_save(Operation\ProcessEvent $event, \ICanBoogie\Module\Operation\SaveOperation $target)
	{
		$params = $event->request->params;

		if (!array_key_exists('metas', $params))
		{
			return;
		}

		/* @var $model ActiveRecord\Model */

		$targetid = $event->rc['key'];
		$type = self::resolve_type($target);

		$model = ActiveRecord\get_model('registry/' . $type);
		$driver_name = $model->connection->driver_name;
		$delete_statement = '';
		$update_groups = [];
		$delete_args = [];

		foreach ($params['metas'] as $name => $value)
		{
			if (is_array($value))
			{
				$value = serialize($value);
			}
			else if (!strlen($value))
			{
				$value = null;

				$delete_statement .= ', ?';
				$delete_args[] = $name;

				continue;
			}

			if ($driver_name == 'sqlite')
			{
				$update_groups[] = [ $targetid, $name, $value ];
			}
			else
			{
				$update_groups[] = [ $targetid, $name, $value, $value ];
			}
		}

		$model->connection->begin();

		if ($delete_statement)
		{
			array_unshift($delete_args, $targetid);

			$delete_statement = 'DELETE FROM {self} WHERE targetid = ? AND name IN (' . substr($delete_statement, 2) . ')';

			$model->execute($delete_statement, $delete_args);
		}

		if ($update_groups)
		{
			if ($driver_name == 'sqlite')
			{
				$update = $model->prepare('INSERT OR REPLACE INTO {self} (targetid, name, value) VALUES(?,?,?)');
			}
			else
			{
				$update = $model->prepare('INSERT INTO {self} (targetid, name, value) VALUES(?,?,?) ON DUPLICATE KEY UPDATE value = ?');
			}

			foreach ($update_groups as $values)
			{
				$update->execute($values);
			}
		}

		$model->connection->commit();
	}

	/**
	 * Deletes the metadatas associated with a record when it is deleted.
	 *
	 * @param \ICanBoogie\Operation\ProcessEvent $event
	 * @param \ICanBoogie\Module\Operation\DeleteOperation $operation
	 *
	 * @throws \Exception
	 */
	static public function on_operation_delete(\ICanBoogie\Operation\ProcessEvent $event, \ICanBoogie\Module\Operation\DeleteOperation $operation)
	{
		$module = $operation->module;
		$type = self::resolve_type($module);

		ActiveRecord\get_model('registry/' . $type)
		->filter_by_targetid($operation->key)
		->delete();
	}

	/*
	 * Prototypes
	 */

	/**
	 * This is the callback for the `metas` virtual property added to the "nodes", "users" and
	 * "sites" active records.
	 *
	 * @param \Icybee\Modules\Nodes\Node|\Icybee\Modules\Users\User|\Icybee\Modules\Sites\Site $target
	 *
	 * @return MetaCollection A {@link MetaCollection} instance.
	 */
	static public function get_metas(\ICanBoogie\ActiveRecord $target)
	{
		return new MetaCollection($target);
	}

	/**
	 * This is the callback for the `registry` virtual property added to the core object.
	 *
	 * @param \ICanBoogie\Application $app
	 *
	 * @return Module The "registry" model.
	 */
	static public function get_registry(\ICanBoogie\Application $app)
	{
		return $app->models['registry'];
	}
}
