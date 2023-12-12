<?php
  $table = "admin_permission";
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
        <div class="card-header pb-0">
          <div class="card">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-6">
                  <span class="text-primary font-weight-bold">&nbsp;&nbsp;Admin Logs</span>
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
        </div>
        <div class="card-body">
          <table id="<?php echo $table; ?>_table" class="table table-responsive table-hover w-100 align-items-center">
            <thead class="text-white text-uppercase bg-gradient-primary"><tr>
              <th>Admin</th>
              <th>Info</th>
              <th style="width:70%;">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'app/views/layouts/home_footer.php'; ?>

<script>
function createForm(data=null,exists=null,rowid="",role=null){
  let checkBoxes = "";
  let output = "";
  role = role.split("");
  function check(int){
    return (int==1) ? "checked" : "";
  }
  if(data!=null || data.length==0){
    Object.entries(data).forEach(([key, value]) => {
      checkBoxes += `<div class="col-3 text-capitalize text-primary font-weight-bold"><p>${key}</p>`;
      for (let j = 0; j < value.length; j++) {
        let checked =  exists.split(',').includes(value[j]) ? "checked" : "";
        checkBoxes += `<div class="form-check"><label class="form-check-label">
                        <input class="form-check-input bg-white text-white" type="checkbox" name="items[]" value="${value[j]}" ${checked}>${value[j]}
                        <span class="form-check-sign bg-white"><span class="check"></span></span></label></div>`;
      }
      checkBoxes += `</div>`;
    });
    output = `<form class="app-controller">
              <input type="hidden" name="action" value="admin_permission_update">
              <input type="hidden" name="target" value="${rowid}">
              <div class="row">
              <div class="col-3 text-capitalize text-primary font-weight-bold"><p>Role</p>
              <div class="form-check"><label class="form-check-label">
              <input class="form-check-input bg-white text-white" type="checkbox" name="admin[0]" value="8" ${check(role[0])}>Master
              <span class="form-check-sign bg-white"><span class="check"></span></span></label></div>
              <div class="form-check"><label class="form-check-label">
              <input class="form-check-input bg-white text-white" type="checkbox" name="admin[1]" value="4" ${check(role[1])}>Management
              <span class="form-check-sign bg-white"><span class="check"></span></span></label></div>
              <div class="form-check"><label class="form-check-label">
              <input class="form-check-input bg-white text-white" type="checkbox" name="admin[2]" value="2" ${check(role[2])}>Admin
              <span class="form-check-sign bg-white"><span class="check"></span></span></label></div>
              <div class="form-check"><label class="form-check-label">
              <input class="form-check-input bg-white text-white" type="checkbox" name="admin[3]" value="1" ${check(role[3])}>Security
              <span class="form-check-sign bg-white"><span class="check"></span></span></label></div>
              </div>
              ${checkBoxes}</div>
              <div class="card-footer text-end">
              <button class="btn btn-outline-primary btn-sm" onclick="toggleCheck(true)">Check All</button>
              <button class="btn btn-outline-primary btn-sm" onclick="toggleCheck(false)">Uncheck All</button>
              <button class="btn bg-gradient-primary btn-sm" onclick="submitAction()">Save</button></div></form>`;
    return output;
  }
}
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
  let checkboxes;
  $('#'+formData.get('table')).DataTable().clear().destroy();
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (xhttp.status === 200) {
      data = JSON.parse(this.responseText);
      if(data.status=='success'){
        for (let j = 0; j < data.data.length; j++) {
          data.data[j][3] = createForm(data.list,data.data[j][3],data.data[j][0],data.data[j][4]);
        }
        $('#'+formData.get('table')).DataTable({
          data: data.data,
          fnCreatedRow: ( row, order, index ) => {
            $(row).attr('id', order[0]);
          },
          columns: [{data:1},{data:2},{data:3}],
          searching: true, // FrontEnd quick search for small size data, add forms and sql params in BackEnd controllers for backend search
          lengthChange: false,
          pageLength: 100,
          responsive: true,
          columnDefs: [
            { responsivePriority: 1, targets: 0 }
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
  const uuid = form.parentNode.parentNode.id;
  const formData = new FormData(form);
  formData.append("target",uuid);
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (xhttp.status === 200) {
      data = JSON.parse(this.responseText);
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
// Initial Load Data?// Initial Load Data?
document.addEventListener('DOMContentLoaded', () => {
  submitSearch();
});
</script>

