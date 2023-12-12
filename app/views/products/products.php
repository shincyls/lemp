<?php
  $table = "appuser_info";
  include 'app/views/layouts/home_header.php';
  include 'app/views/layouts/home_navbar.php';
?>

<div class="container-fluid py-4 h-100">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Appusers Management</h6>
          <button type="button" class="btn btn-sm bg-gradient-primary" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_create">
            APPUSER <i class="fas fa-plus" aria-hidden="true"></i>
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
            <thead class="text-white text-uppercase bg-gradient-primary"><tr><th>#</th><th>Username</th><th>Name</th><th>Email</th><th>Phone</th><th>Balance</th><th></th></tr></thead>
            <tbody class="font-weight-bold"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'app/views/layouts/home_footer.php'; ?>

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
        "url":'/ctrl/appusers',
        "type":'POST',
        "data":formDataObject
      },
      fnCreatedRow: function( row, order, index ) {
        $(row).attr('id', order[0]);
      },
      columns: [{data:1},{data:2},{data:3},{data:4},{data:5},{data:6},{
                    data: null,
                    defaultContent: `<button type="button" class="btn btn-info btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_update"><i class="fas fa-pen"></i></button>
                    <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_delete"><i class="fas fa-trash"></i></button>
                    <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_topup"><i class="ni ni-money-coins"></i></button>
                    <button type="button" class="btn btn-warning btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-action" onclick="modalAction(this)" action="<?php echo $table; ?>_spend"><i class="ni ni-cart"></i></button>`,
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
  fetch('/ctrl/appusers', {
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
    }
    notifyJS(data);
    $('#modal-action').modal('hide');
  });
}


function addmore(){
  event.preventDefault();
  const elem = `<td><input type="text" name="product[]" class="form-control" value=""></td>
                  <td><input type="number" name="unit[]" class="form-control unit" onchange="calcTotal(this)" min="1" max="1000" value="1"></td>
                  <td><input type="number" name="price[]" class="form-control price" onchange="calcTotal(this)" min="0.00" max="99999.90" value="0.00"></td>
                  <td><input type="number" name="amount[]" class="form-control total" value="0.00" readonly></td>`;
  const newDiv = document.createElement('tr');
  newDiv.innerHTML = elem;
  const parentElement = event.target.parentNode;
  const grandparentElement = parentElement.parentElement;
  grandparentElement.parentNode.insertBefore(newDiv, grandparentElement);
}


function calcTotal(input) {
  const row = input.parentNode.parentNode;
  const unitInput = row.querySelector('.unit');
  const priceInput = row.querySelector('.price');
  const totalInput = row.querySelector('.total');
  const unit = parseFloat(unitInput.value) || 0;
  const price = parseFloat(priceInput.value) || 0;
  const total = unit * price;
  totalInput.value = total.toFixed(2);
  calcSum();
}

function calcSum() {
  const inputs = document.querySelectorAll('.total');
  let sum = 0;
  inputs.forEach(input => {
    sum += parseFloat(input.value) || 0;
  });
  document.getElementById('total').value = sum.toFixed(2);
}
</script>
