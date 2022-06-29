<?php


$is_succeeded = false;

if(isset($_GET["code"])){
    
    $code = $_GET["code"];

    $client_id = "xxxxxxxxxxxxx.xxxxxxxxxxxxx";
    $client_secret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";




    $url = "https://slack.com/api/oauth.v2.access";

    //$url = "https://www.google.co.jp/";

    $post_data = array("code" => $code, "client_id" => $client_id, "client_secret" => $client_secret);

    $ch = curl_init(); // はじめ

    //オプション
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    //データの配列を設定する
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

    $html =  curl_exec($ch);
    
    
    error_log($html);

    curl_close($ch); //終了


    $decoded = json_decode($html, true);
    if($decoded["ok"] == "true"){
        $is_succeeded = true;
    }



}
else{
    error_log("no code");
}
?>

<?php if($is_succeeded) : ?>
インストールを行いました。
(トークンはログに保管しました)
<?php else : ?>
トークン取得 エラーが発生しました
<?php endif; ?>