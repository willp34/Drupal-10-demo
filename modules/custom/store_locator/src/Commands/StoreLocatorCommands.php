<?php   

namespace Drupal\store_locator\Commands;

use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


use Drupal\store_locator\Service\CsvImporter;
/**
 * Defines Drush commands for Store Locator.
 */
class StoreLocatorCommands extends DrushCommands {
  
  protected ?LoggerInterface $logger;
  protected $csvImporter;
 /**
   * Constructor.
   */
 public function __construct(LoggerChannelFactoryInterface $loggerFactory, CsvImporter $csvImporter) {
        $this->logger = $loggerFactory->get('store_locator'); // Get a logger channel
		$this->csvImporter = $csvImporter;
    }
/**
   * Factory method for dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
	  $container->get('store_locator.csv_processor')
    );
  }

 #[CLI\Command(name: 'store_locator:say-hello')]
  #[CLI\Option(name: 'name', description: 'Your name')]
  #[CLI\Usage(name: 'drush store_locator:say-hello --name=John', description: 'Prints a hello message.')]
  #[CLI\Help(description: 'Prints a hello message.', synopsis: 'Example: drush store_locator:say-hello --name=John')]
  
  public function sayHello(array $options = ['name' => '']): void {
    $name = $options['name'] ?: 'world';
    $message = "Hello, $name!";
	
    $this->logger->notice($message);
    $this->output()->writeln($message);
  }
   
   
    #[CLI\Command(name: 'store_locator:import-file')]
	#[CLI\Usage(name: 'drush store_locator:import-file --dry-run', description: 'Import file and store data into content ty Store.')]
	#[CLI\Help(description: 'Import stores from CSV file', synopsis: 'Example: Store.csv')]
 
	public function importFile( array $options = ['file' => NULL, 'dry-run' => FALSE]){
	   $file_path = DRUPAL_ROOT . '\instructions\uk_stores.csv';
	   if ($options['dry-run']) {
		  $this->output()->writeln("<info>Dry run mode: No changes will be made.</info>");
		} else {
		   $this->csvImporter->readCsvIndex($file_path);
		   $this->output()->writeln("Import file");
		}
   }
}

