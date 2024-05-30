<?php
// PHP Version 8.1

function slack_sample_workflow_workflow_step_edit($SLACK_AUTHZ_TOKEN, $requestDto) {
    $blocks = <<<'HEREDOC'
{
    "type": "workflow_step",
    "blocks": [
      {
        type: "section",
        block_id: "description",
        text: { type:"plain_text", text:"各項目に対応する「Insert a variable」変数を設定してください" }
      },
      {
        "type": "input",
        "element": {
          "type": "plain_text_input",
          "action_id": "submission_timestamp"
        },
        "label": {
          "type": "plain_text",
          "text": "Submission Timestamp",
          "emoji": false
        }
      },
      {
        "type": "input",
        "element": {
          "type": "plain_text_input",
          "action_id": "submitter_slack_email"
        },
        "label": {
          "type": "plain_text",
          "text": "Submitter Slack Email",
          "emoji": false
        }
      },
      {
        "type": "input",
        "element": {
          "type": "plain_text_input",
          "action_id": "slack_full_name"
        },
        "label": {
          "type": "plain_text",
          "text": "Slack Full Name",
          "emoji": false
        }
      },
      {
        "type": "input",
        "element": {
          "type": "plain_text_input",
          "action_id": "answer"
        },
        "label": {
          "type": "plain_text",
          "text": "Answer",
          "emoji": false
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

function slack_sample_workflow_save($SLACK_AUTHZ_TOKEN, $requestDto, $payload) {
    // $payloadはviewプロパティの中身
    $keyList = array_keys( $payload['state']['values']);
    $var1 = $payload['state']['values'][$keyList[0]]['submission_timestamp']['value'];
    $var2 = $payload['state']['values'][$keyList[1]]['submitter_slack_email']['value'];
    $var3 = $payload['state']['values'][$keyList[2]]['slack_full_name']['value'];
    $var4 = $payload['state']['values'][$keyList[3]]['answer']['value'];
    $inputs = <<<"HEREDOC"
{
    submission_timestamp:  { "skip_variable_replacement": false, "value": "{$var1}" },
    submitter_slack_email: { "skip_variable_replacement": false, "value": "{$var2}" },
    slack_full_name:       { "skip_variable_replacement": false, "value": "{$var3}" },
    answer:                { "skip_variable_replacement": false, "value": "{$var4}" }
}
HEREDOC;
    $outputs = "[]";
    $url = 'https://slack.com/api/workflows.updateStep';
    $data = array(
        'token' => $SLACK_AUTHZ_TOKEN,
        'workflow_step_edit_id' => $requestDto['workflow_step']['workflow_step_edit_id'],
        'inputs' => $inputs,
        'outputs' => $outputs,
        'step_name' => 'slack_sample_workflow!!'
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

function slack_sample_workflow_execute($SLACK_AUTHZ_TOKEN, $SLACK_CHANNEL_ID, $requestDto, $payload) {
}