<?php
class TableRows extends RecursiveIteratorIterator {
    function __construct($it) { 
        parent::__construct($it, self::LEAVES_ONLY); 
    }
 
    function current() {
        return "<option value=\"" . parent::current(). "\">";
    }
} 
 
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";
$customer = strip_tags($_POST['customer']);
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES utf8");
    $stmt = $conn->prepare("SELECT item FROM ihc_customerPrice WHERE customer=?"); 
    $stmt->execute(array($customer));
    // 设置结果集为关联数组
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) { 
        echo $v;
		}
	}
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
$conn = null;
?>