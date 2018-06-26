<?php
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$ppo_storage_time = "2000-01-01";
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
    $stmt = $conn->prepare("SELECT * FROM ihc_ppo WHERE ppo_storage_time=?"); 
    $stmt->execute(array($ppo_storage_time));
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //受注页面显示
    for ($i=0;$i<count($result);$i++) {
        echo "<tr class='text-c'><td><input type='checkbox' value='".$result[$i]['id']."' name='".$result[$i]['ppo_number']."'></td>";
        if ($result[$i]['supplier']=="株式会社石井表记") {

            echo "<td>".$result[$i]['ppo_delivery_time']."</td><td>".$result[$i]['ppo_number']."</td><td>".$result[$i]['supplier']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['ma']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['user_time']."</td></tr>";
        } else {
            echo "<td>".$result[$i]['ppo_delivery_time']."</td><td>".$result[$i]['ppo_number']."</td><td>".$result[$i]['supplier']."</td><td>".$result[$i]['nameC']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['ma']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['user_time']."</td></tr>";
        };
	};
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
};
$conn = null;
?>