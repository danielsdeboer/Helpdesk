@if (count($errors) > 0)
  <section class="hero is-danger">
    <div class="hero-body">
      <div class="container">
        <h1 class="title">Oops!</h1>
        <h2 class="subtitle">Look like we found some problems:</h2>

        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    </div>
  </section>
@endif