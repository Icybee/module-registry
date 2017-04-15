<?php

namespace Icybee\Modules\Registry;

use ICanBoogie\ActiveRecord;
use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return [

	Descriptor::CATEGORY => 'features',
	Descriptor::DESCRIPTION => 'Holds configuration settings as well as metadatas for nodes, users and sites.',
	Descriptor::ID => 'registry',
	Descriptor::MODELS => [

		'primary' => [

			Model::SCHEMA => [

				'name' => [ 'varchar', 'primary' => true ],
				'value' => 'text'

			]

		],

		'node' => [

			Model::ACTIVERECORD_CLASS => ActiveRecord::class,
			Model::CLASSNAME => Model::class,
			Model::SCHEMA => [

				'targetid' => [ 'foreign', 'primary' => true ],
				'name' => [ 'varchar', 'indexed' => true, 'primary' => true ],
				'value' => 'text'

			]

		],

		'user' => [

			Model::ACTIVERECORD_CLASS => ActiveRecord::class,
			Model::CLASSNAME => Model::class,
			Model::SCHEMA => [

				'targetid' => [ 'foreign', 'primary' => true ],
				'name' => [ 'varchar', 'primary' => true ],
				'value' => 'text'

			]

		],

		'site' => [

			Model::ACTIVERECORD_CLASS => ActiveRecord::class,
			Model::CLASSNAME => Model::class,
			Model::SCHEMA => [

				'targetid' => [ 'foreign', 'primary' => true ],
				'name' => [ 'varchar', 'primary' => true ],
				'value' => 'text'

			]

		]

	],

	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSION => false,
	Descriptor::TITLE => "Registry"

];
