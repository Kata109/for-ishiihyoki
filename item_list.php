<?php

$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$code = strip_tags($_POST['code']);
$item = strip_tags($_POST['item']);
$supplier = strip_tags($_POST['supplier']);
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
	//如果没有使用百分号 %, LIKE 子句与等号 = 的效果是一样的。
    $stmt = $conn->prepare("SELECT * FROM ihc_item WHERE ma LIKE '%$code%' AND nameC LIKE '%$item%'AND supplier LIKE '%$supplier%'"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	for ($i=0; $i < count($result); $i++) {
    		if ($result[$i]['supplier']=="株式会社石井表记") {
    			echo "<tr class='text-c'><td>".$result[$i]['ma']."</td><td>".$result[$i]['nameC']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['unit']."</td><td>".$result[$i]['supplier']."</td><td>".$result[$i]['price']."日元</td></tr>";
    		} else {
    			echo "<tr class='text-c'><td>".$result[$i]['ma']."</td><td>".$result[$i]['nameC']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['unit']."</td><td>".$result[$i]['supplier']."</td><td>".$result[$i]['price']."元</td>/tr>";
    		};
    	}
    }
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
$conn = null;
?>