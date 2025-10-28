<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Brand -->
    <a class="navbar-brand text-uppercase font-weight-bold" href="">
        Grafika Yayasan Lektur
    </a>

    <!-- Navbar Items (Right Side) -->
    <ul class="navbar-nav ml-auto">
        <!-- Divider -->
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 font-weight-bold text-uppercase">
                    {{ auth()->check() ? auth()->user()->name : 'Guest' }}
                </span>
                @if (Auth::user()->image_name)
                    <img class="img-profile rounded-circle" src="{{ asset('storage/user/' . Auth::user()->image_name) }}"
                        alt="Profile Picture">
                @else
                    <i class="fas fa-user fa-2x"></i>
                @endif

            </a>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item logout-trigger" href="#">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<!-- JavaScript for Logout -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".logout-trigger").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("logout-form").submit();
        });
    });
</script>
