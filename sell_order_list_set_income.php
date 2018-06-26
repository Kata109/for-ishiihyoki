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
		$income_time = date("Y-m-d H:i:s");
        for ($i=0; $i < count($arr); $i++) {
            $po=$arr[$i]['po'];
            $ihc_id=$arr[$i]['id'];
			$stmt = $conn->prepare("UPDATE ihc_order SET income_time='$income_time' WHERE po='$po' AND id='$ihc_id'"); 
			$stmt->execute();
			$rows = $stmt->rowCount();
			if ($rows) {
				$msg .= $po."入账成功。".PHP_EOL;
			} else {
				$msg .= $po."入账失败。".PHP_EOL;
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