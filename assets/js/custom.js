// Table Related For Controller Callback Actions
function updateTableRow(id,obj){
  // Update Row - Replacing New Value
  let row = document.getElementById(id);
  row.classList.add('table-info');
  for (var j = 0; j < obj.length; j++) {
    if(obj[j]!=null){
      row.cells[j].innerHTML = obj[j];
    }
  };
}
function deleteTableRow(id){
  // Delete Row - Highlightning Only, will get lost on next submitSearch
  let row = document.getElementById(id);
  row.classList.add('table-danger');
}
function prependTableRow(tb,id,obj){
  // Create Row - Targeting the table and inject the row
  tb = document.getElementById(tb).getElementsByTagName('tbody')[0];
  let html = document.createElement('tr');
  html.setAttribute("id", id);
  let str = "";
  obj[0] = tb.rows.length+1;
  for (let j = 0; j < obj.length; j++) {
    str += "<td>"+String(obj[j])+"</td>";
  }
  html.innerHTML = str;
  tb.prepend(html);
}

// UIUX Quality Of Life
function modalLarger(yes=true) {
  if(yes){
    document.getElementById('modal-action').firstElementChild.classList.add('modal-xl');
  } else {
    document.getElementById('modal-action').firstElementChild.classList.remove('modal-xl');
  }
}
function toggleCheck(yes=true){
  event.preventDefault();
  const form = event.target.parentNode.parentNode;
  if (form) {
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((checkbox) => {
      checkbox.checked = yes;
    });
  }
}
function notifyJS(el) {
  var body = document.querySelector('body');
  var alert = document.createElement('div');
  var color = {"success":"lightgreen","danger":"crimson","error":"firebrick","warning":"gold","info":"lightskyblue"};
  alert.classList.add('alert','alert-dismissible','position-fixed','top-0','border-0','text-white','d-flex','w-50','end-0','start-0','mt-2','mx-auto','py-2');
  alert.style.transform = 'translate3d(0px, 0px, 0px)'
  alert.style.opacity = '0';
  alert.style.transition = '.35s ease';
  alert.style.background = color[el.status];
  setTimeout(function() {
    alert.style.transform = 'translate3d(0px, 20px, 0px)';
    alert.style.setProperty("opacity", "1", "important");
  }, 100);
  alert.innerHTML = `<span class="alert-icon"><i class="ni ni-send"></i></span><span class="text-small mx-2">${el.message}</span><span class="ms-auto" data-bs-dismiss="alert" aria-label="Close" aria-hidden="true"><i class="fa fa-circle-notch fa-spin"></i></span></span>`;
  body.appendChild(alert);
  setTimeout(function() {
    alert.style.transform = 'translate3d(0px, 0px, 0px)'
    alert.style.setProperty("opacity", "0", "important");
  }, 4000);
}
function getIdFromClosestRow(element) {
  let me = element.getAttribute('action').split('_');
  me = me[me.length - 1];
  if (me!='create') {
    const id = element.parentNode.parentNode.id;
    return id;
  } else {
    return "";
  }
}
function routeTo(element) {
  const uuid = element.parentNode.parentNode.id;
  const path = element.getAttribute('href');
  window.open(path+'/'+uuid, '_blank');
}

// Render Modal Form From Backend
function modalAction(element) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
      var res = JSON.parse(this.responseText);
      document.getElementById('modal-title').innerHTML = res.title;
      document.getElementById('modal-body').innerHTML = res.body;
  }
  xhttp.open("POST", "/modals");
  xhttp.send(JSON.stringify({'id':getIdFromClosestRow(element),'action':element.getAttribute('action')}));
}

// Automatically click on submitSearch() every x seconds, good for monitor live data
let intervalId;
function autoClick(){
  submitSearch(false);
}
function toggleAutoPerform() {
  const checkbox = document.getElementById('autoPerform');
  const intervalSelect = document.getElementById('interval');
  const spinnerIcon = document.getElementById('spinnerIcon');
  if (checkbox.checked) {
    const selectedInterval = parseInt(intervalSelect.value) * 1000;
    intervalSelect.disabled = true;
    spinnerIcon.classList.add('fa-spin');
    intervalId = setInterval(autoClick, selectedInterval);
  } else {
    intervalSelect.disabled = false;
    spinnerIcon.classList.remove('fa-spin');
    clearInterval(intervalId);
  }
}
// On HTML done loaded Initialization
document.addEventListener('DOMContentLoaded', () => {
  const currentURIs = window.location.pathname.split('/');
  let path = "";
  for(i=1;i<currentURIs.length;i++){
    path += "/"+currentURIs[i];
    const elements = document.querySelectorAll(`[href="${path}"]`);
    elements.forEach(element => {
      element.classList.add('enlarge');
    });
  };
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
});



