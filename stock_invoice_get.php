<?php
$servername = "localhost";
$username = "root";
$password = "66329525";
$dbname = "ihcdata";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->query("SET NAMES utf8");
    //查询已出库订单记录
    $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice_p"); 
    $stmt->execute();
    $p_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    for ($i=0; $i < count($p_array); $i++) {
        $po = $p_array[$i]['po'];
        $customer_code = $p_array[$i]['customer_code'];
        $id = $p_array[$i]['id'];
        //查询该PO的出库数量
        $stmt = $conn->prepare("SELECT ihc_code,qty FROM ihc_order WHERE po='$po' AND customer_code='$customer_code'"); 
        $stmt->execute();
        $order_array = $stmt->fetch(PDO::FETCH_ASSOC);
        $po_ma = $order_array['ihc_code'];
        $po_qty = $order_array['qty'];
        //查询有没有发注
        $stmt = $conn->prepare("SELECT * FROM ihc_ppo WHERE po='$po' AND customer_code='$customer_code'");
        $stmt->execute();
        $ppo_array = $stmt->fetch(PDO::FETCH_ASSOC);
        $ppo_nameA = $ppo_array['nameA'];
        $ppo_customer = $ppo_array['customer'];
        $ppo_user_time = $ppo_array['user_time'];
        $rowC = $stmt->rowCount();
        if($rowC==0){
            //无发注
            //判断有没有正好数量相同的库存订单
            $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice WHERE ma='$po_ma' AND instock='$po_qty' AND po='库存'"); 
            $stmt->execute();
            $rowC = $stmt->rowCount();
            if($rowC==0){
                //判断库存总量是不是大于或等于订单数量
                $stmt = $conn->prepare("SELECT ma, SUM(instock) as instock_count FROM  ihc_stock_invoice WHERE ma='$po_ma' GROUP BY ma"); 
                $stmt->execute();
                $count_array = $stmt->fetch(PDO::FETCH_ASSOC);
                $instock_count = $count_array['instock_count'];
                if ($instock_count>=$po_qty) {
                    //升序排列数量，并数量++循环，如大于应出库数量则停止循环，更新数量（留最后一条记录invoice），并记录invoice到ihc_ppo
                    //如相加不满足数量，则停止出库
                    //SELECT * FROM ihc_stock_invoice ORDER BY instock+0 DESC
                    //注：+0为把varchar转int类型
                    $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice WHERE ma='$po_ma' AND po='库存' ORDER BY instock+0 ASC");
                    $stmt->execute();
                    $stock_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $difflog = 0;
                    for ($s=0; $s < count($stock_array); $s++) {
                        $instock = $stock_array[$s]['instock'] + $difflog;
                        $poid = $stock_array[$s]['poid'];
                        $diff = $instock - $po_qty;
                        if ($diff<0) {
                            $difflog .= $instock;
                            $stmt = $conn->prepare("UPDATE ihc_ppo SET po='$po',customer_code='$customer_code' WHERE id='$poid'");
                            $stmt->execute();
                            $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE poid='$poid'"); 
                            $stmt->execute();
                        } else {
                            if ($diff==0) {
                                $stmt = $conn->prepare("UPDATE ihc_ppo SET po='$po',customer_code='$customer_code' WHERE id='$poid'");
                                $stmt->execute();
                                $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE poid='$poid'"); 
                                $stmt->execute();
                            } else {
                                $pq = $stock_array[$s]['instock'] - $diff;
                                //更新发注订单的受注号和客户单号，并修改qty为$diff
                                //INSERT INTO ihc_ppo(ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,po,customer_code,customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway) SELECT ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,'$diff',nameA,'库存','库存',customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway FROM ihc_ppo WHERE id='$poid';
                                $stmt = $conn->prepare("INSERT INTO ihc_ppo(ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,po,customer_code,customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway) SELECT ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,'$diff',nameA,'库存','库存',customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway FROM ihc_ppo WHERE id='$poid'"); 
                                $stmt->execute();
                                $stmt = $conn->prepare("UPDATE ihc_ppo SET qty='$pq',po='$po',customer_code='$customer_code' WHERE id='$poid'"); 
                                $stmt->execute();
                                //查询新poid
                                $stmt = $conn->prepare("SELECT ppo_number FROM ihc_ppo WHERE id='$poid'"); 
                                $stmt->execute();
                                $ponum_array = $stmt->fetch(PDO::FETCH_ASSOC);
                                $ponum = $ponum_array['ppo_number'];
                                $stmt = $conn->prepare("SELECT id FROM ihc_ppo WHERE ppo_number='$ponum' AND po='库存' AND customer_code='库存'"); 
                                $stmt->execute();
                                $poid_array = $stmt->fetch(PDO::FETCH_ASSOC);
                                $npoid = $poid_array['id'];
                                //更新instock和poid
                                $stmt = $conn->prepare("UPDATE ihc_stock_invoice SET instock='$diff',poid='$npoid' WHERE poid='$poid'"); 
                                $stmt->execute();   
                            };
                            $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice_p WHERE id='$id'"); 
                            $stmt->execute();
                            break;
                        };
                    };
                };
            } else {
                //直接出库，删除第一条相符合记录，并记录invoice到ihc_ppo
                $s_array = $stmt->fetch(PDO::FETCH_ASSOC);
                $poid = $s_array['poid'];
                $stmt = $conn->prepare("UPDATE ihc_ppo SET po='$po',customer_code='$customer_code' WHERE id='$poid'"); 
                $stmt->execute();
                $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE poid='$poid'"); 
                $stmt->execute();
                $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice_p WHERE id='$id'"); 
                $stmt->execute();    
            };
        } else {
            //有发注
            $stmt = $conn->prepare("SELECT * FROM ihc_ppo WHERE po='$po' AND customer_code='$customer_code' AND ppo_storage_time='2000-01-01'"); 
            $stmt->execute();
            $rowC = $stmt->rowCount();
            if($rowC==0){
                //实物已入，等收到发票后，直接出库
                $stmt = $conn->prepare("SELECT * FROM ihc_ppo WHERE po='$po' AND customer_code='$customer_code' AND ppo_invoice='2000-01-01'"); 
                $stmt->execute();
                $rowC = $stmt->rowCount();
                if($rowC==0){
                    $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE po='$po'"); 
                    $stmt->execute();
                    $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice_p WHERE po='$po'"); 
                    $stmt->execute();  
                };  
            }else{
                //实物未入，判断库存是否有相等的订单
                //判断有没有正好数量相同的库存订单
                $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice WHERE ma='$po_ma' AND instock='$po_qty' AND po='库存'"); 
                $stmt->execute();
                $rowC = $stmt->rowCount();
                if($rowC==0){
                    //判断库存总量是不是大于或等于订单数量
                    $stmt = $conn->prepare("SELECT ma, SUM(instock) as instock_count FROM  ihc_stock_invoice WHERE ma='$po_ma' GROUP BY ma"); 
                    $stmt->execute();
                    $count_array = $stmt->fetch(PDO::FETCH_ASSOC);
                    $instock_count = $count_array['instock_count'];
                    if ($instock_count>=$po_qty) {
                        //升序排列数量，并数量++循环，如大于应出库数量则停止循环，更新数量（留最后一条记录invoice），并记录invoice到ihc_ppo
                        //如相加不满足数量，则停止出库
                        //SELECT * FROM ihc_stock_invoice ORDER BY instock+0 DESC
                        //注：+0为把varchar转int类型
                        $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice WHERE ma='$po_ma' AND po='库存' ORDER BY instock+0 ASC");
                        $stmt->execute();
                        $stock_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $difflog = 0;
                        for ($s=0; $s < count($stock_array); $s++) {
                            $instock = $stock_array[$s]['instock'] + $difflog;
                            $poid = $stock_array[$s]['poid'];
                            $diff = $instock - $po_qty;
                            if ($diff<0) {
                                $difflog .= $instock;
                                $stmt = $conn->prepare("UPDATE ihc_ppo SET po='$po',customer_code='$customer_code' WHERE id='$poid'");
                                $stmt->execute();
                                $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE poid='$poid'"); 
                                $stmt->execute();
                            } else {
                                //更新原订单为库存单
                                $stmt = $conn->prepare("UPDATE ihc_ppo SET nameA='IHC',po='库存',customer_code='库存',customer='IHC',user_time='库存' WHERE po='$po' AND customer_code='$customer_code'"); 
                                $stmt->execute();
                                if ($diff==0) {
                                    $stmt = $conn->prepare("UPDATE ihc_ppo SET nameA='$nameA',po='$po',customer_code='$customer_code',customer='$customer',user_time='$user_time' WHERE id='$poid'"); 
                                    $stmt->execute();
                                    $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE poid='$poid'"); 
                                    $stmt->execute();
                                } else {
                                    $pq = $stock_array[$s]['instock'] - $diff;
                                    //更新发注订单的受注号和客户单号，并修改qty为$diff
                                    //INSERT INTO ihc_ppo(ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,po,customer_code,customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway) SELECT ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,'$diff',nameA,'库存','库存',customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway FROM ihc_ppo WHERE id='$poid';
                                    $stmt = $conn->prepare("INSERT INTO ihc_ppo(ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,qty,nameA,po,customer_code,customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway) SELECT ppo_number,ppo_time,ppo_delivery_time,supplier,ma,nameC,nameJ,type,unit,price,'$diff',nameA,'库存','库存',customer,user_time,ppo_storage_time,ppo_invoice,ppo_invoice_time,payment,byway FROM ihc_ppo WHERE id='$poid'"); 
                                    $stmt->execute();
                                    $stmt = $conn->prepare("UPDATE ihc_ppo SET qty='$pq',nameA='$ppo_nameA',po='$po',customer_code='$customer_code',customer='$ppo_customer',user_time='$ppo_user_time' WHERE id='$poid'"); 
                                    $stmt->execute();
                                    //查询新poid
                                    $stmt = $conn->prepare("SELECT ppo_number FROM ihc_ppo WHERE id='$poid'"); 
                                    $stmt->execute();
                                    $ponum_array = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $ponum = $ponum_array['ppo_number'];
                                    $stmt = $conn->prepare("SELECT id FROM ihc_ppo WHERE ppo_number='$ponum' AND po='库存' AND customer_code='库存'"); 
                                    $stmt->execute();
                                    $poid_array = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $npoid = $poid_array['id'];
                                    //更新instock和poid
                                    $stmt = $conn->prepare("UPDATE ihc_stock_invoice SET instock='$diff',poid='$npoid' WHERE poid='$poid'"); 
                                    $stmt->execute();   
                                };
                                $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice_p WHERE id='$id'"); 
                                $stmt->execute();
                                break;
                            };
                        };
                    };
                } else {
                    //直接出库，删除第一条相符合记录，并记录invoice到ihc_ppo
                    $s_array = $stmt->fetch(PDO::FETCH_ASSOC);
                    $poid = $s_array['poid'];
                    //更新原订单为库存单
                    $stmt = $conn->prepare("UPDATE ihc_ppo SET nameA='IHC',po='库存',customer_code='库存',customer='IHC',user_time='库存' WHERE po='$po' AND customer_code='$customer_code'"); 
                    $stmt->execute();
                    $stmt = $conn->prepare("UPDATE ihc_ppo SET nameA='$ppo_nameA',po='$po',customer_code='$customer_code',customer='$ppo_customer',user_time='$ppo_user_time' WHERE id='$poid'"); 
                    $stmt->execute();
                    $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice WHERE poid='$poid'"); 
                    $stmt->execute();
                    $stmt = $conn->prepare("DELETE FROM ihc_stock_invoice_p WHERE id='$id'"); 
                    $stmt->execute();    
                };
            };
        };
    };
    $stmt = $conn->prepare("SELECT * FROM ihc_stock_invoice"); 
    $stmt->execute();
    // 设置结果集为关联数组
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //受注页面显示
    for ($i=0;$i<count($result);$i++) {
        echo "<tr class=\"text-c\">";
        echo "<td>".$result[$i]['ma']."</td><td>".$result[$i]['nameC']."</td><td>".$result[$i]['nameJ']."</td><td>".$result[$i]['type']."</td><td>".$result[$i]['instock']."</td><td>".$result[$i]['po']."</td></tr>";
    };
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>