<?php
namespace Packaged\Api\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Packaged\Api\ApiRequest;
use Packaged\Api\Format\JsonFormat;
use Packaged\Api\HttpVerb;
use Packaged\Api\Tests\Support\MockApi;
use Packaged\Api\Tests\Support\MockEndpoint;
use Packaged\Api\Tests\Support\MockException;
use Packaged\Api\Tests\Support\MockHeaderResponse;
use Packaged\Api\Tests\Support\MockPayload;
use Packaged\Api\Tests\Support\MockResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\parse_query;

class ApiClientTest extends TestCase
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
    if(is_callable($handlerResult))
    {
      $handler = new MockHandler(
        [
          function ($request) use ($handlerResult, $headers)
          {
            /** @var RequestInterface $request */
            if(is_array($headers))
            {
              foreach($headers as $k => $v)
              {
                $request = $request->withAddedHeader($k, $v);
              }
            }
            $resp = $handlerResult($request);
            return new Response(
              $resp['status'],
              $headers ?: [],
              $resp['body']
            );
          },
        ]
      );
    }
    else
    {
      if(is_object($handlerResult))
      {
        $handlerResult = [
          'body'   => $this->_encode($handlerResult),
          'status' => 200,
        ];
      }
      $handler = new MockHandler(
        [
          new Response(
            $handlerResult['status'],
            $headers ?: [],
            $handlerResult['body']
          ),
        ]
      );
    }

    $config = ['handler' => $handler];
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
    $this->assertInstanceOf(MockResponse::class, $apiResult);
    $this->assertEquals($response->toArray(), $apiResult->toArray());
  }

  public function testPost()
  {
    $endpoint = $this->_getEndpoint(
      function ($request)
      {
        /** @var RequestInterface $request */
        $body = $request->getBody()->getContents();
        $params = parse_query($body);
        if(!empty($params))
        {
          return [
            'body'   => $this->_encode(MockResponse::make($params)),
            'status' => 200,
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
    $this->assertInstanceOf(MockResponse::class, $apiResult);
    $this->assertEquals($payload->toArray(), $apiResult->toArray());
  }

  public function testGlobalHeaders()
  {
    $endpoint = $this->_getEndpoint(
      function ($request)
      {
        /** @var RequestInterface $request */
        return [
          'body'   => (new JsonFormat())->encode(
            MockHeaderResponse::make($request->getHeaders())
          ),
          'status' => 200,
        ];
      },
      ['header1' => 'val', 'head2' => 'val2']
    );
    $apiResult = $endpoint->getRequest()->get();
    /**
     * @var MockHeaderResponse $apiResult
     */
    $this->assertInstanceOf(
      MockHeaderResponse::class,
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

  public function testException()
  {
    $toThrow = new MockException('Oops', 1050);
    $toThrow->errorValue = 'test value';
    $endpoint = $this->_getEndpoint(
      [
        'body'   => $toThrow->getFormatted(new JsonFormat()),
        'status' => 200,
      ]
    );
    $payload = new MockPayload();
    $payload->hydrate(['key1' => 'value']);
    $key2 = new \stdClass();
    $key2->key2 = 'vals';
    $payload->hydrate($key2);
    $e = null;
    try
    {
      $endpoint->getRequest($payload, '/', HttpVerb::POST)->get();
    }
    catch(\Exception $e)
    {
    }
    $this->assertEquals($toThrow, $e);
  }
}
