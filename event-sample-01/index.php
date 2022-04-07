<?php
// PHP Version 8.1

/*
Using the Slack Events API  
https://api.slack.com/apis/connections/events-api

request type : url_verification  
https://api.slack.com/events/url_verification

request type : event_callback, event type : reaction_added  
https://api.slack.com/events/reaction_added

chat.postMessage  
https://api.slack.com/methods/chat.postMessage
*/

// event parameter
define('THIS_NAME', 'event-sample-01');
define('SLACK_VERIFICATION_VERSION', 'v0');
// !!!!CAUTION!!!! THIS IS CRETICAL PARAMETER!
define('SLACK_SIGNING_SECRET', 'xxxxxxxx');
define('SLACK_ALLOWED_REQUEST_TYPES', ['url_verification', 'event_callback']);

// postMessage parameter
// https://api.slack.com > Your apps > OAuth&Permissions > Bot User OAuth Token
// !!!!CAUTION!!!! THIS IS CRETICAL PARAMETER!
define('SLACK_AUTHZ_TOKEN', 'xoxb-xxxxxxxxxxxx-xxxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxx');
define('SLACK_CHANNEL_ID', 'CXXXXXXXX');

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/logging.php');
logInfo(THIS_NAME." start");

// 1. Judge request type.
$rawBody = file_get_contents('php://input');
$requestDto = json_decode($rawBody, true);
$type = $requestDto['type'];
if (!in_array($type, SLACK_ALLOWED_REQUEST_TYPES)) logErrorAndExit("request type:'{$type}' is not allowed.");

// 2. If url_verification, response challenge.
if ($type == 'url_verification') {
    header('Content-Type: text/plain');
    echo $requestDto['challenge'];
    logInfo(THIS_NAME." end : url_verification");
    exit;
}

// 3. Process event_callback request.
// 3-1. Validate hash.
$headers = apache_request_headers();
if (!isset($headers['X-Slack-Request-Timestamp']))          logErrorAndExit("X-Slack-Request-Timestamp is not set.");
if (!isset($headers['X-Slack-Signature']))                  logErrorAndExit("X-Slack-Signature         is not set.");
$signature_base_string = SLACK_VERIFICATION_VERSION.':'.$headers['X-Slack-Request-Timestamp'].':'.$rawBody;
$expected = SLACK_VERIFICATION_VERSION.'='.hash_hmac('sha256', $signature_base_string, SLACK_SIGNING_SECRET);
if (!hash_equals($headers['X-Slack-Signature'], $expected)) logErrorAndExit("expected:{$expected}, actual:{$headers['X-Slack-Signature']}");

// 3-2. Validate parameters.
if (!isset($requestDto['event']))                           logErrorAndExit("event is none.");
if (!isset($requestDto['event']['type']))                   logErrorAndExit("event.type is none.");

// 3-3. Do.
if ($requestDto['event']['type'] == 'reaction_added') {
    $url = 'https://slack.com/api/chat.postMessage';
    $data = array(
        'token' => SLACK_AUTHZ_TOKEN,
        'channel' => SLACK_CHANNEL_ID,
        'text' => 'oh, reaction added!'
    );
    $context = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded')),
            'content' => http_build_query($data)
        )
    );
    $responseHtml = file_get_contents($url, false, stream_context_create($context));
    logInfo($responseHtml);
}

logInfo(THIS_NAME." end : event_callback");