<?php
if (isset($_POST['data'])) {
		$msg = "";
		$ifsupplier = true;
		$supplier_true = "";
        $data=$_POST['data'];
		$servername = "localhost";
		$username = "root";
		$password = "66329525";
		$dbname = "ihcdata";
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("SET NAMES utf8");
		$arr=json_decode($data,true);
		date_default_timezone_set('PRC');
		$ppo_invoice_time = date("Y-m-d H:i:s");
		
		//判断供应商是否相同，不同则输入无效
		for ($i = 0; $i < count($arr); $i++) {
			$ppo_number=$arr[$i]['ppo_number'];
            $ihc_id=$arr[$i]['id'];
			//查询ihc_item数据
			$stmt = $conn->prepare("SELECT supplier FROM ihc_ppo WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
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
			for ($i=0; $i < count($arr); $i++) {
            	$ppo_number=$arr[$i]['ppo_number'];
            	$ihc_id=$arr[$i]['id'];
            	$ppo_invoice=$arr[$i]['ppo_invoice'];
            	if($ppo_invoice!=""){
       				//查询发注信息
            		$stmt = $conn->prepare("SELECT ma,nameC,nameJ,type,qty,po FROM ihc_ppo WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
					$stmt->execute();
					$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
					$ma = $item_array['ma'];
					$nameC = $item_array['nameC'];
					$nameJ = $item_array['nameJ'];
					$type = $item_array['type'];
					$qty = $item_array['qty'];
					$po = $item_array['po'];
					//记录Invoice库存
					$stmt = $conn->prepare("INSERT INTO ihc_stock_invoice(ma,nameC,nameJ,type,instock,po,poid) VALUES (?,?,?,?,?,?,?)");
					$stmt->execute(array($ma,$nameC,$nameJ,$type,$qty,$po,$ihc_id));
					$rows = $stmt->rowCount();
					if ($rows) {
						$msg .= $ppo_number."入库成功，";
					} else {
						$msg .= $ppo_number."入库失败，";
					};
            		$stmt = $conn->prepare("UPDATE ihc_ppo SET ppo_invoice_time='$ppo_invoice_time',ppo_invoice='$ppo_invoice' WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
					$stmt->execute();
					$rows = $stmt->rowCount();
					if ($rows) {
					$msg .= $ppo_number."收票成功。".PHP_EOL;
					} else {
						$msg .= $ppo_number."收票失败。".PHP_EOL;
					};
            	}else{
            		$msg .= $ppo_number."收票失败。".PHP_EOL;
            	};
        	};
		};
       
		$conn = null;
		$response = array(
			'errno' => 0,
			'errmsg' => $msg,
			'data' => true,
		);
		echo json_encode($response);
    };
?>