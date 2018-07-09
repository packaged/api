<?php
namespace Packaged\Api;

use GuzzleHttp\Promise\PromiseInterface;
use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\Api\Traits\ApiAwareTrait;

class ApiRequest implements ApiRequestInterface
{
  protected $_verb = HttpVerb::GET;
  protected $_path = '/';
  protected $_post = [];
  protected $_querystring;
  protected $_requiresAuth = true;
  protected $_batchId;
  /** @var PromiseInterface */
  protected $_promise;
  protected $_response;

  use ApiAwareTrait;

  /**
   * @return Interfaces\ApiResponseInterface
   */
  public function get()
  {
    $this->_response = $this->getApi()->processRequest($this);
    return $this->_response;
  }

  public function request()
  {
    $this->_promise = $this->getApi()->prepareRequest($this);
    return $this;
  }

  public function getResponse()
  {
    if(!$this->_response && $this->_promise)
    {
      $this->_response = $this->getApi()->processPreparedRequest($this->_promise);
    }
    return $this->_response;
  }

  public function setBatchId($batchId)
  {
    $this->_batchId = $batchId;
    return $this;
  }

  public function getBatchId()
  {
    return $this->_batchId;
  }

  /**
   * @param string $verb
   * @param string $path
   * @param array  $postData
   * @param string $querystring
   * @param bool   $requiresAuth
   *
   * @return static
   */
  public static function create(
    $verb = HttpVerb::POST, $path = '/', $postData = [], $querystring = '',
    $requiresAuth = true
  )
  {
    $request = new static();
    $request->_verb = $verb;
    $request->_path = $path;
    $request->_post = $postData;
    $request->_querystring = $querystring;
    $request->_requiresAuth = $requiresAuth;
    return $request;
  }

  /**
   * Get the HTTP verb required for this request
   *
   * @return string
   */
  public function getVerb()
  {
    return $this->_verb;
  }

  /**
   * Does this request need to be authed
   *
   * @return bool
   */
  public function requiresAuth()
  {
    return $this->_requiresAuth === true;
  }

  /**
   *
   * @return string
   */
  public function getPath()
  {
    return $this->_path;
  }

  public function getPostData()
  {
    return $this->_post;
  }

  public function getQueryString()
  {
    if(is_array($this->_querystring))
    {
      return '?' . http_build_query($this->_querystring);
    }
    return $this->_querystring;
  }
}
