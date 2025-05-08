<?php 
namespace Drupal\store_locator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


use Drupal\file\Entity\File;


use Drupal\store_locator\Service\CsvImporter;
/**
 * Provides a form for importing store data via CSV.
 */
class StoreLocatorImportForm extends FormBase {

  protected $csvImporter;

  public function __construct(CsvImporter $csvImporter) {
    $this->csvImporter = $csvImporter;
  }
  
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('store_locator.csv_importer')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'store_locator_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['notes'] = [
      '#type' => 'markup',
      '#markup' => '<h2>Paste your CSV data in the text area below</h2>',
    ];

    $form['csv_data'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload CSV File'),
      '#description' => $this->t('Upload a CSV file. Allowed formats: .csv'),
      '#upload_location' => 'public://csv_uploads/',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file_ids = $form_state->getValue('csv_data'); // This is an array

    if (!empty($file_ids) && is_array($file_ids)) {
        $file_id = reset($file_ids); // Get the first file ID

        if ($file_id) {
            $file = File::load($file_id);
            if ($file) {
                $file->setPermanent(); // Optional: Make the file permanent
				
                $file->save();
				//\Drupal::service('file.usage')->add($file, 'store_locator', 'user', \Drupal::currentUser()->id());
                 $file_path = \Drupal::service('file_system')->realpath($file->getFileUri());
      
				  // Read and process CSV file


				
				//Print CSV content (use dpm() from Devel module for debugging)
				 $this->csvImporter->readCsvIndex($file_path);
				   //// Option 2: Delete existing file
				\Drupal::service('file_system')->delete($file->getFileUri());
				 
				 \Drupal::messenger()->addMessage($this->t('CSV file processed successfully.'));
			
            }
        }
    }
}



}
?>