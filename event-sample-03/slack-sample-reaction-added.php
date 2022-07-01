<?php
// PHP Version 8.1

function slack_sample_reaction_added($SLACK_AUTHZ_TOKEN, $SLACK_CHANNEL_ID) {
    $url = 'https://slack.com/api/chat.postMessage';
    $data = array(
        'token' => $SLACK_AUTHZ_TOKEN,
        'channel' => $SLACK_CHANNEL_ID,
        'text' => 'oh, reaction added!'
    );
    $context = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded')),
            'content' => http_build_query($data)
        )
    );
    $response = file_get_contents($url, false, stream_context_create($context));
    return $response;
}