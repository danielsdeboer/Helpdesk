<!DOCTYPE html>
<html>
<head>
  <title>Tickets</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.0/css/bulma.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  <meta charset="utf-8">
  <meta name="robots" content="noindex" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    /* Vertical rhythm */
    .has-vr {
      margin-bottom: 2em;
    }

    .is-collapsed {
      margin-bottom: 0;
      margin-top: 0;
    }

    .is-collapsed-top {
      margin-top: 0;
      padding-top: 0;
    }

    .is-collapsed-bottom {
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .has-margin-bottom {
      margin-bottom: 1rem !important;
    }

    .modal-background {
      background-color: rgba(50, 50, 50, 0.6);
    }

    .section.is-small,
    .hero-body.is-small {
      padding-bottom: 1.5rem;
      padding-top: 1.5rem;
    }

    .is-mi-large {
      font-size: 3rem;
    }

    .select,
    .select select {
      width: 100%;
    }

    [v-cloak] { display: none }
  </style>
</head>
<body>
  @include('helpdesk::partials.header')

  @yield('content')

  @include('helpdesk::partials.footer')
</body>
</html>
