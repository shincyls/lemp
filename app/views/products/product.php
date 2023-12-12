<?php
  $table = "appuser_info";
  include("./connect.php");
  include 'app/views/layouts/home_header.php';
  include 'app/views/layouts/home_navbar.php';
  $sql = "SELECT username,email,phone,fullname,signed_in,uuid FROM appuser_info WHERE uuid='$appuser' LIMIT 1;";
  $user = mysqli_fetch_all(mysqli_query($con,$sql))[0];
?>

<div class="container-fluid py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header pb-0">
          <div class="d-flex align-items-center">
            <h4 class="text-primary text-gradient"><b>Edit Profile</b></h4>
            <button class="btn btn-primary btn-sm ms-auto">Settings</button>
          </div>
        </div>
        <div class="card-body">
          <p class="text-uppercase text-sm">Appuser Information</p>
          <form class="app-controller">
            <div class="row">
              <input type="hidden" name="action" value="admin_change_profile"/>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Fullname</label>
                  <input class="form-control" name="fullname" type="text" value="<?php echo $admin[6]; ?>" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Username</label>
                  <input class="form-control" name="username" type="text" value="<?php echo $admin[0]; ?>" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Email address</label>
                  <input class="form-control" name="email" type="email" value="<?php echo $admin[1]; ?>" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Phone Number</label>
                  <input class="form-control" name="phone" type="text" value="<?php echo $admin[2]; ?>" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Company Name</label>
                  <input class="form-control" name="company" type="text" value="<?php echo $admin[3]; ?>" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
            </div>
            <div class="ms-auto text-end"><button class="btn bg-gradient-primary btn-rounded" onclick="submitAction()">Update</button></div>
          </form>
          <hr class="horizontal dark">
          <h4 class="text-primary text-gradient"><b>Change Password</b></h4>
          <form class="app-controller">
            <div class="row"> 
              <input type="hidden" name="action" value="admin_change_password"/>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Current Password</label>
                  <input class="form-control" name="pwdnow" type="password" value="" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Current Password Confirm</label>
                  <input class="form-control" name="pwdnowcon" type="password" value="" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">New Password</label>
                  <input class="form-control" name="pwdnew" type="password" value="" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">New Password Confirm</label>
                  <input class="form-control" name="pwdnewcon" type="password" value="" onfocus="focused(this)" onfocusout="defocused(this)">
                </div>
              </div>
            </div>
            <div class="ms-auto text-end"><button class="btn bg-gradient-primary btn-rounded" onclick="submitAction()">Update</button></div>
          </form>
          
          <hr class="horizontal dark">
          <p class="text-uppercase text-sm">Last Signed-In</p>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <!-- <label for="example-text-input" class="form-control-label">About me</label> -->
                <input class="form-control" type="text" value="<?php echo $admin[4]; ?>" onfocus="focused(this)" onfocusout="defocused(this)" disabled>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>

<?php include 'app/views/layouts/home_footer.php'; ?>

<script>
function submitAction(){
  event.preventDefault();
  const form = event.target.parentNode.parentNode;
  const formData = new FormData(form);
  fetch('/ctrl/admin', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    notifyJS(data);
  });
}
</script>