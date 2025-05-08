<?php
namespace Drupal\store_locator\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\store_locator\Form\CustomerDetailsForm;
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
	  
	
	 $simpleform = \Drupal::formBuilder()->getForm(CustomerDetailsForm::class);
    return $simpleform;
  }

}