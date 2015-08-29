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

use Icybee;

$hooks = Hooks::class . '::';

return [

	Icybee\Modules\Nodes\EditBlock::class . '::alter_values' => $hooks . 'on_editblock_alter_values',
	Icybee\Modules\Users\EditBlock::class . '::alter_values' => $hooks . 'on_editblock_alter_values',
	Icybee\Modules\Sites\EditBlock::class . '::alter_values' => $hooks . 'on_editblock_alter_values',

	Icybee\Modules\Nodes\Operation\SaveOperation::class . '::process' => $hooks . 'on_operation_save',
	Icybee\Modules\Users\Operation\SaveOperation::class . '::process' => $hooks . 'on_operation_save',
	Icybee\Modules\Sites\Operation\SaveOperation::class . '::process' => $hooks . 'on_operation_save',

	Icybee\Modules\Nodes\Operation\DeleteOperation::class . '::process' => $hooks . 'on_operation_delete',
	Icybee\Modules\Users\Operation\DeleteOperation::class . '::process' => $hooks . 'on_operation_delete',
	Icybee\Modules\Sites\Operation\DeleteOperation::class . '::process' => $hooks . 'on_operation_delete'

];
