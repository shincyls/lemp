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
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Sign Up"</h4>
                <p class="text-white position-relative">The more effortless the writing looks, the more effort the writer actually put into the process.</p>
              </div>
            </div>
            
            <div class="col-xl-6 col-lg-6 col-md-12 d-flex flex-column mx-lg-100 mx-auto rounded shadow" style="background-color: white;">
              <div class="card card-plain">
                <div class="card-header bg-none pb-0 text-center" style="background-color: white;">
                  <h4 class="font-weight-bolder text-primary text-gradient">Sign Up Account</h4>
                </div>
                <div class="card-body">
                  <form class="app-controller" role="form">
                    <div class="row">
                      <input type="hidden" name="action" value="web_signup"/>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" aria-label="Username">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <input type="text" name="fullname" class="form-control form-control-lg" placeholder="Fullname" aria-label="Fullname">
                         </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="number" name="phone" class="form-control form-control-lg" placeholder="Phone" aria-label="Phone">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" name="company" class="form-control form-control-lg" placeholder="Company" aria-label="Company">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="password" name="pwdset" class="form-control form-control-lg" placeholder="Password" aria-label="Password">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="password" name="pwdcon" class="form-control form-control-lg" placeholder="Password Confirm" aria-label="Password">
                        </div>
                      </div>
                      <div class="text-center">
                        <button type="button" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0" onclick="submitAction()">Sign-Up</button>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    <a href="/signin" class="text-primary text-gradient font-weight-bold">Sign-In Now</a>
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