<?php
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        $payload_json = file_get_contents('php://input');
        $payload = json_decode($payload_json);
        if ( $payload->action == 'delete' ) {
            $read = file(__DIR__ . '/data.txt');
            foreach ( $read as $line => $user ) {
                if ( $line == $payload->user_id ) {
                    unset($read[$line]);
                }
            }
            $UpdatedContents = implode("", $read);
            file_put_contents(__DIR__ . '/data.txt', trim($UpdatedContents));
            echo json_encode([
                'deleted' => true,
                'user_id' => $payload->user_id,
                'updated' => $read
            ]);
        }
        if ( $_POST['action'] == 'add' ) {
            if ( empty(trim($_POST['user_fname'])) ) {
                echo json_encode([
                    'empty_field' => 'user_fname'
                ]);
                die();
            }
            if ( empty(trim($_POST['user_lname'])) ) {
                echo json_encode([
                    'empty_field' => 'user_lname'
                ]);
                die();
            }
            $add_user_str = trim($_POST['user_fname']) . ',' . trim($_POST['user_lname']) . ',' . $_POST['position_name'] . ',' . $_POST['position'];
            file_put_contents(__DIR__ . '/data.txt', PHP_EOL . $add_user_str, FILE_APPEND );
            echo json_encode([
                'added' => true,
                'first_name' => trim($_POST['user_fname']),
                'last_name' => trim($_POST['user_lname']),
                'position_name' => $_POST['position_name'],
                'position_val' => $_POST['position']
            ]);
        }
        if ( $_POST['action'] == 'edit' ) {
            if ( empty(trim($_POST['user_fname'])) ) {
                echo json_encode([
                    'empty_field' => 'user_fname'
                ]);
                die();
            }
            if ( empty(trim($_POST['user_lname'])) ) {
                echo json_encode([
                    'empty_field' => 'user_lname'
                ]);
                die();
            }
            $read = file(__DIR__ . '/data.txt');
            $add_user_str = trim($_POST['user_fname']) . ',' . trim($_POST['user_lname']) . ',' . $_POST['position_name'] . ',' . $_POST['position'];

            foreach ( $read as $line => $user ) {
                if ( $line == $_POST['user_id'] ) {
                    $read[$line] = $add_user_str . PHP_EOL;
                }
            }
            $UpdatedContents = implode("", $read);
            file_put_contents(__DIR__ . '/data.txt', trim($UpdatedContents));
            echo json_encode([
                'edited' => true,
                'first_name' => trim($_POST['user_fname']),
                'last_name' => trim($_POST['user_lname']),
                'position_name' => $_POST['position_name'],
                'position_val' => $_POST['position'],
                'user_id' => $_POST['user_id']
            ]);
        }
        die();
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>First task</title>
    <style>
        table {
            margin-top: 50px;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
        }
        .edit, .delete, .add {
            cursor: pointer;
        }
        input.empty {
            border: 2px solid red;
        }
        .add-user {
            margin-top: 30px;
            padding: 5px;
            border: 1px solid black;
            display: inline-block;
            cursor: pointer;
        }
        .delete.disabled {
            display: none;
        }
    </style>
</head>
<body>
    <form id="edit-users">
        <input type="hidden" name="action" value="add">
        <label>Имя
            <input id="first-name" type="text" name="user_fname">
        </label>
        <label>Фамилия
            <input type="text" name="user_lname">
        </label>
        <label>Должность
            <select id="position-inp" name="position">
                <option value="programmer">программист</option>
                <option value="manager">менеджер</option>
                <option value="tester">тестировщик</option>
            </select>
        </label>
        <button id="submit" class="submit_btn">Добавить</button>
    </form>
    <table id="users">
        <thead>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Должность</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $fp = fopen(__DIR__ . "/data.txt", "r");
            if ($fp) {
                while (($user_str = fgets($fp, 4096)) !== false) {
                    $user_arr = explode(',', $user_str); ?>
                    <tr>
                        <td class="first-name"><?php echo $user_arr[0]; ?></td>
                        <td class="last-name"><?php echo $user_arr[1]; ?></td>
                        <td class="position" data-value="<?php echo trim($user_arr[3]); ?>"><?php echo $user_arr[2]; ?></td>
                        <td class="edit">Редактировать</td>
                        <td class="delete">Удалить</td>
                    </tr>
                <?php }
                fclose($fp);
            }
            ?>
        </tbody>
    </table>
    <label for="first-name" class="add-user">Добавить</label>
</body>
<script src="main.js" async></script>
</html>
