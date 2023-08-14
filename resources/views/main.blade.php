<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <title>@yield('title')</title>
</head>
<body>
<div class="wrapper">
  <header class="header">
    <div class="header_container container">
      <div class="header_left">
        <a href="/">Home</a>
      </div>
      <div class="header_right">
        <a href="/lead/create">Create new contact</a>
      </div>
    </div>
  </header>

  <main class="page">
    <div class="main_container container">

      @yield('content')

    </div>
  </main>

  <footer class="footer">
    <div class="container">
      &copy; Bitrix24 app 2023
    </div>
  </footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
