<?php include 'layouts/header.php'; ?>

<div class="container position-sticky z-index-sticky top-0">
  <main class="main-content mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">

            <div class="col-6 d-lg-flex d-none p-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg'); background-size: cover;">
                <span class="mask bg-gradient-primary opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Coming Soon"</h4>
                <p class="text-white position-relative">The more effortless the writing looks, the more effort the writer actually put into the process.</p>
              </div>
            </div>
            
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-100 mx-auto rounded shadow" style="background-color: white;">
              <div class="card card-plain">
                <div class="card-header bg-none pb-0 text-center" style="background-color: white;">
                  <h4 class="font-weight-bolder text-primary text-gradient">Sign In</h4>
                </div>
                <div class="card-body">
                  <form class="app-controller" role="form">
                    <input type="hidden" name="action" value="web_signin"/>
                    <div class="mb-3">
                      <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email">
                    </div>
                    <div class="mb-3">
                      <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" aria-label="Password">
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="rememberMe">
                      <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                    <div class="text-center">
                      <button type="button" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0" onclick="submitAction()">Sign In</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    <a href="/signup" class="text-primary text-gradient font-weight-bold">Sign-Up Now</a>
                  </p>
                  <p class="mb-4 text-sm mx-auto">
                    <a href="/forgetpwd" class="text-primary text-gradient font-weight-bold">Forgot Password</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php include 'layouts/footer.php'; ?>
