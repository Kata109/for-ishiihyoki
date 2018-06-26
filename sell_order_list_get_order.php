<?php
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$stock_time = "2000-01-01";
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->query("SET NAMES utf8");
    $stmt = $conn->prepare("SELECT * FROM ihc_order WHERE stock_time=?"); 
    $stmt->execute(array($stock_time));
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //受注页面显示
    for ($i=0;$i<count($result);$i++) {
          //判断交期是否小于5天
          date_default_timezone_set('PRC');
          $new_time = date_create(date("Y-m-d"));
          $old_time = date_create($result[$i]['date_delivery']);
          $interval = date_diff($new_time, $old_time);
          //判断是否已经发注
          $stmt = $conn->prepare("SELECT * FROM ihc_ppo WHERE po=? AND customer_code=?"); 
          $stmt->execute(array($result[$i]['po'],$result[$i]['customer_code']));
          $rows = $stmt->rowCount();

          echo "<tr class='text-c'><td><input type='checkbox' value='".$result[$i]['id']."' name='".$result[$i]['po']."'></td>";
    	if ($result[$i]['customer']=="株式会社石井表记") {

          if ($interval->format("%R%a") <= 5 && $rows==0) {
            echo "<td class='success'>".$result[$i]['modify_time']."</td><td class='danger'>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['tol']."日元</td></tr>";
          } else if ($interval->format("%R%a") <= 5 && $rows!=0) {
            echo "<td>".$result[$i]['modify_time']."</td><td class='danger'>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['tol']."日元</td></tr>";
          } else if ($interval->format("%R%a") > 5 && $rows==0) {
            echo "<td class='success'>".$result[$i]['modify_time']."</td><td>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['tol']."日元</td></tr>";
          } else {
            echo "<td>".$result[$i]['modify_time']."</td><td>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."日元</td><td>".$result[$i]['tol']."日元</td></tr>";
          };
        } else {
          if ($interval->format("%R%a") <= 5 && $rows==0){
          echo "<td class='success'>".$result[$i]['modify_time']."</td><td class='danger'>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['tol']."元</td></tr>";
        } else if ($interval->format("%R%a") <= 5 && $rows!=0) {
          echo "<td>".$result[$i]['modify_time']."</td><td class='danger'>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['tol']."元</td></tr>";
        } else if ($interval->format("%R%a") > 5 && $rows==0) {
          echo "<td class='success'>".$result[$i]['modify_time']."</td><td>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['tol']."元</td></tr>";
        } else {
            echo "<td>".$result[$i]['modify_time']."</td><td>".$result[$i]['date_delivery']."</td><td>".$result[$i]['po']."</td><td>".$result[$i]['customer']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."元</td><td>".$result[$i]['tol']."元</td></tr>";
        };
  		};
	 };
	// foreach($result as $key=>$val) {
	// 	if (is_array($val)) {   //判断$val的值是否是一个数组，如果是，则进入下层遍历
	// 		echo "<tr class=\"text-c\"><td><input type=\"checkbox\" value=\"".$val['modify_time']."\" name=\"".$val['po']."\"></td>";
	// 		foreach($val as $key=>$val) {	
	// 			echo "<td>".$val."</td>";
	// 		}
	// 		echo "</tr>";
	// 	}
	// }
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>