<?php

use Devqaly\DevqalyClient\Events\DatabaseTransactionEvent;

it('should throw an error when passing empty sql', function () {
    $backendUrl = 'https://devqaly.test/api';
    $sourceIdentifier = 'microservice-x';
    $sessionId = 'c7622e14-21f8-40c2-b151-3a311816b423';
    $sessionSecret = 'c7622e14-21f8-40c2-b151-3a311816b423';
    $securityToken = 'nJOFUgmcKDhzpMMbL6VqEzWbK7XOby8ZMqOWqYooTE1Xtd4Y3RQBidpeq42i';

    $client = new DatabaseTransactionEvent($backendUrl, $sourceIdentifier, $securityToken);

    $client->create($sessionId, $sessionSecret, []);
})->throws(\Error::class, '`sql` must be set to create a database transaction event in $data');

it('should execute curl request when calling `create` method and close', function () {
    $backendUrl = 'https://devqaly.test/api';
    $sourceIdentifier = 'microservice-x';
    $sessionId = 'c7622e14-21f8-40c2-b151-3a311816b423';
    $sessionSecret = 'c7622e14-21f8-40c2-b151-3a311816b423';
    $securityToken = 'nJOFUgmcKDhzpMMbL6VqEzWbK7XOby8ZMqOWqYooTE1Xtd4Y3RQBidpeq42i';

    $client = Mockery::mock(DatabaseTransactionEvent::class, [$backendUrl, $sourceIdentifier, $securityToken])->makePartial();

    $endpoint = 'https://something.com';

    $baseData = ['sql' => 'select * from users'];

    // $payload = $client->generatePayload($baseData, DatabaseTransactionEvent::EVENT_TYPE);

    $client->shouldReceive('validateSessionId')->with($sessionId)->once();
    $client->shouldReceive('validateSessionSecret')->with($sessionSecret)->once();
    $client->shouldReceive('getCreateEventEndpoint')->with($sessionId)->once()->andReturn($endpoint);

    $client->shouldReceive('setOption')->times(1)->with(CURLOPT_URL, $endpoint);
    $client->shouldReceive('setOption')->times(1)->with(CURLOPT_RETURNTRANSFER, true);
    $client->shouldReceive('setOption')->times(1)->with(CURLOPT_POST, true);
//    $client->shouldReceive('setOption')->times(1)->withSomeOfArgs(CURLOPT_POSTFIELDS, json_encode($payload));
//    $client->shouldReceive('setOption')->times(1)->withSomeOfArgs(CURLOPT_HTTPHEADER, [
//        'x-devqaly-session-secret-token: ' . $sessionSecret,
//        'Accept: application/json',
//        'Content-Type: application/json',
////        'Content-Length: ' . strlen($payload)
//    ]);

    $client->shouldReceive('execute')->withNoArgs()->once();
    $client->shouldReceive('close')->withNoArgs()->once();

    $client->create($sessionId, $sessionSecret, $baseData);
});
