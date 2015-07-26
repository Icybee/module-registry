<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

use ICanBoogie\Module\CoreBindings as ModuleBindings;

require __DIR__ . '/../vendor/autoload.php';

#
# Create the app used for the tests.
#

/* @var $app Core|ModuleBindings|Binding\ActiveRecord\CoreBindings */

$app = new Core(array_merge_recursive([ 'module-path' => [] ] + get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config' => Autoconfig\Config::CONFIG_WEIGHT_MODULE

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$app->boot();
$errors = $app->modules->install();

foreach ($errors as $error)
{
	echo $error . PHP_EOL;
}
