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

use ICanBoogie;
use Icybee;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Application::class . '::lazy_get_registry' => $hooks . 'get_registry',

	Icybee\Modules\Nodes\Node::class . '::lazy_get_metas' => $hooks . 'get_metas',
	Icybee\Modules\Users\User::class . '::lazy_get_metas' => $hooks . 'get_metas',
	Icybee\Modules\Sites\Site::class . '::lazy_get_metas' => $hooks . 'get_metas'

];
