<?php

$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$customer = strip_tags($_POST['customer']);
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
	//如果没有使用百分号 %, LIKE 子句与等号 = 的效果是一样的。
    $stmt = $conn->prepare("SELECT * FROM ihc_customer WHERE customer LIKE '%$customer%'"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	for ($i=0; $i < count($result); $i++) {
    			echo "<tr class='text-c'><td>".$result[$i]['customer']."</td><td>".$result[$i]['address']."</td><td>".$result[$i]['contacts']."</td><td>".$result[$i]['tel']."</td><td>".$result[$i]['dayNet']."天</td><td>".$result[$i]['area']."</td></tr>";
    	}
    }
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
$conn = null;
?>