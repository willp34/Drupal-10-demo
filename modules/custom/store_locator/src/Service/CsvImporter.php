<?php

namespace Drupal\store_locator\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\ClientInterface;

class CsvImporter {

  protected $entityTypeManager;
  protected $messenger;
  protected $httpClient;
  protected $googleApiKey;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, MessengerInterface $messenger, ClientInterface $httpClient) {
    $this->entityTypeManager = $entityTypeManager;
    $this->messenger = $messenger;
    $this->httpClient = $httpClient;
    $this->googleApiKey = 'AIzaSyDkn1amsOkmbqBDQl0uJlorDNfRYbnk2u8'; // Store API key in config
  }

  public function readCsvIndex($file_path) {
    $csv_data = [];

    if (!file_exists($file_path) || !is_readable($file_path)) {
      $this->messenger->addError("CSV file not found or unreadable: $file_path");
      return [];
    }

    $storedata = preg_split('/\r\n|\r|\n/', file_get_contents($file_path));

    if (empty($storedata)) {
      $this->messenger->addError("CSV file is empty.");
      return [];
    }

    $headers = str_getcsv(array_shift($storedata));

    foreach ($storedata as $storeItem) {
      if (!empty(trim($storeItem))) {
        $row_data = str_getcsv($storeItem);
        $storage = $this->entityTypeManager->getStorage('node');
        $values = $storage->loadByProperties(['title' => $row_data[0]]);

        if (empty($values)) {
          $new_store = [
            'type' => 'store',
            'title' => $row_data[0],
            'body' => ['value' => 'body text', 'format' => 'full_html'],
            'field_address' => $row_data[1],
            'field_phone_number' => $row_data[2],
            'field_store_manager' => $row_data[3],
            'field_store_type' => $row_data[5],
            'status' => 1,
          ];
          $node = Node::create($new_store);
          $node->save();
        } else {
          $node = reset($values);

          if ($node instanceof Node) {
            $address = urlencode($row_data[1]);
            $geo_data = $this->getGeocodeData($address);

            if (!$geo_data) {
              $this->messenger->addError("Could not retrieve location data for: " . $address);
              continue;
            }

            $node->set("field_longitude", $geo_data['lng']);
            $node->set("field_latitude", $geo_data['lat']);
            $node->save();
          } else {
            $this->messenger->addError("Error: Could not load the store node for update.");
          }
        }
      }
    }
  }

  private function getGeocodeData($address) {
    try {
      $response = $this->httpClient->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
        'query' => [
          'address' => $address,
          'sensor' => 'false',
          'key' => $this->googleApiKey,
        ],
      ]);

      $data = json_decode($response->getBody(), true);
      if (!empty($data['results'][0]['geometry']['location'])) {
        return $data['results'][0]['geometry']['location'];
      }
    } catch (\Exception $e) {
      \Drupal::logger('your_module')->error('Google API request failed: @message', ['@message' => $e->getMessage()]);
    }
    return null;
  }
}