<?php
  $table = "appuser_info";
  include("./connect.php");
  include 'app/views/layouts/home_header.php';
  include 'app/views/layouts/home_navbar.php';
  $sql = "SELECT username,email,phone,fullname,signed_in,uuid,balcredit,usr_credited,usr_debited FROM appuser_info WHERE uuid='$appuser' LIMIT 1;";
  $user = mysqli_fetch_all(mysqli_query($con,$sql))[0];
  if(empty($user)){
    header('Location: /404');
  }
?>

<div class="container-fluid py-4">
  
  <div class="row">
    <div class="col-lg-8">
      <div class="row">
        <div class="col-md-12 mb-lg-0 mb-2">
          <div class="card">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-6">
                  <span class="text-primary font-weight-bold">&nbsp;&nbsp;Search Customer</span>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
              <form class="app-controller">
                <div class="row">
                  <input type="hidden" name="uuid" class="form-control form-control-sm" value="<?php echo $appuser; ?>"/>
                  <div class="col-md-6">
                    <label>Order ID</label>
                    <input type="text" name="order" class="form-control form-control-sm" value=""/>
                  </div>
                  <div class="col-md-6">
                    <label>Description</label>
                    <input type="text" name="desc" class="form-control form-control-sm" value=""/>
                  </div>
                  <div class="col-md-6">
                    <label>Start Date</label>
                    <input type="date" name="datefrom" class="datepicker form-control form-control-sm" value=""/>
                  </div>
                  <div class="col-md-6">
                    <label>End Date</label>
                    <input type="date" name="dateend" class="datepicker form-control form-control-sm" value=""/>
                  </div>
                  <div class="col-md-12 mt-4 text-end">
                    <button class="btn btn-sm bg-gradient-primary" onclick="submitAction()" action="appuser_search_transactions"><i class="fa fa-search" aria-hidden="true"></i> Find</button>
                    <button class="btn btn-sm bg-gradient-info" onclick="submitAction()" action="appuser_balance_reconcile"><i class="fa fa-balance-scale" aria-hidden="true"></i> Reconcile</button>
                    <button class="btn btn-sm bg-gradient-danger" onclick="submitAction()" action="appuser_export_pdf"><i class="fas fa-file-pdf" aria-hidden="true"></i> PDF</button>
                    <button class="btn btn-sm bg-gradient-success" onclick="submitAction()" action="appuser_export_csv"><i class="fas fa-file-csv" aria-hidden="true"></i> CSV</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-xl-6 mb-xl-0 my-4">
          <div class="card bg-transparent shadow-xl">
            <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/card-visa.jpg');">
              <span class="mask bg-gradient-success"></span>
              <div class="card-body position-relative z-index-1 p-3">
                <h5 class="text-white my-2"><?php echo $user[3]; ?></h5>
                <p class="text-white">Balance Credits: <?php echo $user[6]; ?></p>
                <table class="mb-4">
                  <tbody>
                    <tr><td class="text-white opacity-9 text-sm pe-4">Username</td><td class="text-white text-sm"><?php echo $user[0]; ?></td></tr>
                    <tr><td class="text-white opacity-9 text-sm pe-4">Email</td><td class="text-white text-sm"><?php echo $user[1]; ?></td></tr>
                    <tr><td class="text-white opacity-9 text-sm pe-4">Phone Number</td><td class="text-white text-sm"><?php echo $user[2]; ?></td></tr>
                  <tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="row">
            <div class="col-md-6 my-4">
              <div class="card">
                <div class="card-header mx-4 p-3 text-center">
                  <div class="icon icon-shape icon-lg bg-gradient-success shadow text-center border-radius-lg">
                    <i class="fas fa-landmark opacity-10"></i>
                  </div>
                </div>
                <div class="card-body pt-0 p-3 text-center">
                  <h6 class="text-center mb-0">Debited</h6>
                  <hr>
                  <h5 class="mb-2 text-success">+RM <?php echo $user[7]; ?></h5>
                </div>
              </div>
            </div>
            <div class="col-md-6 my-4">
              <div class="card">
                <div class="card-header mx-4 p-3 text-center">
                  <div class="icon icon-shape icon-lg bg-gradient-danger shadow text-center border-radius-lg">
                    <i class="fab fa-paypal opacity-10"></i>
                  </div>
                </div>
                <div class="card-body pt-0 p-3 text-center">
                  <h6 class="text-center mb-0">Credited</h6>
                  <hr>
                  <h5 class="mb-2 text-danger">+RM <?php echo $user[8]; ?></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header pb-0 p-3">
          <div class="row">
            <div class="col-6 d-flex align-items-center">
              <h6 class="mb-0">Address</h6>
            </div>
            <div class="col-6 text-end">
              <button class="btn btn-sm bg-gradient-success"><i class="ni ni-pin-3"></i> New</button>
            </div>
          </div>
        </div>
        <div class="card-body p-3 pb-0">
          <ul id="addresses" class="list-group">
          </ul>
        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-md-12 mt-4">
      <div class="card h-100 mb-4">
        <div class="card-header pb-0 px-3">
          <div class="row">
            <div class="col-md-6">
              <h6 class="mb-0">Transactions</h6>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
              <i class="far fa-calendar-alt me-2"></i>
              <small>23 - 30 March 2020</small>
            </div>
          </div>
        </div>
        <div class="card-body pt-4 p-3">
          <ul id="transactions" class="list-group">
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'app/views/layouts/home_footer.php'; ?>

<script>
let trans;
function submitAction(){
  event.preventDefault();
  function createAddresses(adres){
    let html;
    adres.forEach(adr => {
      html += `<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
              <div class="d-flex flex-column">
                <h6 class="mb-1 text-dark font-weight-bold text-sm">January, 2023</h6>
                <span class="text-xs">#MS-415646</span>
              </div>
              <div class="d-flex align-items-center text-sm">
                $180
                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
              </div>
            </li>`;
    });
    document.getElementId().appendChild();
  }
  function createTransactions(trans){
    let html;
    txns.forEach(txn => {
      html += `<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
              <div class="d-flex align-items-center">
                <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                <div class="d-flex flex-column">
                  <h6 class="mb-1 text-dark text-sm">HubSpot</h6>
                  <span class="text-xs">26 March 2020, at 12:30 PM</span>
                </div>
              </div>
              <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                + $ 1,000
              </div>
            </li>`;
    });
    document.getElementId().innerHTML = html;
  }
  const form = event.target.parentNode.parentNode;
  const formData = new FormData(form);
  fetch('/controller', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    notifyJS(data);
  });
}
</script>