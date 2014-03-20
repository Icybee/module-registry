<?php

namespace Icybee\Modules\Registry;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module;

return [

	Module::T_CATEGORY => 'features',
	Module::T_DESCRIPTION => 'Holds configuration settings as well as metadatas for nodes, users and sites.',

	Module::T_MODELS => [

		'primary' => [

			Model::SCHEMA => [

				'fields' => [

					'name' => [ 'varchar', 'primary' => true ],
					'value' => 'text'

				]

			]

		],

		'node' => [

			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => [

				'fields' => [

					'targetid' => [ 'foreign', 'primary' => true ],
					'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
					'value' => 'text'

				]

			]

		],

		'user' => [

			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => [

				'fields' => [

					'targetid' => [ 'foreign', 'primary' => true ],
					'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
					'value' => 'text'

				]

			]

		],

		'site' => [

			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => [

				'fields' => [

					'targetid' => [ 'foreign', 'primary' => true ],
					'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
					'value' => 'text'

				]

			]

		]

	],

	Module::T_NAMESPACE => __NAMESPACE__,
	Module::T_PERMISSION => false,
	Module::T_REQUIRED => true,
	Module::T_TITLE => 'Registry',
	Module::T_VERSION => '1.0'
];