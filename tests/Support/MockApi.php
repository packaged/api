<?php
namespace Packaged\Api\Tests\Support;

use Packaged\Api\Abstracts\AbstractApi;

class MockApi extends AbstractApi
{
  protected $_url;

  function __construct($url, array $guzzleConfig = [])
  {
    $this->_url = $url;
    $this->_guzzleConfig = $guzzleConfig;
  }

  /**
   * Retrieve the url for the API
   *
   * @return string
   */
  public function getUrl()
  {
    return $this->_url;
  }
}
