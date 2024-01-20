<?php
// SQLite3データベースへの接続
$db = new SQLite3('bulletin_board.db');

// データベースのテーブル作成（初回のみ）
$db->exec('CREATE TABLE IF NOT EXISTS posts (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, message TEXT, timestamp DATETIME)');

// ファイルに書き込む関数
function writeToFile($name, $message) {
    global $db;

    $timestamp = date('Y-m-d H:i:s');

    // プリペアドステートメントを使用してSQLインジェクションを防ぐ
    $stmt = $db->prepare('INSERT INTO posts (name, message, timestamp) VALUES (:name, :message, :timestamp)');
    $stmt->bindValue(':name', htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), SQLITE3_TEXT);
    $stmt->bindValue(':message', htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), SQLITE3_TEXT);
    $stmt->bindValue(':timestamp', $timestamp, SQLITE3_TEXT);

    // 実行
    $stmt->execute();
}

// 投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['message'])) {
    $name = $_POST['name'];
    $message = $_POST['message'];

    if ($name !== '' && $message !== '') {
        writeToFile($name, $message);
    }
}

// 投稿内容を表示
$result = $db->query('SELECT * FROM posts ORDER BY timestamp DESC');

// 投稿データを格納する配列
$posts = array();

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // 投稿データを連想配列に格納
    $post = array(
        'timestamp' => htmlspecialchars($row['timestamp'], ENT_QUOTES, 'UTF-8'),
        'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
        'message' => htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8')
    );
    // 配列に追加
    $posts[] = $post;
}

// 判定して出力
if (strpos($_SERVER['REQUEST_URI'], '/api/status.php') !== false) {
    // JSON形式で出力
    header('Content-Type: application/json');
    echo json_encode($posts);
} else {
    // HTML形式で出力
    foreach ($posts as $post) {
        echo "{$post['timestamp']} - {$post['name']}: {$post['message']}<br>";
    }
}

// データベース接続のクローズ
$db->close();
?>
