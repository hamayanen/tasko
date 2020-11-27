<?php
session_start();
require('../db_connect.php');

header('X-FRAME-OPTIONS: SAMEORIGIN');

if (!empty($_POST)) {
    if ($_POST['email'] === '') {
        $error['email'] = 'blank';
    }
    if ($_POST['name'] === '') {
        $error['name'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    } 
    if ($_POST['password'] === '') {
        $error['password'] = 'blank';
    }


    // アカウントの重複チェック
    if (empty($error)) {
        $mail_dbcheck = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $mail_dbcheck->execute(array($_POST['email']));
        $mail_record = $mail_dbcheck->fetch();
        if ($mail_record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if(empty($error)) {
        $_SESSION['join'] = $_POST;

        header('Location: ../todo/todo.php');
        exit();
    }

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset=UTF-8>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="join.css">
    <title>アカウントを作成する</title>
</head>
<body>
    <div>
        <p class="tasko">Tasko</p>
        <section class="section"> 
            <div class="inner_section">
                <div class="signup">
                    <h1>アカウントを作成</h1>
                    <form action="" name="form" method="post" class="form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="submit">
                        <input type="email" name="email" id="email" maxlength="100" value="<?php htmlspecialchars(print($_POST['email']), ENT_QUOTES); ?>" placeholder="メールアドレスを入力">
                        <?php if ($error['email'] === 'blank') : ?>
                        <p class="error">* メールアドレスを入力してください</p>
                        <?php endif; ?>
                        <?php if ($error['email'] === 'duplicate') : ?>
                        <p class="error">* そのメールアドレスは既に登録されています</p>
                        <?php endif; ?>
                        <input type="text" name="name" id="name" maxlength="100" value="<?php htmlspecialchars(print($_POST['name']), ENT_QUOTES); ?>" placeholder="フルネームを入力">
                        <?php if ($error['name'] === 'blank') : ?>
                        <p class="error">* フルネームを入力してください</p>
                        <?php endif; ?>
                        <input type="password" name="password" id="password" maxlength="100" value="<?php htmlspecialchars(print($_POST['password']), ENT_QUOTES); ?>" placeholder="パスワードを入力">
                        <?php if ($error['password'] === 'length') : ?>
                        <p class="error">* パスワードは4文字以上で入力してください</p>
                        <?php endif; ?>
                        <?php if ($error['password'] === 'blank') : ?>
                        <p class="error">* パスワードを入力してください</p>
                        <?php endif; ?>
                        <p class="terms">※アカウントを作成することにより、利用規約およびプライバシーポリシーを読み、これに同意するものとします。</p>
                        <input type="submit" tabindex="0" id="btn_next" value="続行">
                    </form>
                    <hr>
                    <span class="bottom">
                        <a href="../login/login.php">アカウントをお持ちの場合は、ログイン</a>
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