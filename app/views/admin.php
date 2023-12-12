<?php
  include 'home_header.php';
?>

    <div class="header bg-primary pb-6">
      <div class="container-fluid">
        <div class="row">
          <div class="col-2">Admin</div>
          <div class="col-2"><a href="/admin" class="btn"><b>Accounts</b><a></div>
          <div class="col-2"><a href="/permission" class="btn"><b>Permissions</b><a></div>
          <div class="col-2"><a href="/log" class="btn"><b>Logs</b><a></div>
        </div>
        <div class="header-body">
          <div class="row align-items-center ml-3 py-4">
            <h2 class="text-white text-uppercase ls-1 mb-1">Admin Settings</h2>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid mt--6">
      <div class="row">
        <div class="col nav-wrapper">
          <div class="card shadow">
            <div class="card-body">

              <ul class="nav nav-pills nav-fill flex-column flex-md-row mb-4" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="admin-tab" data-toggle="tab" href="#admin" role="tab" aria-controls="admin" aria-selected="true"><b>Admin</b></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="permission-tab" data-toggle="tab" href="#permission" role="tab" aria-controls="web" aria-selected="true"><b>Permission</b></a>
                </li>
              </ul>

              <div class="tab-content" id="myTabContent">
                
                <div class="tab-pane active" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                  <div class="row">
                    <div class="col-12 rounded shadow-sm" style="background-color:whitesmoke;">
                      <form id="admin-search">
                        <div class="row my-2">
                          <input type="hidden" name="action" value="admin_search"/>
                          <input type="hidden" name="table" value="admin-table"/>
                          <div class="col-3"><input type="text" class="form-control form-control-sm" placeholder="Username" name="username" value=""/></div>
                          <div class="col-3"><input type="text" class="form-control form-control-sm" placeholder="Email" name="email" value=""/></div>
                          <div class="col-3"><input type="text" class="form-control form-control-sm" placeholder="Company" name="company" value=""/></div>
                        </div>
                        <div class="row d-flex justify-content-center my-2">
                          <div class="col-sm-12 col-md-4"><button class="btn btn-sm btn-primary w-100" type="submit">Search <span id="spinner"><i class="fa fa-sync"></i><span></button></div>
                        </div>
                      <form>
                    </div>
                    <div class="col-12">
                      <table id="admin-table" class="table table-sm w-100">
                        <thead>
                          <tr>
                              <th>ID</th>
                              <th>Username</th>
                              <th>Email</th>
                              <th>Company</th>
                              <th>Created On</th>
                              <th>Locked</th>
                              <th>Action</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="permission" role="tabpanel" aria-labelledby="permission-tab">
                  <table id="permission-table" class="w-100"></table>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

<?php
  include 'home_footer.php';
?>

<script>

$(document).on('submit', "#admin-search", function(event) {
  event.preventDefault();
  var form_data = new FormData($(this)[0]);
  $('#spinner').html('<i class="fa fa-sync fa-spin"></i>');
  $('#'+form_data.get('table')).DataTable().destroy();
  $.ajax({
      url: "/controller/admin",
      type: "post",
      data: form_data,
      processData: false,
      contentType: false
  }).done(function (response){
    var res = JSON.parse(response);
    console.log(res);
    $('#'+form_data.get('table')).DataTable({
      'createdRow': function(row, data, dataIndex) {
        $(row).prop('id', 'row-'+data[0]);
      },
      data: res.data,
      columnDefs: [{targets: -1, data: null, sorting: false, render: function(data, type, row, meta) {
        return `<button class="btn btn-sm btn-alt text-info app-modal m-0" action="admin_user_edit"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-alt text-success app-modal m-0" action="admin_reset_password"><i class="fas fa-key"></i></button>
                <button class="btn btn-sm btn-alt text-danger app-modal m-0" action="admin_user_delete"><i class="fas fa-trash"></i></button>`;
      }}],
      searching: false,
      lengthChange: false
    });
  }).always(function (){
    $('#spinner').html('<i class="fa fa-sync"></i>');
  }).fail(function (){
    alert('Connection Error');
  });
});
$('#admin-search').submit();

$(document).on('submit', ".app-controller", function(event) {
  event.preventDefault();
  var curl = "/controller/admin";
  var ajaxRequest;
  var form_data = new FormData($(this)[0]);
  var proceed = true;

  if(proceed){
      ajaxRequest= $.ajax({
          url: curl,
          type: "post",
          data: form_data,
          processData: false,
          contentType: false
      });
      ajaxRequest.done(function (get){
          var res = JSON.parse(get);
          console.log(res);
          if(res.status=='success'){
              if(res['action']=='admin_user_edit'){
                  $('.modal').modal('hide');
                  updateTableRow(res.id, res.data);
                  flashNotification('success', 'User Admin Is Updated');
              }
              else if(res['action']=='admin_user_delete'){
                  $('.modal').modal('hide');
                  updateTableRow(res.id, res.data);
                  flashNotification('success', 'User Admin Is Removed');
              }
              else if(res['action']=='admin_reset_password'){
                  $('.modal').modal('hide');
                  updateTableRow(res.id, res.data);
                  flashNotification('success', 'Admin User Password Has Reset');
              }
              else{
                  $('.modal').modal('hide');
                  flashNotification('success', 'Update Success!');
              }
          }
          else if(res.status=='danger'){
            flashNotification(res.status, res.message);
          }
          else{
            flashNotification(res.status, res.message);
            // console.log('app-controller: fail');
          }
      });
      ajaxRequest.always(function (){
          // Button Change / Loading Icon
      });
      ajaxRequest.fail(function (){
          console.log('Connection Error');
      });
  }
});

</script>


