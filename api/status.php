<?php
// SQLite3データベースへの接続
$db = new SQLite3('../bulletin_board.db');

// 投稿内容を取得
$result = $db->query('SELECT * FROM posts ORDER BY timestamp DESC');

// 投稿データを格納する配列
$posts = array();

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // 投稿データを連想配列に格納
    $post = array(
        'timestamp' => $row['timestamp'],
        'name' => $row['name'],
        'message' => $row['message']
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
