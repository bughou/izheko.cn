        <div id="topbar">
            <?php require APP_ROOT . "/view/component/footprint.view.php"; ?>
            <span id="hello">亲，欢迎您！</span>
            <?php  $no_text = true; require APP_ROOT . "/view/component/sns_share.view.php"; ?>
            <?php  require APP_ROOT . "/view/component/bookmark.view.php"; ?>
        </div>
        <div id="header">
            <a href="/" class="logo"></a>
            <form id="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" value="<?= isset($word) ? $word : null ?>" /></div>
                <button type="submit">搜　索</button>
            </form>
        </div>
<?php
    require APP_ROOT . "/view/component/navbar.view.php";
?>
