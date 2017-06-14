<?php

//--------------------
// 定数宣言
//--------------------
define('DB_DATABASE','[自分のxampp-phpMyAdminのアクセスしたいデータベース名]');
define('DB_USERNAME','[自分のxampp-phpMyAdminのユーザー名]');
define('DB_PASSWORD','[自分のxampp-phpMyAdminのパスワード]');
define('PDO_DSN','mysql:charset=utf8;dbhost=localhost;dbname='.DB_DATABASE);

//--------------------
// 00. DB接続
//--------------------
try{
  //DB接続
  $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
  //エラーをスロー
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //PDOStatement::fetch メソッドや PDOStatement::fetchAll メソッドで引数が省略された場合や，ステートメントがforeach文に直接かけられた場合のフェッチスタイルを設定します．デフォルトはPDO::FETCH_BOTHです．
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

}catch(PDOException $e){
  echo 'DB Connection failed: '.$e->getMessage();
  exit;
}

//--------------------
// 01. レコード追加
//--------------------
try{

  //変数宣言（データ追加成功カウント用）
  $addCount = 0;
  $addErrorCount = 0;
  $SuccessFlag = false;
  $addState = "";

  //テーブルにレコードを追加 （単発に便利？）
  //$db->exec("insert into sample_table (id,name) values ('1','xxx@hogehoge.com')");

  //テーブルにレコードを追加 （複数回実行に適している）
  $stmt = $db->prepare('insert into sample_table (name,memo) values(?,?)');

  $name = 'apple';
  $stmt -> bindValue(1,$name,PDO::PARAM_STR);
  $memo = 'アップル';
  $stmt -> bindParam(2,$memo,PDO::PARAM_STR);
  //処理実行（成功すればカウント＋）
  $SuccessFlag = $stmt->execute();
  if($SuccessFlag) $addCount++; else $addErrorCount++;

  //idは自動で追加、nameには「apple」が入る、memoを新規追加
  $memo = 'アップル2';
  //処理実行（成功すればカウント＋）
  $SuccessFlag = $stmt->execute();
  if($SuccessFlag) $addCount++; else $addErrorCount++;

  //新しいnameとmemo
  $name = 'windows';
  $stmt -> bindValue(1,$name,PDO::PARAM_STR);
  $memo = 'ウィンドウズ';
  $stmt -> bindParam(2,$memo,PDO::PARAM_STR);
  //処理実行（成功すればカウント＋）
  $SuccessFlag = $stmt->execute();
  if($SuccessFlag) $addCount++; else $addErrorCount++;

  //成功数を見やすいように整形
  if($addCount === 0){
    $addState = "データベーステーブルに値を追加できませんでした （".$addCount."/".($addCount+$addErrorCount)."）";
  }else{
    if($addErrorCount === 0){
      $addState = "データベーステーブルに値を追加しました （".$addCount."/".$addCount."）";
    }else{
      $addState = "データベーステーブルに値を追加しました （".$addCount."/".($addCount+$addErrorCount)."）";
    }
  }

}catch(PDOException $e){
  echo 'Add Data failed: '.$e->getMessage();
  exit;
}

//--------------------
// 02. レコード参照
//--------------------
try{

  //テーブルのレコードを抽出
  $stmt = $db->query('select * from sample_table');
  //fetchAll(PDO::返却される配列の形式)でquery関数で返却された値を全件取得します
  $users = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  //HTML側で参照する用にデータを用意
  $output = array();
  $output = $users;

}catch(PDOException $e){
  echo 'Show Data failed: '.$e->getMessage();
  exit;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>【PHP-DB】#01_DBアクセス・データ追加・データ参照</title>
  </head>
  <body>

    <h1>【PHP-DB】#01_DBアクセス・データ追加・データ参照</h1>
    <hr>
    
    <h2>◆01. データを追加</h2>
    <p><?php echo $addState; ?></p>

    <h2>◆02. データを参照</h2>
    <table cellspacing="0" cellpadding="5" border="1">
    <tr><th>id</th><th>name</th><th>memo</th></tr>
    <?php 
    foreach ($output as $record) {
      printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
        htmlspecialchars($record['id']),
        htmlspecialchars($record['name']),
        htmlspecialchars($record['memo'])
      );
    }
    ?>
    </table>

  </body>
</html>