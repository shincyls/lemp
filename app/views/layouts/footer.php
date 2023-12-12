<footer class="footer py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 mx-auto text-center mt-1">
                <small class="mb-0 text-secondary">
                </small>
            </div>
        </div>
    </div>
</footer>

<script src="./app/assets/js/core/popper.min.js"></script>
<script src="./app/assets/js/core/bootstrap.min.js"></script>
<script>
var win = navigator.platform.indexOf('Win') > -1;
if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
        damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
}
function getURLParams() {
  const urlParams = new URLSearchParams(window.location.search);
  const params = {};
  for (const [key, value] of urlParams) {
    params[key] = value;
  }
  return params;
}
function submitAction(){
  event.preventDefault();
  const form = document.querySelector('.app-controller');
  const formData = new FormData(form);
  fetch('/ctrl/auth', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if(data.status=='success'){
      if(data.action=='web_signin'){
        let url = '/home';
        let urlParams = getURLParams();
        if(urlParams.url!=undefined){
          url = '/'+urlParams.url;
        }
        setTimeout(function() {
          window.location.href = url;
        }, 2000);
      }
    }
    notifyJS(data);
  })
}
function notifyJS(el) {
  var body = document.querySelector('body');
  var alert = document.createElement('div');
  var color = {"success":"green","danger":"crimson","error":"firebrick"};
  alert.classList.add('alert','alert-dismissible','position-fixed','fixed-top','top-0','border-0','text-white','d-flex','w-50','end-0','start-0','mt-2','mx-auto','py-2');
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
</script>
</body>

</html>