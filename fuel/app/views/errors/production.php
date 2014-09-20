<?php

\Request::reset_request(true);

$response = Response::forge(View::forge('errors/index.twig'));

$response->body((string)$response);
$response->send(true);


