<?php
// PHP Version 8.1

require_once(dirname(__FILE__).'/logging.php');

function parseSlackHttpRequestBody($headers, $rawBody) {
    if($headers['Content-Type'] === 'application/x-www-form-urlencoded') {
        parse_str($rawBody, $parsed); // $rawBody is query string format
        if (isset($parsed['payload'])) {
            return urldecode($parsed['payload']);
        }
        else
        {
            $encoded = json_encode($parsed);
            return urldecode($encoded);
        }
    }
    return urldecode($rawBody);
}

function ifUrlVerificationRequest_ResponseChallenge($decodedBody) {
    if ($decodedBody['type'] === 'url_verification') {
        logInfo("request is url_verification");
        header('Content-Type: text/plain');
        echo $decodedBody['challenge'];
        exit;
    }
}

function verifySlackSignature($headers, $rawBody, $slackVerificationVersion, $slackSigningSecret) {
    if (!isset($headers['X-Slack-Request-Timestamp']))          logErrorAndExit("X-Slack-Request-Timestamp is not set.");
    if (!isset($headers['X-Slack-Signature']))                  logErrorAndExit("X-Slack-Signature         is not set.");
    $signatureBaseString = $slackVerificationVersion.':'.$headers['X-Slack-Request-Timestamp'].':'.$rawBody;
    $expected = $slackVerificationVersion.'='.hash_hmac('sha256', $signatureBaseString, $slackSigningSecret);
    if (!hash_equals($headers['X-Slack-Signature'], $expected)) logErrorAndExit("expected:{$expected}, actual:{$headers['X-Slack-Signature']}");
}

/*
url_verificationは判断外
*/
function getIncomingEventType($decodedBody) {
    if (isset($decodedBody['event']))   return 'event';
    if (isset($decodedBody['command'])) return 'command';
    if (isset($decodedBody['name']) ||
        $decodedBody['type'] === 'block_suggestion') {
        return 'options';
    }
    if (isset($decodedBody['actions']) ||
        $decodedBody['type'] === 'dialog_submission' ||
        $decodedBody['type'] === 'workflow_step_edit') {
        return 'action';
    }
    if ($decodedBody['type'] === 'shortcut' ||
        $decodedBody['type'] === 'message_action') {
        return 'shortcut';
    }
    if ($decodedBody['type'] === 'view_submission' ||
        $decodedBody['type'] === 'view_closed') {
        return 'viewaction';
    }
}

function getIncomingPayload($decodedBody, $incomingEventType) {
    if ($incomingEventType === 'event') {
        return $decodedBody['event'];
    }
    if ($incomingEventType === 'viewaction') {
        return $decodedBody['view'];
    }
    if ($incomingEventType === 'action' && array_key_exists('actions', $decodedBody)) {
        return $decodedBody['actions'][0];
    }
    return $decodedBody;
}
