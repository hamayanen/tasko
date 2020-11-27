<?php
session_start();
require('../db_connect.php');

header('X-FRAME-OPTIONS: SAMEORIGIN');


if ($_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
}


if (!empty($_POST)) {

    if ($_POST['email'] === '') {
        $error['email'] = 'blank';
    }
    if ($_POST['password'] === '') {
        $error['password'] = 'blank';
    }
}

if (!empty($_POST)) {
    $email = $_POST['email'];

       
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=?');
        $login->execute([
            $_POST['email'],
        ]);
        $member = $login->fetch(PDO::FETCH_ASSOC);

       
        
        if ($member && password_verify($_POST['password'], $member['password'])) {
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            if ($_POST['save'] === 'on') {
                setcookie('email', $member['email'], time() + 60*60*24*14);
            }

            header('Location: ../todo/todo.php');
            exit();
        } else {
            $error['login'] ='failed';
        } 


       
    } 
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset=UTF-8>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>ログイン画面</title>
</head>
<body>
    <div>
        <p class="tasko">Tasko</p>
        <section class="section"> 
            <div class="inner_section">
                <div class="signup">
                    <h1>Taskoにログイン</h1>
                    <form action="" name="form" class="form" method="post">
                        <?php if ($error['login'] === 'failed'): ?>
                            <p class="error3">* ログインに失敗しました。正しくご記入ください
                            </p>
                        <?php endif; ?>
                        <input type="email" name="email" class="text" id="email" maxlength="100" value="<?php htmlspecialchars(print($email), ENT_QUOTES); ?>" placeholder="メールアドレスを入力">
                        <?php if ($error['email'] === 'blank') : ?>
                        <p class="error1">* メールアドレスを入力してください</p>
                        <?php endif; ?>
                        <input type="password" name="password"  class="text" id="password" maxlength="100" value="<?php htmlspecialchars(print($_POST['password']), ENT_QUOTES); ?>" placeholder="パスワードを入力">
                        <?php if ($error['password'] === 'blank') : ?>
                        <p class="error2">* パスワードを入力してください</p>
                        <?php endif; ?>
                        <input type="checkbox" id="save" name="save" value="on">
                        <label for="save">ログイン情報を保存する</label>
                        <input type="submit" tabindex="0" id="btn_next" value="ログイン">
                    </form>
                    <hr>
                    <span class="bottom">
                        <a href="../join/join.php">アカウントを作成</a>
                    </span>
                </div>
            </div>
        </section>
    </div>
    <div class="background">
        <div class="leftpic"><img src="../img/task2.jpg" alt="task"></div>
        <div class="rightpic"><img src="../img/task3.jpg" alt="task"></div>
    </div>
</body>
</html>