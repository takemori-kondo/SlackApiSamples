<?php
// PHP Version 8.1

/*
Guided tutorials  
https://api.slack.com/tutorials

Using the Slack Events API  
https://api.slack.com/apis/connections/events-api

chat.postMessage  
https://api.slack.com/methods/chat.postMessage

url_verification  
https://api.slack.com/events/url_verification

reaction_added  
https://api.slack.com/events/reaction_added

Workflow Builder Steps from Apps  
https://api.slack.com/tutorials/workflow-builder-steps

README.md – steps-from-apps  
https://glitch.com/edit/#!/steps-from-apps?path=README.md%3A1%3A0

bolt-js source  
https://github.com/slackapi/bolt-js/blob/main/src/App.ts

HTTPReceiver.ts handleIncomingEvent  
https://github.com/slackapi/bolt-js/blob/main/src/receivers/HTTPReceiver.ts
*/

define('IS_DEBUG_MODE', true);
define('THIS_NAME', 'event-sample-03');
define('SLACK_VERIFICATION_VERSION', 'v0');
// !!!!CAUTION!!!! THIS IS CRETICAL PARAMETER!
define('SLACK_SIGNING_SECRET', 'xxxxxxxx');
define('SLACK_AUTHZ_TOKEN', 'xoxb-xxxxxxxxxxxx-xxxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxx');
define('SLACK_CHANNEL_ID', 'CXXXXXXXX');

require_once(dirname(__FILE__).'/logging.php');
require_once(dirname(__FILE__).'/slack-common-functions.php');
require_once(dirname(__FILE__).'/slack-sample-reaction-added.php');
require_once(dirname(__FILE__).'/slack-sample-workflow.php');
require_once(dirname(__FILE__).'/slack-sample-shortcut-modal.php');

logInfo(THIS_NAME." start");
if (IS_DEBUG_MODE) logInfo("URL is \n".$_SERVER['REQUEST_URI']);

// 1. Get request headers & body
$headers = apache_request_headers();
$rawBody = file_get_contents('php://input'); // https://www.php.net/manual/ja/wrappers.php.php

// 2. Pre-process
$jsonBody = parseSlackHttpRequestBody($headers, $rawBody);
$requestDto = json_decode($jsonBody, true);
if (IS_DEBUG_MODE) {
    logInfo("Request Headers is \n".print_r($headers, true));
    logInfo("Request Body is \n".print_r($rawBody, true));
    logInfo("Request Json body is \n".print_r($jsonBody, true));
}
ifUrlVerificationRequest_ResponseChallenge($requestDto);
verifySlackSignature($headers, $rawBody, SLACK_VERIFICATION_VERSION, SLACK_SIGNING_SECRET);
$incomingEventType = getIncomingEventType($requestDto);
$incomingPayload = getIncomingPayload($requestDto, $incomingEventType);
if (IS_DEBUG_MODE) logInfo("Incoming event-type is ".$incomingEventType);
if (IS_DEBUG_MODE) logInfo("Incoming payload.type is ".$incomingPayload['type']);

// 3. Do
if ($incomingEventType === 'event') {
    if ($incomingPayload['type'] === 'reaction_added') {
        $response = slack_sample_reaction_added(SLACK_AUTHZ_TOKEN, SLACK_CHANNEL_ID);
        logInfo($response);
    }
    // workflow_step_execute は eventの仲間
    if ($incomingPayload['type'] === 'workflow_step_execute') {
        $response = slack_sample_workflow_execute(SLACK_AUTHZ_TOKEN, SLACK_CHANNEL_ID, $requestDto, $incomingPayload);
        logInfo($response);
    }
}

if ($incomingEventType === 'action' || $incomingEventType === 'viewaction') {
    if ($incomingPayload['type'] === 'workflow_step_edit') {
        $response = slack_sample_workflow_workflow_step_edit(SLACK_AUTHZ_TOKEN, $requestDto);
        logInfo($response);
    }
    // $requestDto['type'] は 'view_submission'
    if ($incomingPayload['type'] === 'workflow_step') {
        $response = slack_sample_workflow_save(SLACK_AUTHZ_TOKEN, $requestDto, $incomingPayload);
        logInfo($response);
    }
    if ($incomingPayload['type'] === 'button') {
        logInfo('button clicked!');
    }
    if ($incomingPayload['type'] === 'modal') {
        $response = slack_sample_shortcut_modal_save(SLACK_AUTHZ_TOKEN, $requestDto, $incomingPayload);
        logInfo($response);
    }
}

if ($incomingEventType === 'shortcut') {
    $response = slack_sample_shortcut_modal(SLACK_AUTHZ_TOKEN, $requestDto, $incomingPayload);
    logInfo($response);
}

logInfo(THIS_NAME." end");