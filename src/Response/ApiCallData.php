<?php
namespace Packaged\Api\Response;

class ApiCallData
{
  protected $_execTime;
  protected $_callTime;
  protected $_totalTime;
  protected $_status;
  protected $_type;
  protected $_result;

  public static function create(
    $type, $result, $statusCode, $statusMessage, $totalTime, $executionTime,
    $callTime
  )
  {
    $callData = new static;
    $callData->_setStatus($statusCode, $statusMessage);
    $callData->_setRawResult($result);
    $callData->_setResponseType($type);
    $callData->_totalTime = $totalTime;
    $callData->_execTime  = $executionTime;
    $callData->_callTime  = $callTime;
    return $callData;
  }

  protected function _setRawResult($result)
  {
    $this->_result = $result;
    return $this;
  }

  public function getRawResult()
  {
    return $this->_result;
  }

  protected function _setStatus($code, $message = '')
  {
    $this->_status          = new \stdClass();
    $this->_status->code    = $code;
    $this->_status->message = $message;
    return $this;
  }

  protected function _setResponseType($type)
  {
    $this->_type = $type;
    return $this;
  }

  public function getResponseType()
  {
    return $this->_type;
  }

  /**
   * Get the time taken to process the call internally on the server
   *
   * @return mixed
   */
  public function getCallTime()
  {
    return $this->_callTime;
  }

  /**
   * Get the time taken to process the whole thread on the server
   *
   * @return mixed
   */
  public function getExecutionTime()
  {
    return $this->_execTime;
  }

  /**
   * Get the amount of time spent in the network
   *
   * @return mixed
   */
  public function getTransportTime()
  {
    return $this->_totalTime - $this->_execTime;
  }

  /**
   * Get the total time taking to make and retrieve the request
   *
   * @return mixed
   */
  public function getTotalTime()
  {
    return $this->_totalTime;
  }

  /**
   * Error message
   *
   * @return mixed
   */
  public function getStatusMessage()
  {
    return $this->_status->message;
  }

  /**
   * status code
   *
   * @return mixed
   */
  public function getStatusCode()
  {
    return $this->_status->code;
  }

  /**
   * Did the call error?
   *
   * @return bool
   */
  public function isError()
  {
    return $this->_status->code !== 200;
  }
}
