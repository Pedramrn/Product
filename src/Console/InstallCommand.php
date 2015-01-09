<?php namespace Jiro\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

///////////// SPEC ME OUT
///////////// SPEC ME OUT
///////////// SPEC ME OUT
///////////// SPEC ME OUT

class InstallCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'jiro:product:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the database migrations';	

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $fileSystem; 

	/**
	 * Create a new session table command instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem $fileSystem
	 * @return void
	 */
	public function __construct(Filesystem $fileSystem)
	{
		parent::__construct();

		$this->files = $fileSystem;
	}	

	/**
	 * Returns the migrations that we need to generate
	 *
	 * @return array
	 */
	protected static function getMigrations()
	{
		return [
			'create_products_table' => 'products.stub',
			'create_properties_table' => 'properties.stub',
			'create_property_values_table' => 'property_values.stub',
			'create_product_property_table' => 'product_property.stub',
		];
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->createMigrations();

		$this->info('Product migrations created successfully!');

		$this->composer->dumpAutoloads();
	}

	/**
	 * Create a base migration files for the tables.
	 *
	 * @return string
	 */
	protected function createMigrations()
	{
		foreach(self::getMigrations() as $migrationName => $stubPath)
		{
			$path = $this->createMigrationFile($migrationName);

			$this->createMigrationContent($path, $stubPath);
		}
	}

	/**
	 * Creates the filename and file for the migration
	 *
	 * @param  string  $migrationName
	 * @return string
	 */
	protected function createMigrationFile($migrationName)
	{
		$path = $this->laravel['path.database'].'/migrations';

		return $this->laravel['migration.creator']->create($migrationName, $path);		
	}

	/**
	 * Creates the content of the migration from a stub
	 *
	 * @param  string  $path
	 * @param  string  $stubPath
	 * @return void
	 */
	protected function createMigrationContent($path, $stubPath)
	{
		$migrationStub = $this->fileSystem->get(__DIR__.'/../Migrations/'.$stubPath);

		$this->fileSystem->put($path, $migrationStub);
	}

}
