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
use ICanBoogie\Exception;

class Hooks
{
	/*
	 * Events
	 */

	/**
	 * This callback alters the edit block of the "nodes", "users" and "sites" modules, adding
	 * support for metadatas by loading the metadatas associated with the edited object and
	 * merging them with the current properties.
	 *
	 * @param Event $event
	 *
	 * @throws Exception
	 */
	static public function on_editblock_alter_values(\Icybee\EditBlock\AlterValuesEvent $event, \Icybee\EditBlock $target)
	{
		if (!$event->key)
		{
			return;
		}

		$module = $event->module;

		if ($module instanceof \Icybee\Modules\Nodes\Module)
		{
			$type = 'node';
		}
		else if ($module instanceof \Icybee\Modules\Users\Module)
		{
			$type = 'user';
		}
		else if ($module instanceof \Icybee\Modules\Sites\Module)
		{
			$type = 'site';
		}
		else
		{
			throw new Exception('Metadatas are not supported for instances of the given class: %class', [ '%class' => get_class($target) ]);
		}

		ActiveRecord\get_model('registry/' . $type)
		->select('name, value')
		->filter_by_targetid($event->key)
		->pairs;

		$values = &$event->values;

		if (isset($values['metas']))
		{
			if ($values['metas'] instanceof MetasHandler)
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
	 * @param Event $event
	 *
	 * @throws Exception
	 */
	static public function on_operation_save(\ICanBoogie\Operation\ProcessEvent $event, \ICanBoogie\SaveOperation $sender)
	{
		$params = $event->request->params;

		if (!array_key_exists('metas', $params))
		{
			return;
		}

		$targetid = $event->rc['key'];

		if ($sender instanceof \Icybee\Modules\Nodes\SaveOperation)
		{
			$type = 'node';
		}
		else if ($sender instanceof \Icybee\Modules\Users\SaveOperation)
		{
			$type = 'user';
		}
		else if ($sender instanceof \Icybee\Modules\Sites\SaveOperation)
		{
			$type = 'site';
		}
		else
		{
			throw new Exception('Metadatas are not supported for instances of the given class: %class', [ '%class' => get_class($sender) ]);
		}

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
	 * @param \ICanBoogie\DeleteOperation $operation
	 *
	 * @throws Exception
	 */
	static public function on_operation_delete(\ICanBoogie\Operation\ProcessEvent $event, \ICanBoogie\DeleteOperation $operation)
	{
		$module = $operation->module;

		if ($module instanceof \Icybee\Modules\Nodes\Module)
		{
			$type = 'node';
		}
		else if ($module instanceof \Icybee\Modules\Users\Module)
		{
			$type = 'user';
		}
		else if ($module instanceof \Icybee\Modules\Sites\Module)
		{
			$type = 'site';
		}
		else
		{
			throw new Exception('Metadatas are not supported for instances of the given class: %class', [ '%class' => get_class($module) ]);
		}

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
	 * @return MetasHandler A {@link MetasHandler} instance.
	 */
	static public function get_metas(\ICanBoogie\ActiveRecord $target)
	{
		return new MetasHandler($target);
	}

	/**
	 * This si the callback for the `registry` virtual property added to the core object.
	 *
	 * @param \ICanBoogie\Core $target The core object.
	 *
	 * @return Module The "registry" model.
	 */

	static public function get_registry(\ICanBoogie\Core $target)
	{
		return $target->models['registry'];
	}
}