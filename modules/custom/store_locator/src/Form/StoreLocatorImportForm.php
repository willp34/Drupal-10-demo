<?php 
namespace Drupal\store_locator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileUsage\FileUsageInterface;

use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityTypeManagerInterface;
/**
 * Provides a form for importing store data via CSV.
 */
class StoreLocatorImportForm extends FormBase {

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
				\Drupal::service('file.usage')->add($file, 'store_locator', 'user', \Drupal::currentUser()->id());
                 $file_path = \Drupal::service('file_system')->realpath($file->getFileUri());
      
				  // Read and process CSV file
				 $this->readCsv_index($file_path);

				  //// Option 2: Delete existing file
				\Drupal::service('file_system')->delete($file->getFileUri());
				//Print CSV content (use dpm() from Devel module for debugging)
				 
				 \Drupal::messenger()->addMessage($this->t('CSV file processed successfully.'));
			
            }
        }
    }
}
  /**
   * Reads a CSV file and returns its contents as an array.
   */
private function readCsv($file_path) {
    $csv_data = [];

  if (!file_exists($file_path) || !is_readable($file_path)) {
    \Drupal::messenger()->addError("CSV file not found or unreadable: $file_path");
    return $csv_data;
  }

  if (($handle = fopen($file_path, 'r')) !== FALSE) {
    $headers = fgetcsv($handle);

    if ($headers === FALSE) {
      \Drupal::messenger()->addError("Failed to read CSV headers.");
      return $csv_data;
    }

    // Debug: Print headers
    dpm($headers);

    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
      // Debug: Print each row
      dpm($row);

      if (count($row) == count($headers)) {
        $csv_data[] = array_combine($headers, $row);
      } else {
        dpm("Skipping row due to column mismatch: " . implode(", ", $row));
      }
    }
    fclose($handle);
  }
  return $csv_data;
}

/**
 * Reads a CSV file and returns its contents as an indexed array.
 */
	private function readCsv_index($file_path) {
	$csv_data = [];

  // Check if file exists and is readable
  if (!file_exists($file_path) || !is_readable($file_path)) {
    \Drupal::messenger()->addError("CSV file not found or unreadable: $file_path");
    return $csv_data;
  }

  // Read file contents safely
  $storedata = preg_split('/\r\n|\r|\n/', file_get_contents($file_path));
	if (empty($storedata)) {
		\Drupal::messenger()->addError("CSV file is empty.");
		return [];
	}
  // Split headers into an array
  $headers = str_getcsv(array_shift($storedata)); // First row becomes headers

  // Loop through each row, starting from the second line
  foreach ($storedata as $storeItem) {
    // Avoid empty lines
    if (!empty(trim($storeItem))) {
	
      // Split the row into columns and combine with headers
      $row_data = str_getcsv($storeItem); // Split the row into columns
     
	  $storage = \Drupal::service('entity_type.manager')->getStorage('node');
		$values = $storage->loadByProperties(['title' => $row_data[0]]);
		if(empty($values)){
			// if node does not exist create new node
			$new_store =array(
			'type'       => 'store',
			'title' => $row_data[0],
			'body' => [
				  'value' => 'body text',
				  'format' => 'full_html',
				],
        'field_address' => $row_data[1], // Adjust based on your fields
		'field_phone_number' => $row_data[2], // Adjust based on your fields
		'field_store_manager' => $row_data[3], // Adjust based on your fields
		'field_store_type' => $row_data[4], // Adjust based on your fields
	
        'status' => 1,
			);
			$node = Node::create($new_store);
			$node->save();
		}else {
				$node = reset($values);

				if ($node instanceof Node) {
					$nid = $node->id();
					$address = urlencode($row_data[0]);

					// Fetch long & lat safely
					$request = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=" . $address . "&sensor=false&key=AIzaSyDkn1amsOkmbqBDQl0uJlorDNfRYbnk2u8");
					$json = json_decode($request, true);

					if (empty($json['results'][0]['geometry']['location'])) {
						\Drupal::messenger()->addError("Could not retrieve location data for: " . $address);
						dpm($json);
						continue;
					}

					$longitude = $json['results'][0]['geometry']['location']['lng'];
					$latitude = $json['results'][0]['geometry']['location']['lat'];

					// Fix typo in field name
					$node->set("field_longitude", $longitude);
					$node->set("field_latitude", $latitude);
					$node->save();
				} else {
					\Drupal::messenger()->addError("Error: Could not load the store node for update.");
				} 
      }
    }
  }

 
}
}
?>