<?php
namespace Packaged\ApiExample;

use Packaged\Api\Abstracts\AbstractApi;

class CorpApi extends AbstractApi
{
  /**
   * Retrieve the url for the API
   *
   * @return string
   */
  public function getUrl()
  {
    return 'http://127.0.0.1:8080/';
  }
}
