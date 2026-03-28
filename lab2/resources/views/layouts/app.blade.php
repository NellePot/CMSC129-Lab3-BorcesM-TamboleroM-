<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Emergency Inventory System')</title>

    <!-- Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <!-- Emergency Theme -->
    <style>
      :root {
        --primary-blue: #0d6efd;
        --dark-blue: #0a58ca;
        --alert-red: #dc3545;
        --dark-red: #a71d2a;
      }

      .bg-primary-blue {
        background-color: var(--primary-blue) !important;
      }

      .btn-blue {
        background-color: var(--primary-blue);
        color: white;
        border: none;
      }

      .btn-blue:hover {
        background-color: var(--dark-blue);
      }

      .btn-red {
        background-color: var(--alert-red);
        color: white;
        border: none;
      }

      .btn-red:hover {
        background-color: var(--dark-red);
      }

      .card-header.bg-primary-blue {
        background-color: var(--primary-blue);
        color: white;
      }
    </style>

    @stack('styles')
  </head>

  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-blue">
      <div class="container">
        <a class="navbar-brand" href="{{ route('inventory.index') }}">
          <i class="fas fa-truck-medical"></i> Emergency Inventory
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('inventory.index') }}">
                <i class="fas fa-boxes"></i> Supplies
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('inventory.create') }}">
                <i class="fas fa-plus-circle"></i> Add Supply
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-danger" href="{{ route('inventory.critical') }}">
                <i class="fas fa-triangle-exclamation"></i> Critical Items
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Alerts -->
    <div class="container mt-3">
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif
    </div>

    <!-- Main Content -->
    <main class="container my-4">
      @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
      <p class="mb-0">
        &copy; {{ date('Y') }} Emergency Preparedness System
      </p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
  </body>
</html>
