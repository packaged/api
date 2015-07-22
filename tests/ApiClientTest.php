<?php
namespace Packaged\Api\Tests;

use GuzzleHttp\Post\PostBody;
use GuzzleHttp\Ring\Client\MockHandler;
use Packaged\Api\ApiRequest;
use Packaged\Api\Format\JsonFormat;
use Packaged\Api\HttpVerb;
use Packaged\Api\Tests\Support\MockApi;
use Packaged\Api\Tests\Support\MockEndpoint;
use Packaged\Api\Tests\Support\MockException;
use Packaged\Api\Tests\Support\MockHeaderResponse;
use Packaged\Api\Tests\Support\MockPayload;
use Packaged\Api\Tests\Support\MockResponse;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{
  protected function _encode(
    $result, $statusCode = 200, $statusMessage = '', $type = null
  )
  {
    return (new JsonFormat())->encode(
      $result,
      $statusCode,
      $statusMessage,
      $type
    );
  }

  protected function _getApi($handlerResult, array $headers = null)
  {
    if(!is_callable($handlerResult))
    {
      $handlerResult = [
        'body'   => $this->_encode($handlerResult),
        'status' => 200
      ];
    }
    $handler = new MockHandler($handlerResult);
    $config = ['handler' => $handler];
    if($headers)
    {
      $config['defaults']['headers'] = $headers;
    }
    return new MockApi('http://www.test.com', $config);
  }

  protected function _getEndpoint($handlerResult, array $headers = null)
  {
    $api = $this->_getApi($handlerResult, $headers);
    return MockEndpoint::bound($api);
  }

  public function testGet()
  {
    $response = new MockResponse();
    $response->key1 = 'value1';
    $response->key2 = 'value2';

    $endpoint = $this->_getEndpoint($response);
    $apiResult = $endpoint->getRequest()->get();
    $this->assertInstanceOf(
      '\Packaged\Api\Tests\Support\MockResponse',
      $apiResult
    );
    $this->assertEquals($response->toArray(), $apiResult->toArray());
  }

  public function testPost()
  {
    $endpoint = $this->_getEndpoint(
      function ($request)
      {
        $body = $request['body'];
        if($body instanceof PostBody)
        {
          return [
            'body'   => $this->_encode(MockResponse::make($body->getFields())),
            'status' => 200
          ];
        }
        return null;
      }
    );
    $payload = new MockPayload();
    $payload->notNullField = 'a';
    $payload->key1 = 'value';
    $payload->key2 = 'vals';
    $apiResult = $endpoint->getRequest($payload, '/', HttpVerb::POST)->get();
    $this->assertInstanceOf(
      '\Packaged\Api\Tests\Support\MockResponse',
      $apiResult
    );
    $this->assertEquals($payload->toArray(), $apiResult->toArray());
  }

  public function testGlobalHeaders()
  {
    $endpoint = $this->_getEndpoint(
      function ($request)
      {
        return [
          'body'   => (new JsonFormat())->encode(
            MockHeaderResponse::make($request['headers'])
          ),
          'status' => 200
        ];
      },
      ['header1' => 'val', 'head2' => 'val2']
    );
    $apiResult = $endpoint->getRequest()->get();
    /**
     * @var MockHeaderResponse $apiResult
     */
    $this->assertInstanceOf(
      '\Packaged\Api\Tests\Support\MockHeaderResponse',
      $apiResult
    );
    $this->assertArrayHasKey('header1', $apiResult->toArray());
    $this->assertArrayHasKey('head2', $apiResult->toArray());
    $this->assertEquals([0 => 'val2'], $apiResult->head2);
  }

  /**
   * @expectedException \GuzzleHttp\Exception\RequestException
   * @expectedExceptionMessageRegExp /(\[curl\] \(#6\))|(cURL error 6)/
   */
  public function testInvalidDomain()
  {
    $api = new MockApi('http://invalid.this-domain-does-not-exist.co.test');
    $request = ApiRequest::create()->setApi($api);
    $request->get();
  }

  /**
   * @expectedException \Packaged\Api\Tests\Support\MockException
   * @expectedExceptionMessage Oops
   * @expectedExceptionCode    1050
   */
  public function testException()
  {
    $endpoint = $this->_getEndpoint(
      function ()
      {
        $e = new MockException('Oops', 1050);
        return [
          'body'   => $e->getFormatted(new JsonFormat()),
          'status' => 200
        ];
      }
    );
    $payload = new MockPayload();
    $payload->key1 = 'value';
    $payload->key2 = 'vals';
    $endpoint->getRequest($payload, '/', HttpVerb::POST)->get();
  }
}
