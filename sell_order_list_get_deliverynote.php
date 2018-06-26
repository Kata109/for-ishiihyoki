<?php
		$servername = "localhost";
		$username = "root";
		$password = "66329525";
		$dbname = "ihcdata";
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("SET NAMES utf8");
		//查询表头数据
		$stmt = $conn->prepare("SELECT customer,ihc_address,ihc_dayNet,ihc_contacts,ihc_tel FROM deliverynote_name");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		//查询表中数据
		$stmt = $conn->prepare("SELECT id,po,item,ihc_type,ihc_code,qty,unit,customer_code,price FROM deliverynote_value");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//设置时间格式
		date_default_timezone_set('PRC');
		$te = "0";
		$tf = "0";
		$tg = "0";

		//人民币大写
		class Num2RmbClass{
  		public static function num2rmb($number = 0, $int_unit = '元', $is_round = TRUE, $is_extra_zero = FALSE, $dec_to_int = FALSE) {
    	// 将数字切分成两段
    	$parts = explode('.', $number, 2);
    	$int = isset($parts[0]) ? strval($parts[0]) : '0';
    	$dec = isset($parts[1]) ? strval($parts[1]) : '';
    	// 如果小数点后多于2位，不四舍五入就直接截，否则就处理
    	$dec_len = strlen($dec);
    	if (isset($parts[1]) && $dec_len > 2) {
      		if($is_round){
        		if(round(floatval("0.".$dec), 2) == 1 && $dec_to_int){//小数进位到个位
          			$int = empty($int)?1: strval($parts[0]+1);
          			$dec = 0;
        		}elseif(round(floatval("0.".$dec), 2) == 1){//小数不进位到个位
          			$dec = "99";
        		}else{
          			$dec = substr(strrchr(strval(round(floatval("0.".$dec), 2)), '.'), 1);
          			echo $dec;die('boss');
        		}
      		}else{
        		$dec = substr($parts[1], 0, 2);
      		}
    	}
    	// 当number为0.001时，小数点后的金额为0元
    	if (empty($int) && empty($dec)) {
      		return '零';
    	}
 
    	// 定义
    	$chs = array('0','壹','贰','叁','肆','伍','陆','柒','捌','玖');
    	$uni = array('','拾','佰','仟');
    	$dec_uni = array('角', '分');
    	$exp = array('', '万');
    	$res = '';
 
    	// 整数部分从右向左找
    	for ($i = strlen($int) - 1, $k = 0; $i >= 0; $k++) {
      		$str = '';
      		// 按照中文读写习惯，每4个字为一段进行转化，i一直在减
      		for ($j = 0; $j < 4 && $i >= 0; $j++, $i--) {
        		$u = $int{$i} > 0 ? $uni[$j] : ''; // 非0的数字后面添加单位
        		$str = $chs[$int{$i}] . $u . $str;
      		}
      		//echo $str."|".($k - 2)."<br>";
      		$str = rtrim($str, '0');// 去掉末尾的0
      		$str = preg_replace("/0+/", "零", $str); // 替换多个连续的0
      		if (!isset($exp[$k])) {
        		$exp[$k] = $exp[$k - 2] . '亿'; // 构建单位
      		}
      		$u2 = $str != '' ? $exp[$k] : '';
      		$res = $str . $u2 . $res;
    	}
 
    	// 如果小数部分处理完之后是00，需要处理下
    	$dec = rtrim($dec, '0');
 
    	// 小数部分从左向右找
    	if (!empty($dec)) {
      		$res .= $int_unit;
      	// 是否要在整数部分以0结尾的数字后附加0，有的系统有这要求
      		if ($is_extra_zero) {
        		if (substr($int, -1) === '0') {
          		$res.= '零';
        		}
      		}
      		for ($i = 0, $cnt = strlen($dec); $i < $cnt; $i++) {
        		$u = $dec{$i} > 0 ? $dec_uni[$i] : ''; // 非0的数字后面添加单位
        		$res .= $chs[$dec{$i}] . $u;
      		}
      		$res = rtrim($res, '0');// 去掉末尾的0
      		$res = preg_replace("/0+/", "零", $res); // 替换多个连续的0
    	} else {
      		$res .= $int_unit . '整';
    	}
    	return $number < 0 ? "(负)".$res : $res;
  		}
 
	}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo date("YmdHis"); ?><?php echo $data['customer']; ?></title>
	<script src="http://cdn.static.runoob.com/libs/jquery/1.10.2/jquery.min.js"></script>
	<style>
	@media print{
		INPUT {display:none}
	}
	table.bbb {
    	font-family: Arial,Helvetica,sans-serif;
		font-size: 10px;
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
	<TABLE class="aaa" width="100%" align="center" cellpadding="1">
	<THEAD style="display:table-header-group;font-weight:bold">
		<TR><Td colspan="4" rowspan="1" align="center"><img src="logo.png"  alt="logo" style="width:180px" /><br><h3>石井表记精密机械（苏州）有限公司<br>送货单</h3></Td></TR>
		<TR><TD colspan="2" ></TD><TD><h5>NO:<?php echo date("YmdHis"); ?></h5></TD></TR>
	</THEAD>
	<TBODY style="text-align:left;font-size:10px">
		<TR><TD width="15%">客户名称 Customer：</TD><TD width="60%"><?php echo $data['customer']; ?></TD><TD width="10%">日期 Date：</TD><TD width="15%"><?php echo date("Y-m-d"); ?></TD></TR>
		<TR><TD width="15%">客户地址 Add：</TD><TD width="60%"><?php echo $data['ihc_address']; ?></TD><TD width="10%">联系人：</TD><TD width="15%"><?php echo $data['ihc_contacts']; ?></TD></TR>
		<TR><TD width="15%">客付款方式 Terms：</TD><TD width="60%"><?php
		if ($data['ihc_dayNet']==0){
			echo "预付";
		} else {
			echo "月结".$data['ihc_dayNet']."天";
		}
		?></TD><TD width="10%">联系电话 Tel：</TD><TD width="15%"><?php echo $data['ihc_tel']; ?></TD></TR>
		</TBODY>
	</TABLE>
	<TABLE class="bbb" width="100%" align="center">
	<THEAD style="display:table-header-group;font-weight:bold">
		<TR><Th width="6%">序号</Th><Th width="12%">物料编号</Th><Th width="12%">商品名称<br>Description</Th><Th width="16%">型号<br>Type</Th><Th width="10%">品目番号</Th><Th width="6%">数量<br>Qty</Th><Th width="6%">单位<br>Unit</Th><Th width="8%">未税单价<br>Unit Price</Th><Th width="8%">金额<br>Total</Th><Th width="8%">增值税</Th><Th width="8%">金额<br>Total</Th></TR>
	</THEAD>
	<TBODY style="text-align:center">
	<?php
		for ($i=0;$i<count($result);$i++) {
			$ta = sprintf("%.2f",($result[$i]['price']*$result[$i]['qty']));
			$tb = sprintf("%.2f",($ta*0.16));
			$tc = sprintf("%.2f",($ta+$tb));

  			echo "<tr><td>".$result[$i]['id']."</td><td>".$result[$i]['po']."<br>".$result[$i]['customer_code']."</td><td>".$result[$i]['item']."</td><td>".$result[$i]['ihc_type']."</td><td>".$result[$i]['ihc_code']."</td><td>".$result[$i]['qty']."</td><td>".$result[$i]['unit']."</td><td>".$result[$i]['price']."</td><td>".$ta."</td><td>".$tb."</td><td>".$tc."</td></tr>";
  			$te += $ta;
  			$tf += $tb;
  			$tg += $tc;
		}
	?>
	</TBODY>
	<TFOOT style="display:table-footer-group;font-weight:bold">
		<TR><TD colspan="8" align="right">TOTAL：</TD><TD align="center"><?php echo sprintf("%.2f",$te); ?></TD><TD align="center"><?php echo sprintf("%.2f",$tf); ?></TD><TD align="center"><?php echo sprintf("%.2f",$tg); ?></TD></TR>
	</TFOOT>
	</TABLE>
	<TABLE class="aaa" width="100%" align="center" cellpadding="1">
	<TBODY style="font-size:10px">
		<TR><TD width="5%" style="vertical-align:top;text-align:left"><br><br><br><br><br><br><br></TD><TD style="vertical-align:top;text-align:left">合计人民币（大写）：<?php echo (Num2RmbClass::num2rmb(sprintf("%.2f",$tg),'圆',false,false,false)); ?></TD></TR>
		<TR><TD width="15%" style="text-align:left">经办人：</TD><TD width="35%" style="text-align:right">签收人：</TD><TD style="text-align:right">签收日期：_____年_____月_____日</TD></TR>
	</TBODY>
	</TABLE>
</body>
</html>