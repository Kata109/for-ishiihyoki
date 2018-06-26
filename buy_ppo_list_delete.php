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
        for ($i=0; $i < count($arr); $i++) {
            $ppo_number=$arr[$i]['ppo_number'];
            $ihc_id=$arr[$i]['id'];
            $stmt = $conn->prepare("DELETE FROM ihc_ppo WHERE ppo_number='$ppo_number' AND id='$ihc_id'"); 
			$stmt->execute();
			$rows = $stmt->rowCount();
			if ($rows) {
				$msg .= $ppo_number."删除成功。".PHP_EOL;
			} else {
				$msg .= $ppo_number."删除失败。".PHP_EOL;
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