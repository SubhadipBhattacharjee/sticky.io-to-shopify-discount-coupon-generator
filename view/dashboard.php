<?php 

   $servername = "";
   $username = "";
   $password = "";
   $dbname = "";

   //Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);

   // Check connection
   //if ($conn->connect_error) {
   //  die("Connection failed: " . $conn->connect_error);
   //} else {
   //  echo "Connected successfully";
   //}

  $sql= "SELECT a.shopify_cust_id as cust_id,a.price_rule_id, a.discount_code as   code, b.user_email as email,c.f_name,c.l_name from shopify_price_rules a   LEFT JOIN shopify_created_user b ON a.shopify_cust_id=b.customer_id 
        LEFT JOIN rebuild_orders_details c ON  b.user_email = c.email 
        WHERE a.disc_code_status = 1";
  $users = $conn->query($sql);
  $data = $users->fetch_all(MYSQLI_ASSOC);
  //print_r($data);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Admin Dashboard DataTable</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

  <style>
    @media (max-width: 767px) {
        #adminDashboard {
            font-size: 14px;
        }
        .dataTables_length, .dataTables_info {
            margin-bottom: 10px;
        }
    }
    table.table-bordered.dataTable td.dtr-control {
        border-left-width: 1px;
    }
  </style>
</head>
<body>

<div class="container mt-2">
  <h2 class="text-md-start text-center">Admin Dashboard</h2>
  <table id="adminDashboard" class="table table-bordered dt-responsive" style="width:100%">
    <thead>
      <tr class="table-dark">
        <th class="d-none d-sm-table-cell">No.</th>
        <th>Name</th>
        <th>Email</th>
        <th>Coupon Code</th>
      </tr>
    </thead>
    <tbody>
        <?php foreach($data as $k=>$d){ ?>
        <tr>
          <td class="d-none d-sm-table-cell"><?php echo $k+1;?></td>
          <td><?php echo $d['f_name'].' '.$d['l_name'];?></td>
          <td><?php echo $d['email']; ?></td>
          <td><?php echo $d['code']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
  </table>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    $('#adminDashboard').DataTable({
      responsive: true
    });
  });
</script>

</body>
</html>
