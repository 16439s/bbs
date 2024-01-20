<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Bulletin Board</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            // ページ読み込み時に投稿内容を表示
            loadPosts();

            // 投稿ボタンがクリックされたときの処理
            $("#postButton").click(function () {
                var name = $("#name").val();
                var message = $("#message").val();

                // 入力チェック
                if (name === '' || message === '') {
                    alert('名前とメッセージを入力してください。');
                    return;
                }

                // 投稿処理
                $.ajax({
                    type: "POST",
                    url: "post.php",
                    data: { name: name, message: message },
                    success: function () {
                        // 投稿成功時にフォームをクリアして再読み込み
                        $("#name").val('');
                        $("#message").val('');
                        loadPosts();
                    }
                });
            });

            // 定期的に投稿内容を更新
            setInterval(function () {
                loadPosts();
            }, 5000); // 5秒ごとに更新（適宜調整）
        });

      // 投稿内容を読み込む関数
      function loadPosts() {
          $.ajax({
              type: "GET",
              url: "post.php",
              success: function (data) {
                  // 現在の投稿を非表示にして新しい投稿をフェードイン
                  $("#posts").fadeOut(300, function () {
                      $(this).html(data).fadeIn(300);
                  });
              }
          });
      }
    </script>
</head>
<body>
    <div class="container">
        <div class="board">
            <div id="posts"></div>
            <div class="post-form">
                <input type="text" id="name" placeholder="名前">
                <textarea id="message" placeholder="投稿内容"></textarea>
                <button id="postButton">投稿する</button>
            </div>
        </div>
    </div>
</body>
</html>
