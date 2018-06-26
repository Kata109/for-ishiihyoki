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
		$stock_time = date("Y-m-d H:i:s");
        for ($i=0; $i < count($arr); $i++) {
            $po=$arr[$i]['po'];
            $ihc_id=$arr[$i]['id'];
            $stock_num=$arr[$i]['stock_num'];
        if ($stock_num!="") {
            //查询受注信息
            $stmt = $conn->prepare("SELECT item,nameJ,ihc_type,qty,customer_code FROM ihc_order WHERE po='$po' AND id='$ihc_id'"); 
			$stmt->execute();
			$item_array = $stmt->fetch(PDO::FETCH_ASSOC);
			$nameC = $item_array['item'];
			$nameJ = $item_array['nameJ'];
			$type = $item_array['ihc_type'];
			$qty = $item_array['qty'];
			$customer_code = $item_array['customer_code'];

            //查询在库信息
			$stmt = $conn->prepare("SELECT * FROM ihc_stock WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
			$stmt->execute();
			$rowC = $stmt->rowCount();
			if ($rowC==0) {
				$msg .= $po."没有库存，请先入库。".PHP_EOL;
			} else {
				//获取在库数
				$stmt = $conn->prepare("SELECT * FROM ihc_stock WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
				$stmt->execute();
				$stock_array = $stmt->fetch(PDO::FETCH_ASSOC);
				$instock = $stock_array['instock'];
				$newstock = $instock - $qty;

				if ($newstock>=0) {
					if ($newstock==0) {
						//更新出库
						$stmt = $conn->prepare("DELETE FROM ihc_stock WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
						$stmt->execute();
						$rows = $stmt->rowCount();
						if ($rows) {
							$msg .= $po."出库成功，";
						} else {
							$msg .= $po."出库失败，";
						};
					} else {
						//更新出库
						$stmt = $conn->prepare("UPDATE ihc_stock SET instock='$newstock' WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
						$stmt->execute();
						$rows = $stmt->rowCount();
						if ($rows) {
							$msg .= $po."出库成功，";
						} else {
							$msg .= $po."出库失败，";
						};
					};
					//记录出库订单
					$stmt = $conn->prepare("INSERT INTO ihc_stock_invoice_p(po,customer_code) VALUES (?,?)"); 
					$stmt->execute(array($po,$customer_code));
					// //查询invoice在库信息
					// $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
					// $stmt->execute();
					// $rowC = $stmt->rowCount();
					// if ($rowC==0) {
					// 	$stmt = $conn->prepare("INSERT INTO ihc_stock_invoice_l(po,nameC,qty) VALUES (?,?,?)");
					// 	$stmt->execute(array($po,$nameC,$qty));
					// } else {
					// 	//获取在库数
					// 	$stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
					// 	$stmt->execute();
					// 	$stock_array = $stmt->fetch(PDO::FETCH_ASSOC);
					// 	$instock = $stock_array['instock'];
					// 	$newstock = $instock - $qty;

					// 	if ($newstock==0) {
					// 		//更新出库
					// 		$stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
					// 		$stmt->execute();
					// 	} else {
					// 		//更新出库
					// 		$stmt = $conn->prepare("UPDATE ihc_stock_invoice SET instock='$newstock' WHERE nameC='$nameC' AND nameJ='$nameJ'"); 
					// 		$stmt->execute();
					// 	};
					// };
					//更新出库时间
					$stmt = $conn->prepare("UPDATE ihc_order SET stock_time='$stock_time',stock_num='$stock_num' WHERE po='$po' AND id='$ihc_id'"); 
					$stmt->execute();
					$rows = $stmt->rowCount();
					if ($rows) {
						$msg .= $po."时间更新成功。".PHP_EOL;
					} else {
						$msg .= $po."时间更新失败。".PHP_EOL;
					};
				} else {
					$msg .= $po."库存不足，请先入库。".PHP_EOL;
				};
			};
        } else {
            $msg .= $po."送货单号输入不正确，请重新输入。".PHP_EOL;
        }
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