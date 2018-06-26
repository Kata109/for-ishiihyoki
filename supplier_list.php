<?php

$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$supplier = strip_tags($_POST['supplier']);
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
	//如果没有使用百分号 %, LIKE 子句与等号 = 的效果是一样的。
    $stmt = $conn->prepare("SELECT * FROM ihc_supplier WHERE supplier LIKE '%$supplier%'"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	for ($i=0; $i < count($result); $i++) {
    			echo "<tr class='text-c'><td>".$result[$i]['supplier']."</td><td>".$result[$i]['address']."</td><td>".$result[$i]['contacts']."</td><td>".$result[$i]['tel']."</td><td>".$result[$i]['paymentDays']."天</td></tr>";
    	}
    }
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
$conn = null;
?>