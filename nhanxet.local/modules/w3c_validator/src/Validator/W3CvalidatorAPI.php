<?php

/**
 * @file
 * Contains \Drupal\w3c_validator\Validator\W3cValidatorApi
 *
 * This class helps validating a page using W3cValidator regardless it is an
 * online or offline version.
 */
namespace Drupal\w3c_validator\Validator;

use Drupal\Core\Url;
use Drupal\w3c_validator\Validator\Response;
use GuzzleHttp\ClientInterface;

class W3cValidatorApi {

  /**
   * URI to the W3C validator.
   *
   * @var string
   */
  protected $baseUrl = 'http://validator.w3.org/check';

  /**
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Output format
   * Triggers the various outputs formats of the validator.
   * - If unset, the usual Web format will be sent.
   * - If set to soap12, the SOAP1.2 interface will be triggered.
   * - If set to json, the JSON output will be triggered.
   */
  protected $output = 'soap12';

  /**
   * W3cValidatorApi constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   An HTTP client interface.
   */
  function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * Build the full validation URL to call W3C.
   *
   * @param array $settings
   *   - uri : The URL to validate.
   *   - fragment: The source code to validate.
   *
   * @return \Drupal\Core\Url The URL ready to call.
   */
  protected function buildValidationUrl($settings) {
    $data = array(
      'output' => $this->output
    );
    if (isset($settings['uri'])) {
      $data += array(
        'uri' => $settings['uri']
      );
    }
    if (isset($settings['fragment'])) {
      $data += array(
        'fragment' => $settings['fragment']
      );
    }
    return Url::fromUri($this->baseUrl, array(
      'query' => $data
    ));
  }

  /**
   * Validate the input URI.
   *
   * @param string $fragment
   *   the URL to validate.
   *
   * @return \Drupal\w3c_validator\Validator\Response
   *   A response containing erros and warnings.
   */
  public function validateUrl($fragment) {

    // Build the callUrl to call W3C validator.
    // $settings = array('form_params' => array('fragment' => $fragment));
    // $validation_url = $this->buildValidationUrl()->toString();

    // Call the W3Cvalidator WS.
    try {
      $response = $this->httpClient->post('http://validator.w3.org/check',
        array('multipart' => array(
        array(
          'name' => 'fragment',
          'contents' => $fragment
        ),
        array(
          'name' => 'output',
          'contents' => 'soap12'
        )
      )));
      $data = $response->getBody()->getContents();
    } catch(\Exception $e) {
        watchdog_exception('w3c_validator', $e);
        return NULL;
    }

    // Analyze the retrieved data to build-up a comprehensive response.
    return $this->parseSOAP12Response($data);
  }

  /**
   * Parse an XML response from the validator
   *
   * This function parses a SOAP 1.2 response xml string from the validator.
   *
   * @param string $xml
   *   The raw soap12 XML response from the validator.
   *
   * @return \Drupal\w3c_validator\Validator\Response
   *   The analyzed response.
   */
  protected function parseSOAP12Response($xml) {

    // If the document the answer is incorrect : let it go.
    $doc = new \DOMDocument();
    if (! $doc->loadXML($xml)) {
      return NULL;
    }

    $response = new Response();

    // Get the standard CDATA elements from the response.
    foreach(array(
      'uri',
      'checkedby',
      'doctype',
      'charset'
    ) as $var) {
      $element = $doc->getElementsByTagName($var);
      if ($element->length) {
        $response->$var = $element->item(0)->nodeValue;
      }
    }

    // Handle the bool element validity.
    $element = $doc->getElementsByTagName('validity');
    if ($element->length && $element->item(0)->nodeValue == 'true') {
      $response->validity = TRUE;
    } else {
      $response->validity = FALSE;
    }

    // If response is invalid : get the errors corresponding.
    if (! $response->validity) {
      $errors = $doc->getElementsByTagName('error');
      foreach($errors as $error) {
        $response->errors[] = new Result($error);
      }
    }
    $response->error_count = count($response->errors);

    // Get the eventual warnings.
    $warnings = $doc->getElementsByTagName('warning');
    foreach($warnings as $warning) {
      $response->warnings[] = new Result($warning);
    }
    $response->warning_count = count($response->warnings);

    // Return the response.
    return $response;
  }

  /**
   * Set base URL.
   *
   * @param string $base_url
   *          The base URL.
   */
  public function setBaseUrl($base_url) {
    $this->baseUrl = $base_url;
  }
}
