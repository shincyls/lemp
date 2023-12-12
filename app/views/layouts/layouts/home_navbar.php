<nav class="navbar navbar-expand-lg navbar-dark bg-none">
  <div class="container-fluid">
    <a class="navbar-brand text-primary me-4" href="/home"><b><i class="fa fa-google"></i> HOMEPAGE</b><p class="breadcrumb-item text-xs text-white p-0 m-0">My Slogan Here</p></a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <div class="sidenav-toggler-inner">
        <i class="sidenav-toggler-line bg-white"></i>
        <i class="sidenav-toggler-line bg-white"></i>
        <i class="sidenav-toggler-line bg-white"></i>
      </div>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item d-flex align-items-center">
          <a href="/dashboard" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline"><b>Dashboard</b></span>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="/appusers" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline"><b>Users</b></span>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="/topups" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline"><b>Topups</b></span>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="/orders" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline"><b>Orders</b></span>
          </a>
        </li>
        <li>
          <div class="dropdown">
            <a href="/products" class="nav-link text-white mx-1 my-0 dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
              <b>Products</b>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
              <li><a class="dropdown-item" href="/products">Products</a></li>
              <li><a class="dropdown-item" href="/collections">Collections</a></li>
            </ul>
          </div>
        </li>
        <li>
          <div class="dropdown">
            <a href="/admin" class="nav-link text-white mx-1 my-0 dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
              <b>Admin</b>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
              <li><a class="dropdown-item" href="/admin/accounts">Accounts</a></li>
              <li><a class="dropdown-item" href="/admin/permissions">Permissions</a></li>
              <li><a class="dropdown-item" href="/admin/logs">Logs</a></li>
            </ul>
          </div>
        </li>
      </ul>
      <ul class="navbar-nav justify-content-between ms-auto">
        <li class="nav-item px-3 d-flex align-items-center ml-auto">
          <a href="javascript:;" class="nav-link text-white p-0">
            <i class="fa fa-bell cursor-pointer"></i>
          </a>
        </li>
        <li class="nav-item dropdown pe-2 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
          </a>
          <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
            <li class="mb-2">
              <a class="dropdown-item border-radius-md" href="/profile">
                <div class="d-flex py-1">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="text-sm font-weight-normal mb-1">
                      <span class="font-weight-bold">Profile</span>
                    </h6>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item border-radius-md" href="/signout">
                <div class="d-flex py-1">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="text-sm font-weight-normal mb-1">
                      <span class="font-weight-bold">Sign Out</span>
                    </h6>
                  </div>
                </div>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- 
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <a href="/home" class="font-weight-bolder text-white mb-0">Homepage</a>
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
      </ol>
    </nav>
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex" id="navbar">
      <ul class="navbar-nav justify-content-between">
        <li class="nav-item d-flex align-items-center">
          <a href="/dashboard" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline d-none"><b>Dashboard</b></span>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="/appusers" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline d-none"><b>Users</b></span>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="/topups" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline d-none"><b>Topups</b></span>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="/orders" class="nav-link text-white mx-1 my-0">
            <span class="d-sm-inline d-none"><b>Orders</b></span>
          </a>
        </li>
        <li>
          <div class="dropdown">
            <a href="/products" class="nav-link text-white mx-1 my-0 dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              <b>Products</b>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="/products">Products</a></li>
              <li><a class="dropdown-item" href="/collections">Collections</a></li>
            </ul>
          </div>
        </li>
        <li>
          <div class="dropdown">
            <a href="/admin" class="nav-link text-white mx-1 my-0 dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              <b>Admin</b>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="/admin/accounts">Accounts</a></li>
              <li><a class="dropdown-item" href="/admin/permissions">Permissions</a></li>
              <li><a class="dropdown-item" href="/admin/logs">Logs</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
            </div>
          </a>
        </li>
      </ul>
      
    </div>
  </div>
</nav> -->