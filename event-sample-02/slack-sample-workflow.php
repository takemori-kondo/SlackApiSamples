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
        text: { type:"plain_text", text:"アンケート結果をユーザのカスタムフィールドに書き込みます。" }
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
    $inputs = '{ taskName: { value: "タスク名" }, taskDescription: { value: "タスク説明" } }';
    $outputs =  <<<'HEREDOC'
[
    { type: "text", name: "taskName", label: "Task name" },
    { type: "text", name: "taskDescription", label: "Task description" }
]
HEREDOC;
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