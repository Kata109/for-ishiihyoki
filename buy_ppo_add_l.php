<?php
if (isset($_POST['data'])) {
		$msg = "";
		$emsg = "";
		$ifsupplier = true;
		$supplier_true = "";
		$servername = "localhost";
		$username = "root";
		$password = "66329525";
		$dbname = "ihcdata";
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("SET NAMES utf8");

		//查询发注单号数据
		$stmt = $conn->prepare("SELECT ppo_number_c,ppo_number_j,ppo_number_cy,ppo_number_jy FROM ppo_number"); 
		$stmt->execute();
		$number_array = $stmt->fetch(PDO::FETCH_ASSOC);
		$ppo_number_cy = $number_array['ppo_number_cy'];
		$ppo_number_jy = $number_array['ppo_number_jy'];
		$ppo_number_c = $number_array['ppo_number_c'];
		$ppo_number_j = $number_array['ppo_number_j'];


		//获取传过来的json数据
		$data = $_POST['data'];
		$arr = json_decode($data,true);

		//判断供应商是否相同，不同则输入无效
		for ($i = 0; $i < count($arr); $i++) {
			$po = $arr[$i]['po'];
            $ihc_id = $arr[$i]['id'];
			//查询ihc_order数据
			$stmt = $conn->prepare("SELECT item FROM ihc_order WHERE po='$po' AND id='$ihc_id'"); 
			$stmt->execute();
			$order_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$nameC = $order_array['item'];
			//查询ihc_item数据
			$stmt = $conn->prepare("SELECT supplier FROM ihc_item WHERE nameC='$nameC'"); 
			$stmt->execute();
			$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$supplier = $item_array['supplier'];
			if ($i == 0){
				$supplier_true = $supplier;
			}else{
				if ($supplier == $supplier_true){
					$ifsupplier = true;
				} else {
					$ifsupplier = false;
					break;
				}
			}
		};

		if ($ifsupplier == false) {
   			$msg = "请选择正确的公司。";  
		}else{
    		for ($i = 0; $i < count($arr); $i++) {
            $po = $arr[$i]['po'];
            $ihc_id = $arr[$i]['id'];
            $ppo_delivery_time = $arr[$i]['ppo_delivery_time'];

            //查询ihc_order数据
			$stmt = $conn->prepare("SELECT customer,item,qty,date_delivery,customer_code FROM ihc_order WHERE po='$po' AND id='$ihc_id'"); 
			$stmt->execute();
			$order_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$customer = $order_array['customer'];
			$nameC = $order_array['item'];
			$qty = $order_array['qty'];
			$date_delivery = $order_array['date_delivery'];
			$customer_code = $order_array['customer_code'];

			//查询ihc_customer数据
			$stmt = $conn->prepare("SELECT nameA FROM ihc_customer WHERE customer='$customer'"); 
			$stmt->execute();
			$customer_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$nameA = $customer_array['nameA'];
			
			//查询ihc_item数据
			$stmt = $conn->prepare("SELECT ma,nameJ,type,unit,supplier,price,byway FROM ihc_item WHERE nameC='$nameC'"); 
			$stmt->execute();
			$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$ma = $item_array['ma'];
			$nameJ = $item_array['nameJ'];
			$type = $item_array['type'];
			$unit = $item_array['unit'];
			$price = $item_array['price'];
			$byway = $item_array['byway'];
			$supplier = $item_array['supplier'];

			//临时数据
			$ppo_storage_time = "2000-01-01";
			$ppo_invoice = "2000-01-01";
			$ppo_invoice_time = "2000-01-01";
			$payment = "2000-01-01";

			//更改时区
			date_default_timezone_set('PRC');
			$ppo_time = date("Y-m-d");
			//查重复
            $stmt = $conn->prepare("SELECT * FROM ihc_ppo WHERE po='$po' AND customer_code='$customer_code'"); 
			$stmt->execute();
			$rowC = $stmt->rowCount();
			
			if($rowC==0){
				
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
						$msg .= $po."发注成功。".PHP_EOL;
					} else {
						$msg .= $po."发注失败。".PHP_EOL;
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
						$msg .= $po."发注成功。".PHP_EOL;
					} else {
						$msg .= $po."发注失败。".PHP_EOL;
					};
				};
			} else {
				$emsg .= $po."发注失败。".PHP_EOL;
			};

        };
		};

		$conn = null;

		$response = array(
			'errno' => 0,
			'errmsg' => $msg.$emsg,
			'data' => true,
		);

		echo json_encode($response);

    };
?>