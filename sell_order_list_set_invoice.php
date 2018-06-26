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
		$invoice_time = date("Y-m-d H:i:s");
        for ($i=0; $i < count($arr); $i++) {
            $po=$arr[$i]['po'];
            $ihc_id=$arr[$i]['id'];
            $invoice_num=$arr[$i]['invoice_num'];
            if ($invoice_num!="") {
            	$stmt = $conn->prepare("UPDATE ihc_order SET invoice_time='$invoice_time',invoice_num='$invoice_num' WHERE po='$po' AND id='$ihc_id'"); 
				$stmt->execute();
				$rows = $stmt->rowCount();
				if ($rows) {
					$msg .= $po."出票成功。".PHP_EOL;
				} else {
					$msg .= $po."出票失败。".PHP_EOL;
				}
            } else {
            	$msg .= $po."出票失败。".PHP_EOL;
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