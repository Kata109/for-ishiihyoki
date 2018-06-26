<?php
	$msg = "";
	$servername = "localhost";
	$username = "root";
	$password = "66329525";
	$dbname = "ihcdata";
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
	//获取传值数据
	$supplier =$_POST["supplier"];
	$nameC =$_POST["item"];
	$qty = $_POST["qty"];
	$ppo_delivery_time = $_POST["datemax"];

if ($supplier!="" && $nameC!="" && $ppo_delivery_time!="" && check_zzs($qty)) {
	//查询发注单号数据
	$stmt = $conn->prepare("SELECT ppo_number_c,ppo_number_j,ppo_number_cy,ppo_number_jy FROM ppo_number"); 
	$stmt->execute();
	$number_array = $stmt->fetch(PDO::FETCH_ASSOC);
	$ppo_number_cy = $number_array['ppo_number_cy'];
	$ppo_number_jy = $number_array['ppo_number_jy'];
	$ppo_number_c = $number_array['ppo_number_c'];
	$ppo_number_j = $number_array['ppo_number_j'];

	//查询ihc_item数据
	$stmt = $conn->prepare("SELECT ma,nameJ,type,unit,price,byway FROM ihc_item WHERE nameC='$nameC'"); 
	$stmt->execute();
	$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
	$ma = $item_array['ma'];
	$nameJ = $item_array['nameJ'];
	$type = $item_array['type'];
	$unit = $item_array['unit'];
	$price = $item_array['price'];
	$byway = $item_array['byway'];
	//更改时区
	date_default_timezone_set('PRC');
	$ppo_time = date("Y-m-d");
	//临时数据
	$ppo_storage_time = "2000-01-01";
	$ppo_invoice = "2000-01-01";
	$ppo_invoice_time = "2000-01-01";
	$payment = "2000-01-01";
	$nameA = "IHC";
	$date_delivery = "库存";
	$customer_code = "库存";
	$customer = "IHC";
	$po = "库存";

	if ($supplier == "株式会社石井表记"){
		//获取订单号
		if ($ppo_number_jy==date("Y")){
			$d = date("Y");
			$y = ($ppo_number_j + 1)/100;
			$p = str_replace('.', '', ((string)$y));
			$ppo_number = "POJ".$d.$p;
			//更新订单号
			$stmt = $conn->prepare("UPDATE ppo_number SET ppo_number_j='$p'"); 
			$stmt->execute();
		}else{
			$d = date("Y");
			$p = "001";
			$ppo_number = "POJ".$d.$p;
			//更新订单号
			$stmt = $conn->prepare("UPDATE ppo_number SET ppo_number_j='$p',ppo_number_jy='$d'"); 
			$stmt->execute();
		};
		//临时数据插入表
		$stmt = $conn->prepare("INSERT INTO ihc_ppo(ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,po,customer_code,customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->execute(array($ppo_number,$ppo_time,$ppo_delivery_time,$supplier,$ma,$nameC,$nameJ,$type,$unit,$price,$qty,$nameA,$po,$customer_code,$customer,$date_delivery,$ppo_storage_time,$ppo_invoice,$ppo_invoice_time,$payment,$byway));
		$rows = $stmt->rowCount();
		if ($rows) {
			$msg = "添加成功！";
		} else {
			$msg = "添加失败！";
		};
	}else{
		//获取订单号
		if ($ppo_number_cy==date("Y")){
			$d = date("Y");
			$y = ($ppo_number_c + 1)/100;
			$p = str_replace('.', '', ((string)$y));
			$ppo_number = "PO".$d.$p;
			//更新订单号
			$stmt = $conn->prepare("UPDATE ppo_number SET ppo_number_c='$p'"); 
			$stmt->execute();
		}else{
			$d = date("Y");
			$p = "001";
			$ppo_number = "PO".$d.$p;
			//更新订单号
			$stmt = $conn->prepare("UPDATE ppo_number SET ppo_number_c='$p',ppo_number_cy='$d'"); 
			$stmt->execute();
		};
		//插入发注数据表
		$stmt = $conn->prepare("INSERT INTO ihc_ppo(ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,po,customer_code,customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->execute(array($ppo_number,$ppo_time,$ppo_delivery_time,$supplier,$ma,$nameC,$nameJ,$type,$unit,$price,$qty,$nameA,$po,$customer_code,$customer,$date_delivery,$ppo_storage_time,$ppo_invoice,$ppo_invoice_time,$payment,$byway));
		$rows = $stmt->rowCount();
		if ($rows) {
			$msg = "添加成功！";
		} else {
			$msg = "添加失败！";
		};
	};
	$conn = null;
} else {
	$msg = "添加失败！";	
};
	//判断是否为正整数
function check_zzs($varnum){
	$string_var = "0123456789";
	$len_string = strlen($varnum);
 	if(substr($varnum,0,1)=="0"){
  		return false;
  		die();
 	}else{
  		for($i=0;$i<$len_string;$i++){
   		$checkint = strpos($string_var,substr($varnum,$i,1));
   		if($checkint===false){
    		return false;
    		die();
   		};
  	};
 	return true;
 	};
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
		采购管理
		<span class="c-gray en">&gt;</span>
		新增发注
	</nav>
	<div class="Hui-article">
		<article class="cl pd-20">
		<div class="Huialert Huialert-success"><i class="Hui-iconfont">&#xe6a6;</i><?php echo $msg; ?></div>
		</article>
	</div>
</section>
</body>
</html>