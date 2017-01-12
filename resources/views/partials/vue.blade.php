@if(config('app.debug'))
  <script src="https://unpkg.com/vue/dist/vue.js"></script>
@else
  <script src="https://unpkg.com/vue/dist/vue.min.js"></script>
@endif