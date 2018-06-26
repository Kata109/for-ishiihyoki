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
		$stmt = $conn->prepare("SELECT id,ppo_number,ppo_time,ppo_delivery_time,ma,nameC,nameJ,type,unit,price,qty,nameA,user_time,byway FROM ppo_value");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//设置时间格式
		date_default_timezone_set('PRC');
		$ta = 0;
		$te = 0;
		$td = 0;
		$tf = 0;
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $result[0]['ppo_number']; ?></title>
	<style>
	@media print{
		INPUT {display:none}
	}
	table.bbb {
		font-family: Arial,Helvetica,sans-serif;
		font-size:11px;
		color:#222;
    	border: 1px solid black;
		border-collapse: collapse;
	}
	table.bbb th {
		padding: 8px;
    	border: 1px solid black;
		background-color: #dedede;
	}
	table.bbb td {
		padding: 8px;
    	border: 1px solid black;
		background-color: #ffffff;
	}
	table.aaa {
		font-family: Arial,Helvetica,sans-serif;
		color:#222;
	}
	</style>
</head>

<body>
<TABLE class="aaa" width="95%" align="center" cellpadding="1">
	<THEAD style="display:table-header-group;font-weight:bold">
		<TR><Td colspan="4" rowspan="1" align="center"><img src="logo.png"  alt="logo" style="width:180px" /><br><h3>石井表记精密机械（苏州）有限公司<br>订购单</h3></Td></TR>
	</THEAD>
<TBODY style="text-align:left;font-size:12px">
	<TR><TD width="12%">采购单号：</TD><TD width="50%"><?php echo $result[0]['ppo_number']; ?></TD><TD width="10%">日期：</TD><TD><?php echo date("Y-m-d"); ?></TD></TR>
	<TR><TD width="12%">供方：</TD><TD width="50%"><?php echo $data['supplier']; ?></TD><TD width="10%">购方：</TD><TD>石井表记精密机械苏州有限公司</TD></TR>
	<TR><TD width="12%">地址：</TD><TD width="50%"><?php echo $data['supplier_address']; ?></TD><TD width="10%">地址：</TD><TD>苏州市吴中区木渎镇珠江南路211号1538室</TD></TR>
	<TR><TD width="12%">联系人：</TD><TD width="50%"><?php echo $data['supplier_contacts']; ?></TD><TD width="10%">联系人：</TD><TD>陈斐</TD></TR>
	<TR><TD width="12%">电话：</TD><TD width="50%"><?php echo $data['supplier_tel']; ?></TD><TD width="10%">电话：</TD><TD>0512-66329525</TD></TR>
</TBODY>
</TABLE>
<TABLE class="bbb" width="95%" align="center">
<THEAD style="display:table-header-group;font-weight:bold">
	<TR><Th width="7%">序号</Th><Th width="25%">商品名称</Th><Th width="25%">型号</Th><Th width="8%">单位</Th><Th width="8%">数量</Th><Th width="9%">未税单价（RMB）</Th><Th width="9%">未税金额（RMB）</Th><Th width="9%">含税金额（RMB）</Th></TR>
</THEAD>
<TBODY style="text-align:center">
	<?php
		for ($i=0;$i<count($result);$i++) {
			$ta = sprintf("%.2f",($result[$i]['price']*$result[$i]['qty']));
			$td = sprintf("%.2f",($ta*1.16));
  			echo "<tr><td>".$result[$i]['id']."</td><td>".$result[$i]['nameC']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['unit']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['price']."</td><td>".$ta."</td><td>".$td."</td></tr>";
  			$te += $ta;
  			$tf += $td;
		}
	?>
</TBODY>
<TFOOT style="display:table-footer-group;font-weight:bold">
	<TR><TD colspan="6" align="right">总金额（人民币）：</TD><TD align="center"><?php echo sprintf("%.2f",($te)); ?></TD><TD align="center"><?php echo sprintf("%.2f",($tf)); ?></TD></TR>
</TFOOT>
</TABLE>
<TABLE class="aaa" width="95%" align="center" cellpadding="1">
<TBODY style="font-size:12px">
	<TR><TD width="15%" style="vertical-align:top;text-align:left">备注：<br><br><br><br><br><br><br></TD><TD colspan="2" style="vertical-align:top;text-align:left">1.以上报价含16%增值税。<br>2.交货期限:<?php echo $result[0]['ppo_delivery_time']; ?>。<br>3.付款方式：	<?php
			if ($data['supplier_dayNet']==0) {
				echo "预付100%";
			} else {
				echo "月结".$data['supplier_dayNet']."天。";
			};
	?><br>4.送货方式：<?php echo $result[0]['byway']; ?><br>5.本合同传真件经双方签署或盖章与原件同等有效。</TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD></TD><TD></TD><TD></TD></TR>
	<TR><TD width="15%" style="text-align:left">厂商回签：</TD><TD width="35%" style="text-align:right">核准：</TD><TD style="text-align:right">制表人：陈斐</TD></TR>
</TBODY>
</TABLE>
</body>
</html>