<?php
	//接受传递过来的参数
	//去除传递过来的多余标签，或者用整型约束
	$po = strip_tags($_POST['po']);
	$customer = strip_tags($_POST['customer']);
	$item = strip_tags($_POST['item']);
	$qty = intval($_POST['qty']);
	$customer_code = strip_tags($_POST['code']);
	$order_time = strip_tags($_POST['date']);
	$date_delivery = strip_tags($_POST['dateDelivery']);
	$ihc_c = strip_tags($_POST['ihc_class']);
	$ihc_taxation = strip_tags($_POST['ihc_taxation']);
	$tol = strip_tags($_POST['tol']);
	$ihc_user = "陈斐";
	//更改时区
	date_default_timezone_set('PRC');
	$modify_time = date("Y-m-d");
	$stock_time = "2000-01-01";
	$stock_num = "2000-01-01";
	$invoice_time = "2000-01-01";
	$invoice_num = "2000-01-01";
	$income_time = "2000-01-01";	
	$servername = "localhost";
	$username = "root";
	$password = "66329525";
	$dbname = "ihcdata";
	$ihc_remark = "";

	if ($ihc_taxation!="1.03" && $ihc_taxation!="1" && $customer=="深圳连群电子有限公司苏州分公司") {
		$ihc_remark .= $ihc_taxation;
	};

	if ($ihc_taxation!="1.03") {
		$ihc_taxation = "1";
	};

	try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");

	$stmt = $conn->prepare("SELECT * FROM ihc_order WHERE po='$po' AND customer_code='$customer_code' AND item='$item'"); 
			$stmt->execute();
			$rowC = $stmt->rowCount();
	if ($rowC==0) {
		//查询价格
		$sql = "SELECT * FROM ihc_customerprice WHERE customer=? AND item=?";
		$stmt = $conn->prepare($sql);
		$stmt->execute(array($customer,$item));
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		$price = $data['price'];
		//查询型号、品目番号
		$sql = "SELECT * FROM ihc_item WHERE nameC=?";
		$stmt = $conn->prepare($sql);
		$stmt->execute(array($item));
		$item_data = $stmt->fetch(PDO::FETCH_ASSOC);
		$ihc_type = $item_data['type'];
		$ihc_code = $item_data['ma'];
		$nameJ = $item_data['nameJ'];
		$supplier = $item_data['supplier'];

		//查询区域并设定ihc_class
		$sql = "SELECT area FROM ihc_customer WHERE customer=?";
		$stmt = $conn->prepare($sql);
		$stmt->execute(array($customer));
		$area_data = $stmt->fetch(PDO::FETCH_ASSOC);
		$area = $area_data['area'];

		if ($tol=="") {
			$tol = $price*$qty;
		};


		if ($supplier=="株式会社石井表记") {
			$area.= "J";
		} else {
			$area.= "C";
		};
		$ihc_class = substr_replace($area,$ihc_c,4,0);

		if($price != null && $ihc_type != null && $ihc_code != null){
			//添加到数据表
			$sql = "INSERT INTO ihc_order(po,customer,item,nameJ,ihc_type,ihc_code,qty,ihc_class,ihc_taxation,ihc_remark,price,tol,customer_code,order_time,date_delivery,ihc_user,modify_time,stock_time,stock_num,invoice_time,invoice_num,income_time) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute(array($po,$customer,$item,$nameJ,$ihc_type,$ihc_code,$qty,$ihc_class,$ihc_taxation,$ihc_remark,$price,$tol,$customer_code,$order_time,$date_delivery,$ihc_user,$modify_time,$stock_time,$stock_num,$invoice_time,$invoice_num,$income_time));
			$rows = $stmt->rowCount();
		}else{
			$rows = null;
		};
	} else {
		$rows = null;
	};
	} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
	};
	$conn = null;
	//返回信息
	if($rows){
		$response = array(
			'errno' => 0,
			'errmsg' => 'success',
			'data' => true,
		);
	}else{
		$response = array(
			'errno' => -1,
			'errmsg' => 'fail',
			'data' => false,
		);
	}
	
	echo json_encode($response);

?>