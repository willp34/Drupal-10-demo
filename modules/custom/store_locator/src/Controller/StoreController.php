<?php
namespace Drupal\store_locator\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class StoreController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function import_stores() {
	  
	   \Drupal::messenger()->addMessage($this->t('CSV data received!'));
	 
    return [
      '#markup' => 'Hello, world',
    ];
  }

}