<?php
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$jp = strip_tags($_POST['jp']);
$usa = strip_tags($_POST['usa']);
$logmin = strip_tags($_POST['logmin']);
$logmax = strip_tags($_POST['logmax']);

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
    $stmt = $conn->prepare("SELECT CONCAT(customer,ihc_remark) as ihc_name,ihc_class,SUM(tol/ihc_taxation) as ihc_tol FROM  ihc_order WHERE str_to_date(modify_time,'%Y-%m-%d')>='$logmin' AND str_to_date(modify_time,'%Y-%m-%d')<='$logmax' GROUP BY CONCAT(customer,ihc_remark),ihc_class"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	for ($i=0; $i < count($result); $i++) {
            if (substr($result[$i]['ihc_class'], -1)=="J") {
                if ($result[$i]['ihc_name']=="KCE") {
                    echo "<tr class='text-c'><td>".$result[$i]['ihc_name']."</td><td>".$result[$i]['ihc_class']."</td><td>".round($result[$i]['ihc_tol']*$usa,2)."</td></tr>";
                } else {
                    echo "<tr class='text-c'><td>".$result[$i]['ihc_name']."</td><td>".$result[$i]['ihc_class']."</td><td>".round($result[$i]['ihc_tol']*$jp,2)."</td></tr>";
                };
            } else {
                echo "<tr class='text-c'><td>".$result[$i]['ihc_name']."</td><td>".$result[$i]['ihc_class']."</td><td>".round($result[$i]['ihc_tol'],2)."</td></tr>";
            };
    	};
    }
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
$conn = null;
?>