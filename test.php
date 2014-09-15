<?php
namespace Test;

use Packaged\ApiExample\CorpApi;
use Packaged\ApiExample\Endpoints\UserEndpoint;
use Packaged\ApiExample\Requests\UserPayload;
use Packaged\ApiExample\UserResult;

$api = new CorpApi();

$users   = UserEndpoint::bound($api);
$newUser = $users->create(UserPayload::create('Brooke', '29'));
$user    = UserResult::create($newUser);

echo "Name: " . $user->getName() . "\n";
echo "Username: " . $user->getUsername() . "\n";
echo "Age: " . $user->getAge() . "\n";

