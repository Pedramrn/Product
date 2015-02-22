<?php namespace Jiro\Product\Console;

use Illuminate\Console\Command;
use Jiro\Support\Migration\MigrationCreator;

class InstallCommand extends Command {

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
	protected $description = 'Install the product package';	

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->createMigrations();

		$this->info('Product migrations created successfully!');
	}

	/**
	 * Create and move the migration files to app directory
	 *
	 * @return void
	 */
	public function createMigrations()
	{	
		$source = __DIR__ . '/../Migrations';

        $destination = $this->laravel['path.database'].'/migrations';

		$migrationCreator = new MigrationCreator($source, $destination);

		$migrationCreator->createMigrations();
	}
}
