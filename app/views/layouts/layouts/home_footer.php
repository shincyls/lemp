  <footer class="footer pt-3  ">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div class="copyright text-center text-sm text-white">
            Pandastic Technologies © <script>document.write("2023 - "+(new Date().getFullYear()))</script>
          </div>
        </div>
      </div>
    </div>
  </footer>
</main>

<div class="modal fade" id="modal-action" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header p-0 border-bottom-0">
        <div id="modal-title" class="card-header pb-0 text-left my-2"></div>
        <div class="ms-auto text-end">
          <a class="btn btn-link text-primary text-gradient p-0" onclick="modalLarger(false)"><i class="fas fa-minus" aria-hidden="true"></i></a>
          <a class="btn btn-link text-primary text-gradient" onclick="modalLarger(true)"><i class="fas fa-plus" aria-hidden="true"></i></a>
        </div>
      </div>
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div id="modal-body" class="card-body pb-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="../app/assets/js/core/popper.min.js"></script>
<script src="../app/assets/js/core/bootstrap.min.js"></script>
<script src="../app/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../app/assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="../app/assets/js/argon-dashboard.min.js?v=2.0.4"></script>
<script src="../app/assets/js/custom.js?v=1.0.2"></script>
</body>
</html>

<style>
.enlarge {
  color: gold !important;
  /* font-size: 24px; */
  /* animation: enlarge 1s infinite alternate; */
}
@keyframes enlarge {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(1.2);
  }
}
</style>