<?php
		$servername = "localhost";
		$username = "root";
		$password = "66329525";
		$dbname = "ihcdata";
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("SET NAMES utf8");
		//查询表头数据
		$stmt = $conn->prepare("SELECT supplier,supplier_address,supplier_tel,supplier_contacts,supplier_dayNet FROM ppo_name");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		//查询表中数据
		$stmt = $conn->prepare("SELECT id,ppo_number,ppo_time,ppo_delivery_time,ma,nameC,nameJ,type,price,qty,nameA,user_time FROM ppo_value");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//设置时间格式
		date_default_timezone_set('PRC');
		$ta = 0;
		$tb = 0;


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $result[0]['ppo_number']; ?></title>
	<script src="http://cdn.static.runoob.com/libs/jquery/1.10.2/jquery.min.js"></script>
	<style>
	@media print{
		INPUT {display:none}
	}
	div.divc {
		position:absolute;
		top:350px;
		left:40px;
		right:40px;
	}
	table.ccc {
    	font-family: Arial, Helvetica, sans-serif;
		color:#222;
	}
	table.bbb {
		font-size:14px;
    	font-family: Arial, Helvetica, sans-serif;
		color:#222;
    	border: 1px solid black;
		border-collapse: collapse;
		border-spacing:0;
	}
	table.bbb th {
		text-align:center;
    	border: 1px solid black;
		background-color: #ffffff;
		vertical-align:bottom;
		padding: 10px;
	}
	table.bbb td {
		padding: 10px;
    	border: 1px solid black;
		background-color: #ffffff;
	}
	table.aaa {
    	font-family: Arial, Helvetica, sans-serif;
		color:#222;	
	}
	h5.pos_abs
	{
	position:absolute;
	right:40px;
	top:80px
	}
	h5.pos_abs2
	{
	position:absolute;
	left:40px;
	top:120px
	}
	h5.pos_abs3
	{
	position:absolute;
	left:150px;
	top:120px;
	text-decoration:underline
	}
	h5.pos_abs4
	{
	position:absolute;
	left:40px;
	top:200px;
	}
	h5.pos_abs5
	{
	position:absolute;
	left:150px;
	top:200px;
	text-decoration:underline
	}
	h5.pos_abs6
	{
	position:absolute;
	left:40px;
	top:280px;
	}
	h5.pos_abs7
	{
	position:absolute;
	left:150px;
	top:280px;
	}
	</style>
</head>
<body>
	<TABLE class="aaa" width="100%" align="center" cellpadding="1">
	<THEAD style="display:table-header-group;font-weight:bold">
		<TR><Td colspan="4" rowspan="1" align="center"><img src="logo.png"  alt="logo" style="width:180px" /><br><h3>PURCHASE ORDER</Td></TR>
		<TR><TD><h5 class="pos_abs">PO NO:<?php echo $result[0]['ppo_number']; ?><br>Date:<?php echo date("M.dS Y"); ?></h5></TD></TR>
	</THEAD>
	<TBODY style="text-align:left">
		<TR><TD><h5 class="pos_abs2">MESSERS:</h5><h5 class="pos_abs3">ISHIIHYOKI CO.,LTD<br>ATTN: MS.EGUSA<br>5、ASAHIOKA、KANNABE-CHO、FUKUYAMA-CITY、HIROSHIMA、<br>720-2113、JAPAN</h5><h5 class="pos_abs4">CONSIGNEE:</h5><h5 class="pos_abs5">ISHIIHYOKI（SUZHOU）CO.,LTD<br>ROOM 1538,NO.211,ZHUJIANG SOUTH ROAD,<br>MUDU>WUZHONG DISTRICT.SUZHOU,JIANGSU 215101,CHINA</h5><h5 class="pos_abs6">DELIVERY:<br>QUOTATION NO:</h5><h5 class="pos_abs7"><?php echo $result[0]['ppo_delivery_time']; ?></h5></TD></TR>
	</TBODY>
	</TABLE>
<div class = "divc">
<TABLE class="bbb" width="100%" align="center">
<THEAD style="display:table-header-group;font-weight:bold">
	<TR><Th width="10%">Parts NO</Th><Th width="15%">PARTS NAME</Th><Th width="15%">DRAWING NO.MATERIAL</Th><Th width="10%">UNIT PRICE</Th><Th width="5%">Q'TY</Th><Th width="10%">AMOUNT</Th></TR>
</THEAD>
<TBODY style="text-align:center">
        <?php
        for ($i=0;$i<count($result);$i++) {
        	$ta = $result[$i]['price'] * $result[$i]['qty'];
        	$tb += $ta;
  			echo "<tr><td>".$result[$i]['ma']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['price']."</td><td>".$result[$i]['qty']."</td><td>".$ta."</td></tr>";
  		}
        ?>
</TBODY>
<TFOOT style="display:table-footer-group;font-weight:bold">
	<TR><TD colspan="5" align="right">TOTAL DDU SHANGHAI</TD><TD align="center"><?php echo $tb; ?></TD></TR>
</TFOOT>
</TABLE>
<table width="100%" align="center" class="ccc">
<tr><td>
<h5>REMARKS：<?php echo $result[0]['nameA']; ?><br>ユーザー希望納期:<?php echo $result[0]['user_time']; ?></h5><h5>Price Basis : Japanese Yen<br>Packing : Export Standard<br>Destination : CHINA</h5>
</td><td align="right"><h4>ISHIIHYOKI（SUZHOU)　CO.,LTD</h4></td></tr><tr><td colspan="2"><hr  align="right" style="height:1px;border:none;border-top:1px solid #555555;width:40%;" /></td></tr>

</table>
</div>
</body>
</html>