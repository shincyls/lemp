<?php
  $table = "admin_info";
  include 'app/views/layouts/home_header.php';
  include 'app/views/layouts/home_navbar.php';
?>

<div class="container-fluid h-100">
  <div class="row">
    <div class="col-12 text-center">
      <a class="btn btn-sm btn-primary" href="/admin/accounts"><b>Accounts</b></a>
      <a class="btn btn-sm btn-primary" href="/admin/permissions"><b>Permission</b></a>
      <a class="btn btn-sm btn-primary" href="/admin/logs"><b>Logs</b></a>
    </div>
  </div>
</div>

<div class="container-fluid py-4 h-100">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-6">
                <h6 class="text-primary font-weight-bold">&nbsp;&nbsp;Admin Accounts</h6>
                <button  class="btn bg-gradient-primary mb-0 me-auto" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_create">
                  + New Admin
                </button>
              </div>
            </div>
          </div>
          <div class="card-body p-3">
            <form id="<?php echo $table; ?>_search">
              <div class="row">
                <input type="hidden" name="action" value="<?php echo $table; ?>_search"/>
                <input type="hidden" name="table" value="<?php echo $table; ?>_table"/>
                <div class="col-md-6">
                  <label>Name</label>
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
                  <button class="btn bg-gradient-primary m-0" onclick="submitSearch()"><i class="fa fa-search" aria-hidden="true"></i> Find</button>
                  <div class="form-check form-switch d-inline-block d-inline-block ps-0">
                    <div class="input-group border">
                      <span class="input-group-text border-0"><i id="spinnerIcon" class="fa fa-sync"></i></span>
                      <select id="interval"class="form-control border-0">
                        <option value="30" selected>30 seconds</option>
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
              </div>
            </form>
          </div>
        </div>
        <div class="card-body">
          <table id="<?php echo $table; ?>_table" class="table table-responsive table-hover w-100 align-items-center">
            <thead class="text-white text-uppercase bg-gradient-primary">
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Signed In</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="font-weight-bold"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'app/views/layouts/home_footer.php'; ?>

<script>
function submitSearch(human=true){
  if(human){
    event.preventDefault();
  }
  const button = event.target;
  const before = event.target.innerHTML;
  button.innerHTML = '<i class="fa fa-circle-notch fa-spin"></i>';
  button.disabled = true;
  const form = document.getElementById('<?php echo $table; ?>_search');
  const formData = new FormData(form);
  const formDataObject = {};
  for (const [key, value] of formData.entries()) {
    formDataObject[key] = value;
  }
  $('#'+formData.get('table')).DataTable().clear().destroy();
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (xhttp.status === 200) {
      data = JSON.parse(this.responseText);
      if(data.status=='success'){
        // Preprocessing
        // Continue
        $('#'+formData.get('table')).DataTable({
          data: data.data,
          fnCreatedRow: ( row, order, index ) => {
            $(row).attr('id', order[0]);
          },
          columns: [{data:1},{data:2},{data:3},{data:4},{data:5},{
                  data: null,
                  defaultContent: `<button type="button" class="btn btn-info btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_update"><i class="fas fa-pen"></i></button>
                  <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_delete"><i class="fas fa-trash"></i></button>`,
                  orderable: false
              }],
          searching: true, // FrontEnd quick search for small size data, add forms and sql params in BackEnd controllers for backend search
          lengthChange: false,
          pageLength: 100,
          responsive: true,
          columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 1 }
          ],
          pagingType: "full_numbers",
          dom : '<"top"fp>',
          language: {
            search: '',
            paginate: {
              "first": '<i class="fas fa-angle-double-left"></i>',
              "previous": '<i class="fas fa-angle-left"></i>',
              "next": '<i class="fas fa-angle-right"></i>',
              "last": '<i class="fas fa-angle-double-right"></i>'
          }},
          info: false,
          order: [0,'desc']
        });
      }
    }
    button.disabled = false;
    button.innerHTML = before;
  }
  xhttp.onerror = function() {
    button.disabled = false;
    button.innerHTML = before;
    notifyJS({"message":"Internet Error","status":"error"});
  };
  xhttp.open("POST", "/controller");
  xhttp.send(formData);
  
}

function submitAction(){
  event.preventDefault();
  const button = event.target;
  const before = event.target.innerHTML;
  button.innerHTML = '<i class="fa fa-circle-notch fa-spin"></i>';
  button.disabled = true;
  const form = event.target.parentNode.parentNode;
  const formData = new FormData(form);
  const rowId = formData.get("target");
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (xhttp.status === 200) {
      data = JSON.parse(this.responseText);
      if(data.status=='success'){
        switch (data.action) {
          case 'admin_info_create':
            prependTableRow("admin_info_table",rowId,data.data);
          break;
          case 'admin_info_update':
            updateTableRow(rowId, data.data);
          break;
          case 'admin_info_delete':
            deleteTableRow(rowId);
          break;
          default:
        } 
      }
    }
    button.disabled = false;
    button.innerHTML = before;
    notifyJS(data);
    $('#modal-action').modal('hide');
  }
  xhttp.onerror = function() {
    button.disabled = false;
    button.innerHTML = before;
    notifyJS({"message":"Internet Error","status":"error"});
    $('#modal-action').modal('hide');
  };
  xhttp.open("POST", "/controller");
  xhttp.send(formData);
}

// Allow Periodically Reload?
document.getElementById('autoPerform').addEventListener('change', toggleAutoPerform);
// Initial Load Data?
document.addEventListener('DOMContentLoaded', () => {
  submitSearch();
});

</script>

