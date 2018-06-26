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
		$stmt = $conn->prepare("truncate table ppo_value");
		$stmt->execute();
		$stmt = $conn->prepare("truncate table ppo_name");
		$stmt->execute();
		//获取传过来的json数据
		$data = $_POST['data'];
		$arr = json_decode($data,true);
        for ($i = 0; $i < count($arr); $i++) {
            $ppo_number = $arr[$i]['ppo_number'];
            $ihc_id = $arr[$i]['id'];

            //查询数据
			$stmt = $conn->prepare("SELECT ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,user_time,byway FROM ihc_ppo WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
			$stmt->execute();
			$ppo_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$ppo_time = $ppo_array['ppo_time'];
			$ppo_delivery_time = $ppo_array['ppo_delivery_time'];
			$ma = $ppo_array['ma'];
			$nameC = $ppo_array['nameC'];
			$nameJ = $ppo_array['nameJ'];
			$type = $ppo_array['type'];
			$unit = $ppo_array['unit'];
			$price = $ppo_array['price'];
			$qty = $ppo_array['qty'];
			$nameA = $ppo_array['nameA'];
			$user_time = $ppo_array['user_time'];
			$byway = $ppo_array['byway'];
			$supplier = $ppo_array['supplier'];
            //查询数据
			$stmt = $conn->prepare("SELECT paymentDays,contacts,address,tel FROM ihc_supplier WHERE supplier='$supplier'"); 
			$stmt->execute();
			$supplier_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$supplier_address = $supplier_array['address'];
			$supplier_tel = $supplier_array['tel'];
			$supplier_contacts = $supplier_array['contacts'];
			$supplier_paymentDays = $supplier_array['paymentDays'];

            //判断供应商是否一致
            if ($i == 0){
				if ($supplier == "株式会社石井表记") {
				//临时数据插入表
				$stmt = $conn->prepare("INSERT INTO ppo_value(ppo_number,ppo_time,ppo_delivery_time,ma,nameC,nameJ,type,unit,price,qty,nameA,user_time,byway) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$stmt->execute(array($ppo_number,$ppo_time,$ppo_delivery_time,$ma,$nameC,$nameJ,$type,$unit,$price,$qty,$nameA,$user_time,$byway));
				$stmt = $conn->prepare("INSERT INTO ppo_name(supplier,supplier_address,supplier_tel,supplier_contacts,supplier_dayNet) VALUES (?,?,?,?,?)");
				$stmt->execute(array($supplier,$supplier_address,$supplier_tel,$supplier_contacts,$supplier_paymentDays));
				//记录订单号
				$p = $ppo_number;
				$rows = 1;
				} else {
				//临时数据插入表
				$stmt = $conn->prepare("INSERT INTO ppo_value(ppo_number,ppo_time,ppo_delivery_time,ma,nameC,nameJ,type,unit,price,qty,nameA,user_time,byway) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$stmt->execute(array($ppo_number,$ppo_time,$ppo_delivery_time,$ma,$nameC,$nameJ,$type,$unit,$price,$qty,$nameA,$user_time,$byway));
				$stmt = $conn->prepare("INSERT INTO ppo_name(supplier,supplier_address,supplier_tel,supplier_contacts,supplier_dayNet) VALUES (?,?,?,?,?)");
				$stmt->execute(array($supplier,$supplier_address,$supplier_tel,$supplier_contacts,$supplier_paymentDays));
				//记录订单号
				$p = $ppo_number;
				$rows = 2;
				}
			} else {
				//判断订单号是否一致
				if ($ppo_number == $p){
				// 	临时数据插入表
				$stmt = $conn->prepare("INSERT INTO ppo_value(ppo_number,ppo_time,ppo_delivery_time,ma,nameC,nameJ,type,unit,price,qty,nameA,user_time,byway) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$stmt->execute(array($ppo_number,$ppo_time,$ppo_delivery_time,$ma,$nameC,$nameJ,$type,$unit,$price,$qty,$nameA,$user_time,$byway));
				$stmt = $conn->prepare("INSERT INTO ppo_name(supplier,supplier_address,supplier_tel,supplier_contacts,supplier_dayNet) VALUES (?,?,?,?,?)");
				$stmt->execute(array($supplier,$supplier_address,$supplier_tel,$supplier_contacts,$supplier_paymentDays));
				} else {
					$rows = 0;
				};
			};
        };
		$conn = null;

		$response = array(
			'msg' => $rows,
		);
		echo json_encode($response);
    };
?>