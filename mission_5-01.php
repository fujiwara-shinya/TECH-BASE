<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    <?php
    // DB接続設定
    $dsn = '（データベース名）';
    $user = '（ユーザー名）';
    $password = '（パスワード）';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS tbm5"//tbm5がテーブル名
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "PW TEXT"
    .");";
    $stmt = $pdo->query($sql);
    ?>

    <!--編集コマンド-->
    <form action=""  method="post" >
        <input type= "number" name="edit" placeholder="編集番号">
        <input type= "text" name="edit_PW" value="パスワード">
        <input type= "submit" name="submit" value="編集"><br>
    </form>
    <?php
    if(isset($_POST["edit"])){
        $id = $_POST["edit"]; //変更する投稿番号
        $sql = 'SELECT * FROM tbm5 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        if ($results[0]['PW'] == $_POST["edit_PW"]){
            $edit_name = $results[0]['name'];
            $edit_comment = $results[0]['comment'];
            $edit_PW = $results[0]['PW'];
            $edit_mode = true;
            echo "編集モード";
        }else{
            $edit_mpde = false;
            echo "パスワードが違います";
        }
    }
    ?>

    <!--投稿フォーム-->
    <form action=""  method="post" >
        <input type= "hidden" name="edit_num" value=<?php if(isset($id) && isset($edit_mode) && $edit_mode == true){echo $id;}?>>
        <input type= "text" name="name" value=<?php if(isset($edit_name)){echo $edit_name;}else{echo "名前";} ?>>
        <input type= "text" name="comment" value=<?php if(isset($edit_comment)){echo $edit_comment;}else{echo "コメント";}  ?>>
        <input type= "text" name="PW" value=<?php if(isset($edit_PW)){echo $edit_PW;}else{echo "パスワード";}  ?>>
        <input type="submit" name="submit">
        </form>
    
    <!--削除フォーム-->
    <form action=""  method="post" >
        <input type= "number" name="del" placeholder="削除番号">
        <input type= "text" name="del_PW" value="パスワード">
        <input type="submit" name="submit" value="削除">
    </form>

    <!--バックエンド処理-->
    <?php
    //編集内容受信
    if(isset($_POST["edit_num"]) && $_POST["edit_num"] > 0){
        if( isset($_POST["comment"]) || isset($_POST["name"]) ){
            $comment = $_POST["comment"];
            $name = $_POST["name"];
            $date = date("YmdHis");
            $PW = $_POST["PW"];
            $id = $_POST["edit_num"]; //編集する投稿番号
            $sql = 'UPDATE tbm5 SET name=:name,comment=:comment,date=:date,PW=:PW WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_INT);
            $stmt->bindParam(':PW', $PW, PDO::PARAM_STR);
            $stmt->execute();
        }
    }else{
        //新規投稿受信
        if( isset($_POST["comment"]) || isset($_POST["name"]) ){
            //送信フォーム内容の書き込み
            $sql = $pdo -> prepare("INSERT INTO tbm5 (name, comment, date, PW) VALUES (:name, :comment, :date, :PW)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_INT);
            $sql -> bindParam(':PW', $PW, PDO::PARAM_STR);
            $comment = $_POST["comment"];
            $name = $_POST["name"];
            $date = date("YmdHis");
            $PW = $_POST["PW"];
            $sql -> execute();
        }elseif(isset($_POST["del"])){
            //削除フォームの処理
            $id = $_POST["del"]; // 削除番号取得
            $sql = 'SELECT * FROM tbm5 WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
            if($_POST["del_PW"]!=$results[0]['PW'] || isset($_POST["del_PW"])==false){
                echo "パスワードが違います<br>";
            }else{
                $sql = 'delete from tbm5 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
    $sql = 'SELECT * FROM tbm5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        //echo $row['PW'].',';
        echo $row['date'].'<br>';
    echo "<hr>";
    }
    ?>
</body>
</html>