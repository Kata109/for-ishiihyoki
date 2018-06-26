<?php
	$servername = "localhost";
	$username = "root";
	$password = "66329525";
	$dbname = "ihcdata";
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");

	$customer = $_POST["customer"];
	$item = $_POST["item"];
	//核对客户是否在在库客户
	$stmt = $conn->prepare("SELECT * FROM ihc_customer WHERE customer='$customer'"); 
	$stmt->execute();
	$row1 = $stmt->rowCount();
	//核对产品是否在在库产品
	$stmt = $conn->prepare("SELECT * FROM ihc_item WHERE nameC='$item'"); 
	$stmt->execute();
	$row2 = $stmt->rowCount();

	if ($row1!=0 && $row2!=0 && $_POST["unit"]!="" && is_numeric($_POST["price"]) && $_POST["price"]>0) {
		//判断是否添加过
		$stmt = $conn->prepare("SELECT * FROM ihc_customerprice WHERE customer='$customer' AND item='$item'"); 
		$stmt->execute();
		$rowC = $stmt->rowCount();
		if ($rowC==0) {
			$stmt = $conn->prepare("INSERT INTO ihc_customerprice(customer,item,price,unit) VALUES (?,?,?,?)");
			$stmt->execute(array($customer,$item,$_POST["price"],$_POST["unit"]));
			$rows = $stmt->rowCount();
			$conn = null;
			if ($rows) {
				$msg = "添加成功！";
			} else {
				$msg = "添加失败！";
			};
		}else{
			$msg = "添加失败！";
		};
	} else {
		$msg = "添加失败！";
	};
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>IHC录入系统 by C.F</title>
		<link rel="stylesheet" type="text/css" href="static/h-ui/css/H-ui.min.css" />
		<link rel="stylesheet" type="text/css" href="static/h-ui.admin/css/H-ui.admin.css" />
		<link rel="stylesheet" type="text/css" href="lib/Hui-iconfont/1.0.8/iconfont.css" />
		<link rel="stylesheet" type="text/css" href="static/h-ui.admin/skin/default/skin.css" id="skin" />
		<link rel="stylesheet" type="text/css" href="static/h-ui.admin/css/style.css" />
		<link rel="stylesheet" type="text/css" href="static/h-ui.admin/css/ul.css" />
		<link rel="stylesheet" type="text/css" href="static/cf.css" />
		<script type="text/javascript" src="lib/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="lib/layer/2.4/layer.js"></script>
		<script type="text/javascript" src="static/h-ui.admin/js/H-ui.admin.page.js"></script>
		<script type="text/javascript" src="static/h-ui/js/H-ui.js"></script>
		<script type="text/javascript" src="lib/My97DatePicker/4.8/WdatePicker.js"></script>
		<script type="text/javascript" src="static/cf.js"></script>
	</head>
<body>
<header class="navbar-wrapper">
	<div class="navbar navbar-fixed-top">
		<div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="index.html">石井表记精密机械(苏州)有限公司后台管理系统</a>
			<a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
			<nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
			<ul class="cl">
				<li>超级管理员</li>
				<li class="dropDown dropDown_hover"> <a href="#" class="dropDown_A">admin <i class="Hui-iconfont">&#xe6d5;</i></a>
					<ul class="dropDown-menu menu radius box-shadow">
						<li><a href="javascript:;" onClick="myselfinfo()">个人信息</a></li>
						<li><a href="#">切换账户</a></li>
						<li><a href="#">退出</a></li>
					</ul>
				</li>
				<li id="Hui-msg"> <a href="#" title="消息"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>
					<li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
						<ul class="dropDown-menu menu radius box-shadow">
							<li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
							<li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
							<li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
							<li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
							<li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
							<li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</header>
<aside class="Hui-aside">
	<div class="menu_dropdown bk_2">
		<dl id="menu-article">
			<dt><i class="Hui-iconfont">&#xe616;</i> 订单管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="sell_order_list.html">受注列表</a></li>
					<li><a href="sell_order_add.html">新增受注</a></li>
				</ul>
			</dd>
		</dl>
		<dl id="menu-picture">
			<dt><i class="Hui-iconfont">&#xe645;</i> 采购管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="buy_ppo_list.html">发注列表</a></li>
					<li><a href="buy_ppo_add.html">新增发注</a></li>
				</ul>
			</dd>
		</dl>
		<dl id="menu-picture">
			<dt><i class="Hui-iconfont">&#xe644;</i> 库存管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="stock_list.html">库存列表</a></li>
				</ul>
			</dd>
		</dl>
		<dl id="menu-comments">
			<dt><i class="Hui-iconfont">&#xe683;</i> 查询管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="item_list.html.html">产品查询</a></li>
					<li><a href="customer_list.html">客户查询</a></li>
					<li><a href="supplier_list.html">供应商查询</a></li>
				</ul>
			</dd>
		</dl>
		<dl id="menu-member">
			<dt><i class="Hui-iconfont">&#xe607;</i> 新增管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="supplier_add.html">供应商新增</a></li>
					<li><a href="item_add.html">产品新增</a></li>
					<li><a href="customer_add.html">客户新增</a></li>
					<li><a href="customer_price_add.html">客户价格新增</a></li>	
				</ul>
			</dd>
		</dl>
		<dl id="menu-member">
			<dt><i class="Hui-iconfont">&#xe62d;</i> 用户管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="user_list.html" title="会员列表">用户列表</a></li>
					<li><a href="user_add.html" title="会员列表">用户列表</a></li>
				</ul>
			</dd>
		</dl>
		<dl id="menu-tongji">
			<dt><i class="Hui-iconfont">&#xe61a;</i> 数据统计<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a href="sell_statistics.html">受注统计</a></li>
					<li><a href="buy_statistics.html">发注统计</a></li>
				</ul>
			</dd>
		</dl>
	</div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
	<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页
		<span class="c-gray en">&gt;</span>
		新增管理
		<span class="c-gray en">&gt;</span>
		客户价格
	</nav>
	<div class="Hui-article">
		<article class="cl pd-20">
		<div class="Huialert Huialert-success"><i class="Hui-iconfont">&#xe6a6;</i><?php echo $msg; ?></div>
		</article>
	</div>
</section>
</body>
</html>