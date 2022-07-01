<?php
// PHP Version 8.1

function slack_sample_shortcut_modal($SLACK_AUTHZ_TOKEN, $requestDto, $payload) {
    $blocks = <<<'HEREDOC'
{
    "type": "modal",
    "callback_id": "modal-sample",
    "title": {
        "type": "plain_text",
        "text": "Just a modal"
    },
    "submit": {
        "type": "plain_text",
        "text": "Submit"
    },
    "close": {
        "type": "plain_text",
        "text": "Cancel"
    },
    "blocks": [
      {
        "type": "section",
        "block_id": "section-identifier",
        "text": {
          "type": "mrkdwn",
          "text": "*Welcome* to ~my~ Block Kit _modal_!"
        },
        "accessory": {
          "type": "button",
          "text": {
            "type": "plain_text",
            "text": "Just a button"
          },
          "action_id": "modal-sample-button"
        }
      },
      {
        "type": "input",
        "block_id": "input01",
        "element": {
          "type": "plain_text_input",
          "placeholder": {
            "type": "plain_text",
            "text": "What do you want to ask of the world?"
          },
          "action_id": "modal-sample-input01",
        },
        "label": {
          "type": "plain_text",
          "text": "Title"
        }
      }
    ]
}
HEREDOC;
    $url = 'https://slack.com/api/views.open';
    $data = array(
        'token' => $SLACK_AUTHZ_TOKEN,
        'trigger_id' => $requestDto['trigger_id'],
        'view' => $blocks
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

function slack_sample_shortcut_modal_save($SLACK_AUTHZ_TOKEN, $requestDto, $payload) {
    $answer = $requestDto['view']['state']['values']['input01']['modal-sample-input01']['value'];
    return '"modal-sample-input01"\'s value='.$answer;
}