<?php
session_start();
require('../db_connect.php');

header('X-FRAME-OPTIONS: SAMEORIGIN');


if(!empty($_SESSION['join'])) {
    $hash = password_hash($_SESSION['join']['password'], PASSWORD_DEFAULT);
    $set = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, created=NOW()');
    $set->execute([
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        $hash,
    ]);

    unset($_SESSION['join']);
}

//ログイン情報保持の期間内か確認
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] - time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute([$_SESSION['id']]);
    $member = $members->fetch();
} else {
    header('Location: ../login/login.php');
}



// 検索機能
if (isset($_POST['search'])) {

    $search = $_POST['search'];

    if (empty($search)) {
        header("Location: todo.php?mess=error2");
    } else {
        $stmts = $db->prepare("SELECT * FROM todo WHERE title=?");
        $stmts->execute([$search]);
        $stmt = $stmts->fetch();
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset=UTF-8>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="todo.css">
    <link href="../jquery-ui/jquery-ui.css" rel="stylesheet">
    <title>Taskoへようこそ</title>
</head>
<body>
        <div id="header">
            <a href="" id="tasko">Tasko</a>
            <p id="date"></p>
            <div id="logout"><a href="logout.php" >ログアウト</a></div>
        </div>
        <div class="section">
            <div class="section_search">
                <form action="" method="post">
                    <?php if(isset($_GET['mess']) && $_GET['mess'] === 'error2') { ?>
                    <input type="search" name="search" id="search_task" style="border-color: #ff0000" placeholder="検索したいタスクを入力してください" >
                    <button type="submit" id="search_btn">検索</button>

                    <?php } else { ?>
                    <input type="search" name="search" id="search_task" placeholder="タスクを検索">
                    <button type="submit" id="search_btn">検索</button>
                    <?php } ?>
                </form>
                <?php if (isset($_POST['search'])) { ?>
                    <?php if (empty($stmt['title'])) { ?>
                        <p class="error_notask">※そのタスクはないです</p>
                    <?php } else { ?>
                        <p class="doing_task"><?php echo $stmt['title']?>: そのタスクは進行中です</p>
                <?php }     }?>
            </div>

        <form action="add.php" method="post" autocomplete="off">
            <?php if(isset($_GET['mess']) && $_GET['mess'] === 'error'){ ?>
            <input type="text" name="title" id="title"
            style="border-color: #ff0000" placeholder="タスクを入力してください">
            <button type="submit" id="btn">追加　&nbsp; <span>&#43;</span></button>
                
            <?php } else { ?>
            <input type="text" name="title" id="title" placeholder="タスクを入力">
            <button type="submit" id="btn">追加　&nbsp; <span>&#43;</span></button>
            <?php } ?>
        </form>
        </div>
        <?php
            $todos = $db->query("SELECT * FROM todo ORDER BY id DESC");
        ?>
        <div class="show_todo">
            <?php while ($todo = $todos->fetch()): ?>
            <div class="todo_item">
                <span id="<?php echo $todo['id']; ?>"
                    class="remove_todo">x</span>
                    <?php if ($todo['checked']) { ?>
                        <input  type="checkbox" 
                                data_todo_id = "<?php echo $todo['id']; ?>"
                                class="check_box" checked>
                        <h2 class="checked"><?php echo $todo['title']?></h2>
                    <?php } else {?>
                        <input  type="checkbox"
                                data_todo_id = "<?php echo $todo['id']; ?>"
                                class="check_box">
                        <h2><?php echo $todo['title']?></h2>
                    <?php } ?>
                    </br>
                    <small>created: <?php echo $todo['date_time']?></small>
             </div>  
            <?php endwhile; ?>
        </div>

    
    <script src="../jquery-ui/jquery.js"></script>
    <script src="../jquery-ui/jquery-ui.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $(function(){
                let now = new Date();
                let y = now.getFullYear();
                let m = now.getMonth() + 1;
                let d = now.getDate();
                let mm = ('0' + m).slice(-2);
                let dd = ('0' + d).slice(-2);
                $('#date').text(y + '/' + mm + '/' + dd);
            })


            $("#date").css({
                color: '#fff',
                position: "absolute",
                top: "15px",
                left: "10px",
                fontSize: "16px",
            });


            $('.remove_todo').click(function() {
                const id = $(this).attr('id');
                
                $.post("remove.php", 
                    {
                        id: id
                    },
                    
                    (data) => {
                            if(data) {
                                $(this).parent().hide(600);
                            }
                        }
                    
                );
            });

            $(".check_box").click(function(e) {
                const id = $(this).attr('data_todo_id');
                
                $.post('check.php',
                    {
                        id: id
                    },
                    (data) => {
                        if (data != 'eroor') {
                            const h2 = $(this).next();
                            if(data === '1') {
                                h2.removeClass('checked');
                            } else {
                                h2.addClass('checked');
                            }
                        }
                    }
                )
            });

            $(function() {
                $(".show_todo").sortable();
                $(".show_todo").disableSelection();
            });


        });
    </script>
</body>
</html>