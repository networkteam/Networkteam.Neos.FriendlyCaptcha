<?php
namespace Networkteam\Neos\FriendlyCaptcha\Factory;

/***************************************************************
 *  (c) 2019 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use GuzzleHttp\Client;

class HttpClientFactory
{

    public function create(): Client
    {
        return new Client();
    }
}