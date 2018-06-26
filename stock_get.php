<?php
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";

 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->query("SET NAMES utf8");
    $stmt = $conn->prepare("SELECT * FROM ihc_stock"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //受注页面显示
    for ($i=0;$i<count($result);$i++) {
    	echo "<tr class=\"text-c\">";
  		echo "<td>".$result[$i]['ma']."</td><td>".$result[$i]['nameC']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['instock']."</td></tr>";
	};
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>