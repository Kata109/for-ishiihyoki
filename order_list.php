<?php

$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$po = strip_tags($_POST['po']);
$customer = strip_tags($_POST['customer']);
$logmin = strip_tags($_POST['logmin']);
$logmax = strip_tags($_POST['logmax']);
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
	//如果没有使用百分号 %, LIKE 子句与等号 = 的效果是一样的。
    $stmt = $conn->prepare("SELECT * FROM ihc_order WHERE str_to_date(modify_time,'%Y-%m-%d')>='$logmin' AND str_to_date(modify_time,'%Y-%m-%d')<='$logmax' AND po LIKE '%$po%'AND customer LIKE '%$customer%'"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	for ($i=0; $i < count($result); $i++) {
    			if ($result[$i]['customer']=="株式会社石井表记") {
                    echo "<tr class='text-c'><td>".$result[$i]['modify_time']."</td><td>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['tol']."日元</td></tr>";
                } else {
                    echo "<tr class='text-c'><td>".$result[$i]['modify_time']."</td><td>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['tol']."元</td></tr>";
                };
    	};
    }
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
$conn = null;
?>