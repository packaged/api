<?php
namespace Packaged\Api\Interfaces;

use GuzzleHttp\Promise\PromiseInterface;

interface ApiRequestInterface extends ApiAwareInterface
{
  const HTTP_GET = 'get';
  const HTTP_POST = 'post';
  const HTTP_DELETE = 'delete';
  const HTTP_HEAD = 'head';
  const HTTP_OPTIONS = 'options';
  const HTTP_PATCH = 'patch';
  const HTTP_PUT = 'put';

  public function get();

  public function getVerb();

  public function getPath();

  public function getPostData();

  public function getQueryString();

  public function setPromise(PromiseInterface $promise);
}
