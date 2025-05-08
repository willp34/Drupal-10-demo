<?php
namespace Drupal\store_locator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Provides a default form.
 */
class CustomerDetailsForm extends FormBase {

 /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'customer_form';
  }
    /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Create a select field that will update the contents
    // of the textbox below.
	$form['custom_intro'] = [
	  '#markup' => '<h2>Welcome to the customer form!</h2><p>Please select an option below:</p>',
	];
    $form['example_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select element'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
        '4' => $this->t('From New York to Ger-ma-ny!'),
      ], 
	  '#ajax' => [
			'callback' => '::myAjaxCallback', // don't forget :: when calling a class method.
			//'callback' => [$this, 'myAjaxCallback'], //alternative notation
			'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
			'event' => 'change',
			'wrapper' => 'edit-output', // This element is updated with this AJAX callback.
			'progress' => [
			  'type' => 'throbber',
			  'message' => $this->t('Verifying entry...'),
			],
		  ]
    ];

    // Create a textbox that will be updated
    // when the user selects an item from the select box above.
	 $form['output'] = [
	  '#type' => 'textfield',
	  '#size' => '60',
	  '#disabled' => TRUE,
	  '#value' => 'Hello, Drupal!!1',      
	  '#prefix' => '<div id="edit-output">',
	  '#suffix' => '</div>',
	];
	
	
	$form['customer_info'] = [
  '#type' => 'details',
  '#title' => $this->t('Customer Information'),
  '#open' => TRUE, // set to FALSE if you want it collapsed by default
	];

	$form['customer_info']['first_name'] = [
	  '#type' => 'textfield',
	  '#title' => $this->t('First Name'),
	];

	$form['customer_info']['last_name'] = [
	  '#type' => 'textfield',
	  '#title' => $this->t('Last Name'),
	];

	$form['customer_info']['email'] = [
	  '#type' => 'email',
	  '#title' => $this->t('Email'),
	];
	
    // Create the submit button.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
		// If there's a value submitted for the select list let's set the textfield value.
if ($selectedValue = $form_state->getValue('example_select')) {
  // Get the text of the selected option.
  $selectedText = $form['example_select']['#options'][$selectedValue];
  // Place the text of the selected option in our textfield.
  $form['output']['#value'] = $selectedText;
}
    return $form;
  }
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addStatus($key . ': ' . $value);
    }
  }
  // Get the value from example select field and fill
// the textbox with the selected text.
public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
  // Return the prepared textfield.
  return $form['output']; 
}
}

?>