<?php
  $table = "task_log";
  include 'app/views/layouts/home_header.php';
  include 'app/views/layouts/home_navbar.php';
?>

<div class="container-fluid h-100">
  <div class="row">
    <div class="col-12 text-center">
      <a class="btn btn-sm btn-primary" href="/cronjob/jobs"><b>Jobs</b></a>
      <a class="btn btn-sm btn-primary" href="/cronjob/logs"><b>Logs</b></a>
    </div>
  </div>
</div>

<div class="container-fluid py-4 h-100">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Cron Logs</h6>
          <form id="<?php echo $table; ?>_search">
            <div class="row my-2">
              <input type="hidden" name="action" value="<?php echo $table; ?>_search"/>
              <input type="hidden" name="table" value="<?php echo $table; ?>_table"/>
            </div>
            <div class="row d-flex">
              <div class="col-sm-12 col-md-2 my-1">
                <button class="btn btn-sm bg-gradient-primary w-100" type="submit" onclick="submitSearch()">Query <span id="spinner"><i class="fa fa-sync"></i><span></button>
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
        <div class="card-body">
          <table id="<?php echo $table; ?>_table" class="table table-responsive table-hover w-100 align-items-center">
            <thead class="text-white text-uppercase bg-gradient-primary"><tr>
              <th>Time</th>
              <th>Admin</th>
              <th>Action</th>
              <th>Message</th>
              <th>Status</th>
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
      "url":'/controller',
      "type":'POST',
      "data":formDataObject
    },
    fnCreatedRow: function( row, order, index ) {
      $(row).attr('id', order[0]);
    },
    columns: [{data:1},{data:2},{data:3},{data:4},{data:5}],
    searching: true, // FrontEnd quick search for small size data, add forms and sql params in BackEnd controllers for backend search
    lengthChange: false,
    pageLength: 100,
    responsive: true,
    columnDefs: [
      { responsivePriority: 1, targets: 0 },
      { responsivePriority: 2, targets: 2 },
      { responsivePriority: 3, targets: 3 }
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
  document.getElementById('spinner').innerHTML = '<i class="fa fa-sync"></i>';
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

