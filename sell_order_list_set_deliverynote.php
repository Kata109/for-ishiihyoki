<?php
if (isset($_POST['data'])) {
		$servername = "localhost";
		$username = "root";
		$password = "66329525";
		$dbname = "ihcdata";
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("SET NAMES utf8");
		//删除临时表里内容
		$stmt = $conn->prepare("truncate table deliverynote_value");
		$stmt->execute();
		$stmt = $conn->prepare("truncate table deliverynote_name");
		$stmt->execute();
		//获取传过来的json数据
		$data = $_POST['data'];
		$arr = json_decode($data,true);
        for ($i = 0; $i < count($arr); $i++) {
            $po = $arr[$i]['po'];
            $ihc_id = $arr[$i]['id'];
            //查询产品数据
			$stmt = $conn->prepare("SELECT customer,item,ihc_type,ihc_code,qty,price,customer_code FROM ihc_order WHERE po='$po' AND id='$ihc_id'"); 
			$stmt->execute();
			$order_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$customer = $order_array['customer'];
			$item = $order_array['item'];
			$ihc_type = $order_array['ihc_type'];
			$ihc_code = $order_array['ihc_code'];
			$qty = $order_array['qty'];
			$price = $order_array['price'];
			$customer_code = $order_array['customer_code'];

			//查询客户数据
			$stmt = $conn->prepare("SELECT dayNet,consignee,consignee_address,consignee_tel FROM ihc_customer WHERE customer='$customer'"); 
			$stmt->execute();
			$customer_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$ihc_dayNet = $customer_array['dayNet'];
			$ihc_consignee = $customer_array['consignee'];
			$ihc_consignee_address = $customer_array['consignee_address'];
			$ihc_consignee_tel = $customer_array['consignee_tel'];

			//查询客户单位
			$stmt = $conn->prepare("SELECT unit FROM ihc_customerprice WHERE customer='$customer' AND item='$item'"); 
			$stmt->execute();
			$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$unit = $item_array['unit'];

			


			if ($i == 0){
				//临时数据插入表
				$stmt = $conn->prepare("INSERT INTO deliverynote_name(customer,ihc_address,ihc_dayNet,ihc_contacts,ihc_tel) VALUES (?,?,?,?,?)");
				$stmt->execute(array($customer,$ihc_consignee_address,$ihc_dayNet,$ihc_consignee,$ihc_consignee_tel));
				$stmt = $conn->prepare("INSERT INTO deliverynote_value(po,item,ihc_type,ihc_code,qty,unit,customer_code,price) VALUES (?,?,?,?,?,?,?,?)");
				$stmt->execute(array($po,$item,$ihc_type,$ihc_code,$qty,$unit,$customer_code,$price));

				$c = $customer;
				$rows = 1;
			} else {
				if ($customer == $c){
					//临时数据插入表
					$stmt = $conn->prepare("INSERT INTO deliverynote_name(customer,ihc_address,ihc_dayNet,ihc_contacts,ihc_tel) VALUES (?,?,?,?,?)");
					$stmt->execute(array($customer,$ihc_consignee_address,$ihc_dayNet,$ihc_consignee,$ihc_consignee_tel));
					$stmt = $conn->prepare("INSERT INTO deliverynote_value(po,item,ihc_type,ihc_code,qty,unit,customer_code,price) VALUES (?,?,?,?,?,?,?,?)");
					$stmt->execute(array($po,$item,$ihc_type,$ihc_code,$qty,$unit,$customer_code,$price));
				} else {
					$rows = 0;
				};
			};
        };
		$conn = null;
		
		if($rows==1){
			$response = array(
			'errno' => 1,
			'errmsg' => 'success',
			'data' => true,
		);
		}else{
			$response = array(
			'errno' => 0,
			'errmsg' => 'fail',
			'data' => false,
		);
		};
		echo json_encode($response);
    };
?>