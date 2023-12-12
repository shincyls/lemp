<?php
  $table = "admin_info";
  include '../app/views/layouts/home_header.php';
  include '../app/views/layouts/home_navbar.php';
?>

<div class="container-fluid h-100">
  <div class="row">
    <div class="col-12 text-center">
      <a class="btn btn-sm btn-primary" href="/customer/<?php echo $customer; ?>/invoice"><b>Invoice</b></a>
      <a class="btn btn-sm btn-primary" href="/customer/<?php echo $customer; ?>/account"><b>Bank Account</b></a>
      <a class="btn btn-sm btn-primary" href="/customer/<?php echo $customer; ?>/statement"><b>Debtor Statement</b></a>
      <a class="btn btn-sm btn-primary" href="/customer/<?php echo $customer; ?>/report"><b>Report</b></a>
    </div>
  </div>
</div>

<div class="container-fluid py-4 h-100">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Admin Management</h6>
          <button type="button" class="btn btn-sm bg-gradient-primary" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_create">
            + New Admin
          </button>
          <form id="<?php echo $table; ?>_search">
            <div class="row my-2">
              <input type="hidden" name="action" value="<?php echo $table; ?>_search"/>
              <input type="hidden" name="table" value="<?php echo $table; ?>_table"/>
              <div class="col-sm-6 col-md-3 my-1"><input type="text" class="form-control" name="fullname" placeholder="Admin Name" value=""/></div>
              <div class="col-sm-6 col-md-3 my-1"><input type="text" class="form-control" name="email" placeholder="Email" value=""/></div>
              <div class="col-sm-6 col-md-3 my-1"><input type="text" class="form-control" name="phone" placeholder="Phone Number" value=""/></div>
              <div class="col-sm-6 col-md-3 my-1">
                <select class="form-control" name="status">
                  <option value="0" selected>Active</option>
                  <option value="10">Suspended</option>
                  <option value="99">Removed</option>
                </select>
            </div>
            </div>
            <div class="row d-flex">
              <div class="col-sm-12 col-md-2 my-1">
                <button class="btn btn-sm bg-gradient-primary w-100" type="submit" onclick="submitSearch()">Search <span id="spinner"><i class="fa fa-sync"></i><span></button>
              </div>
              <div class="col-sm-12 col-md-3 my-1">
                <div class="form-check form-switch">
                  <div class="input-group border">
                    <span class="input-group-text border-0"><i id="spinnerIcon" class="fa fa-sync"></i></span>
                    <select id="interval"class="form-control form-control-sm border-0">
                      <option value="10" selected>10 seconds</option>
                      <option value="60">1 minute</option>
                      <option value="300">5 minutes</option>
                      <option value="600">10 minutes</option>
                    </select>
                    <span class="input-group-text border-0 mx-2"></span>
                    <span class="input-group-text border-0">
                      <input id="autoPerform" class="form-check-input border-0" type="checkbox">
                    </span>
                  </div>
                </div>
            </div>
          <form>
          </div>
        </div>
        <div class="card-body">
          <table id="<?php echo $table; ?>_table" class="table table-rounded table-striped table-hover align-items-center" style="font-family: 'Hind Siliguri', sans-serif;">
            <thead class="text-white text-uppercase bg-gradient-primary"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Signed In</th><th></th></tr></thead>
            <tbody class="font-weight-bold"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../app/views/layouts/home_footer.php'; ?>

<script>
function autoClick(){
  submitSearch(false);
}
function submitSearch(human=true){
    if(human){
      event.preventDefault();
    }
    document.getElementById('spinner').innerHTML = '<i class="fa fa-sync fa-spin"></i>';
    const form = document.getElementById('<?php echo $table; ?>_search');
    const formData = new FormData(form);
    const formDataObject = {};
    for (const [key, value] of formData.entries()) {
      formDataObject[key] = value;
    }
    $('#'+formData.get('table')).DataTable().clear().destroy();
    $('#'+formData.get('table')).DataTable({
      ajax: {
        "url":'/ctrl/admin',
        "type":'POST',
        "data":formDataObject
      },
      fnCreatedRow: function( row, order, index ) {
        $(row).attr('id', order[0]);
      },
      columns: [{data:1},{data:2},{data:3},{data:4},{data:5},{
                    data: null,
                    defaultContent: `<button type="button" class="btn btn-info btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_update"><i class="fas fa-pen"></i></button>
                    <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_delete"><i class="fas fa-trash"></i></button>`,
                    orderable: false
                }],
      searching: false, // FrontEnd quick search for small size data, add forms and sql params in BackEnd controllers for backend search
      lengthChange: false,
      info: false,
      order: [0,'desc']
    });
    document.getElementById('spinner').innerHTML = '<i class="fa fa-sync"></i>';
}

function submitAction(){
  event.preventDefault();
  const form = document.querySelector('.app-controller');
  const formData = new FormData(form);
  fetch('/ctrl/admin', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if(data.status=='success'){
      switch (data.action) {
        case '<?php echo $table; ?>_create':
          prependTableRow("<?php echo $table; ?>_table",data.id,data.data);
        break;
        case '<?php echo $table; ?>_update':
          updateTableRow(data.id, data.data);
        break;
        case '<?php echo $table; ?>_delete':
          deleteTableRow(data.id);
        break;
        default:
      }
      $('#modal-action').modal('hide');
      notifyJS(data);
    } else{
      notifyJS(data);
    }
  });
}
// Allow Periodically Reload?
document.getElementById('autoPerform').addEventListener('change', toggleAutoPerform);
// Initial Load Data?
document.addEventListener('DOMContentLoaded', () => {
  submitSeach();
});

</script>

