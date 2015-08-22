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

$hooks = Hooks::class . '::';

return [

	'events' => [

		'Icybee\Modules\Nodes\EditBlock::alter_values' => $hooks . 'on_editblock_alter_values',
		'Icybee\Modules\Users\EditBlock::alter_values' => $hooks . 'on_editblock_alter_values',
		'Icybee\Modules\Sites\EditBlock::alter_values' => $hooks . 'on_editblock_alter_values',

		'Icybee\Modules\Nodes\SaveOperation::process' => $hooks . 'on_operation_save',
		'Icybee\Modules\Users\SaveOperation::process' => $hooks . 'on_operation_save',
		'Icybee\Modules\Sites\SaveOperation::process' => $hooks . 'on_operation_save',

		'Icybee\Modules\Nodes\DeleteOperation::process' => $hooks . 'on_operation_delete',
		'Icybee\Modules\Users\DeleteOperation::process' => $hooks . 'on_operation_delete',
		'Icybee\Modules\Sites\DeleteOperation::process' => $hooks . 'on_operation_delete'

	]

];
