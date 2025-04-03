<?php   

namespace Drupal\store_locator\Commands;

use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Drush commands for Store Locator.
 */
class StoreLocatorCommands extends DrushCommands {
  
  protected ?LoggerInterface $logger;


 /**
   * Constructor.
   */
 public function __construct(LoggerChannelFactoryInterface $loggerFactory) {
        $this->logger = $loggerFactory->get('store_locator'); // Get a logger channel
    }
/**
   * Factory method for dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory')
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
  #[CLI\Usage(name: 'drush store_locator:import-file ', description: 'Import file and store data into content tpy Store.')]
  #[CLI\Help(description: 'Impport stores from csv file', synopsis: 'Example: Store.csv')]
 
	public function Import_file(){
	   
	   $this->output()->writeln("Import file");
   }
}

