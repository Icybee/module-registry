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
use ICanBoogie\ToArray;
use Icybee\Modules\Nodes\Node;
use Icybee\Modules\Sites\Site;
use Icybee\Modules\Users\User;

/**
 * Manages the metadatas associated with a target object.
 */
class MetasHandler implements \ArrayAccess, ToArray
{
	static private $models;

	/**
	 * Identifier of the target.
	 *
	 * @var int
	 */
	protected $targetid;

	/**
	 * Values associated with the target.
	 *
	 * @var array
	 */
	protected $values;

	/**
	 * Model managing the values.
	 *
	 * @var RegistryModel
	 */
	protected $model;

	/**
	 * Initializes the {@link $targetid} and {@link $model} properties.
	 *
	 * @param \ICanBoogie\ActiveRecord $target
	 *
	 * @throws \Exception
	 */
	public function __construct(ActiveRecord $target)
	{
		if ($target instanceof Node)
		{
			$this->targetid = $target->nid;
			$type = 'node';
		}
		else if ($target instanceof User)
		{
			$this->targetid = $target->uid;
			$type = 'user';
		}
		else if ($target instanceof Site)
		{
			$this->targetid = $target->siteid;
			$type = 'site';
		}
		else
		{
			throw new \Exception(\ICanBoogie\format('Metadatas are not supported for instances of %class', [ '%class' => get_class($target) ]));
		}

		if (empty(self::$models[$type]))
		{
			self::$models[$type] = ActiveRecord\get_model('registry/' . $type);
		}

		$this->model = self::$models[$type];
	}

	public function get($name, $default=null)
	{
		if ($this->values === null)
		{
			$this->values = $this->model
			->select('name, value')
			->filter_by_targetid($this->targetid)
			->order('name')
			->pairs;
		}

		if ($name == 'all')
		{
			return $this->values;
		}

		if (!isset($this->values[$name]))
		{
			return $default;
		}

		return $this->values[$name];
	}

	public function set($name, $value)
	{
		$this->values[$name] = $value;

		if ($value === null)
		{
			$this->model->filter_by_targetid_and_name($this->targetid, $name)->delete();

			return;
		}

		$this->model->insert([

			'targetid' => $this->targetid,
			'name' => $name,
			'value' => $value

		], [ 'on duplicate' => true ]);
	}

	public function to_array()
	{
		return $this->get('all');
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetExists($offset)
	{
		return $this->get($offset) !== null;
	}

	public function offsetUnset($offset)
	{
		$this->set($offset, null);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}
}
