<?php
  $table = "admin_info";
  include 'layouts/home_header.php';
  // include 'layouts/home_sidebar.php';
  include 'layouts/home_navbar.php';
?>

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
            <div class="row d-flex my-2">
              <div class="col-sm-12 col-md-3 my-1"><button class="btn btn-sm bg-gradient-primary" type="submit" onclick="submitSearch()">Search <span id="spinner"><i class="fa fa-sync"></i><span></button></div>
            </div>
          <form>
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

<?php include 'layouts/home_footer.php'; ?>

<script>
function submitSearch(){
    event.preventDefault();
    document.getElementById('spinner').innerHTML = '<i class="fa fa-sync fa-spin"></i>';
    const form = document.getElementById('<?php echo $table; ?>_search');
    const formData = new FormData(form);
    const formDataObject = {};
    for (const [key, value] of formData.entries()) {
      formDataObject[key] = value;
    }
    $('#'+formData.get('table')).DataTable('#'+formData.get('table')).clear().destroy();
    $('#'+formData.get('table')).DataTable({
      ajax: {
        "url":'/ctrl/<?php echo $table; ?>',
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
      searching: false,
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
  fetch('/ctrl/<?php echo $table; ?>', {
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
</script>

