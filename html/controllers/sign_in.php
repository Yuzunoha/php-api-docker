<?php
// ログイン機能
function signIn()
{
    // jsonを取得
    $json = file_get_contents('php://input');
    $params = json_decode($json, true)['sign_in_user_params'];
    $email = $params['email'];
    $password = $params['password'];
    $password = hash('sha256', $password);  // ハッシュ化
    $passwordConfirm = $params['password_confirmation'];
    $passwordConfirm = hash('sha256', $passwordConfirm); //ハッシュ化

    // email欄が空だったらエラー吐く
    if ($email === '') {
        $errorMessage = 'そのemailもしくはpasswordが違います';
        sendResponse($errorMessage);
    }

    // emailに入力された値と一致する行をdbから拾ってくる
    $selectUserByEmailFetchAllResult = Db::selectUserByEmailFetchAll($email);

    // 一致するものがなかったらエラー吐く
    if (count($selectUserByEmailFetchAllResult) === 0) {
        $errorMessage = 'そのemailもしくはpasswordが違います';
        sendResponse($errorMessage);
    }

    // 一致するものがあったら値取り出す
    $selectedEmail = $selectUserByEmailFetchAllResult[0]['email'];
    $selectedPassword = $selectUserByEmailFetchAllResult[0]['password'];

    // パスワード一致チェック
    if ($password !== $passwordConfirm || $password !== $selectedPassword) {
        $errorMessage = 'そのemailもしくはpasswordが違います';
        sendResponse($errorMessage);
    }

    // dbからemailが一致するレコードを取得して返却
    $selectUserAgainByEmailFetchAllResult = Db::selectUserByEmailFetchAll($selectedEmail);
    unset($selectUserAgainByEmailFetchAllResult[0]['password']); // 配列からpassword要素を削除
    sendResponse($selectUserAgainByEmailFetchAllResult[0]);
}
