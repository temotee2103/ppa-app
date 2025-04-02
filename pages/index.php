<?php
// 添加头部和标题
$page_title = 'Home';
$current_page = 'home';
$additional_css = array('modern.css');
include_once("../includes/header.php");

// 主页内容
?>

<!-- 主页内容 -->
<div class="container py-5">
    <h1>Welcome to Plus Protect Auto</h1>
    <!-- 其他主页内容 -->
</div>

<!-- 成功案例展示 -->
<?php include_once("../includes/success-stories.php"); ?>

<?php
include_once("../includes/footer.php");
?> 