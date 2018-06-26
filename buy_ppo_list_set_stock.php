<?php
if (isset($_POST['data'])) {
		$msg = "";
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
		$ppo_storage_time = date("Y-m-d H:i:s");
        for ($i=0; $i < count($arr); $i++) {
            $ppo_number=$arr[$i]['ppo_number'];
            $ihc_id=$arr[$i]['id'];
            //查询发注信息
            $stmt = $conn->prepare("SELECT ma,nameC,nameJ,type,qty FROM ihc_ppo WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
			$stmt->execute();
			$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$ma = $item_array['ma'];
			$nameC = $item_array['nameC'];
			$nameJ = $item_array['nameJ'];
			$type = $item_array['type'];
			$qty = $item_array['qty'];
			//查询在库信息
			$stmt = $conn->prepare("SELECT * FROM ihc_stock WHERE ma='$ma' AND nameC='$nameC'"); 
			$stmt->execute();
			$rowC = $stmt->rowCount();
			if ($rowC==0) {
				//新建入库
				$stmt = $conn->prepare("INSERT INTO ihc_stock(ma,nameC,nameJ,type,instock) VALUES (?,?,?,?,?)");
				$stmt->execute(array($ma,$nameC,$nameJ,$type,$qty));
				$rows = $stmt->rowCount();
				if ($rows) {
					$msg .= $ppo_number."入库成功，";
				} else {
					$msg .= $ppo_number."入库失败，";
				};
			} else {
				//获取在库数
				$stmt = $conn->prepare("SELECT * FROM ihc_stock WHERE ma='$ma' AND nameC='$nameC'"); 
				$stmt->execute();
				$stock_array = $stmt->fetch(PDO::FETCH_ASSOC);
				$instock = $stock_array['instock'];
				$newstock = $instock + $qty;
				//更新入库
				$stmt = $conn->prepare("UPDATE ihc_stock SET instock='$newstock' WHERE ma='$ma' AND nameC='$nameC'"); 
				$stmt->execute();
				$rows = $stmt->rowCount();
				if ($rows) {
					$msg .= $ppo_number."入库成功，";
				} else {
					$msg .= $ppo_number."入库失败，";
				};
			};
			//更新入库时间
			$stmt = $conn->prepare("UPDATE ihc_ppo SET ppo_storage_time='$ppo_storage_time' WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
			$stmt->execute();
			$rows = $stmt->rowCount();
			if ($rows) {
				$msg .= $ppo_number."时间更新成功。".PHP_EOL;
			} else {
				$msg .= $ppo_number."时间更新失败。".PHP_EOL;
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