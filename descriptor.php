<?php

namespace Icybee\Modules\Registry;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return [

	Descriptor::CATEGORY => 'features',
	Descriptor::DESCRIPTION => 'Holds configuration settings as well as metadatas for nodes, users and sites.',
	Descriptor::ID => 'registry',
	Descriptor::MODELS => [

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

	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSION => false,
	Descriptor::REQUIRED => true,
	Descriptor::TITLE => 'Registry',
	Descriptor::VERSION => '1.0'
];
