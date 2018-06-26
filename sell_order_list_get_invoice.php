<?php
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$invoice_time = "2000-01-01";
$income_time = "2000-01-01";
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
    $stmt = $conn->prepare("SELECT * FROM ihc_order WHERE invoice_time<>? AND income_time=?"); 
    $stmt->execute(array($invoice_time,$income_time));
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //受注页面显示
    for ($i=0;$i<count($result);$i++) {
        echo "<tr class='text-c'><td><input type='checkbox' value='".$result[$i]['id']."' name='".$result[$i]['po']."'></td>";
        if ($result[$i]['customer']=="株式会社石井表记") {
            echo "<td>".$result[$i]['invoice_num']."</td><td>".$result[$i]['invoice_time']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['tol']."日元</td></tr>";
        } else {
            echo "<td>".$result[$i]['invoice_num']."</td><td>".$result[$i]['invoice_time']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['tol']."元</td></tr>";
        };

	};
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
};
$conn = null;
?>