@if(config('app.debug'))
  <script src="https://unpkg.com/vue/dist/vue.js"></script>
@else
  <script src="https://unpkg.com/vue/dist/vue.min.js"></script>
@endif

<script>
  /*
   * Use ES-past syntax for wider compatibility.
   */
  Vue.component('form-modal', {
    props: ['modalName', 'modalTitle', 'actionRoute', 'csrfToken', 'availableModals', 'buttonText'],

    computed: {
      isVisible: function () {
        return this.availableModals[this.modalName].visible
      }
    },

    methods: {
      close: function () {
        this.$emit('close-modal', {modalName: this.modalName})
      }
    },

    mounted: function () {
      document.addEventListener("keydown", (e) => {
        if (this.isVisible && e.keyCode == 27) {
          this.close();
        }
      })
    },

    template: `
      <div
        class="modal"
        v-bind:class="{
          'is-active': isVisible
        }"
        v-on:keydown.esc="close"
        v-cloak
      >
        <div class="modal-background" @click="close"></div>

        <div class="modal-content">
          <div class="box">
            <h1 class="title" v-text="modalTitle"></h1>

            <form method="post" :action="actionRoute">
              <input type="hidden" name="_token" :value="csrfToken">

              <slot></slot>

              <div class="control is-grouped">
                <p class="control">
                  <button class="button is-primary" name="close_submit" v-text="buttonText"></button>
                </p>

                <p class="control">
                  <button class="button is-link" @click.prevent="close">Cancel</button>
                </p>
              </div>
            </form>
          </div>
        </div>

        <button class="modal-close" @click="close"></button>
      </div>
    `,
  })

  var app = new Vue({
    el: '.modal-app',

    data: {
      modals: {
        close: false,
        open: false,
        reply: false,
        assign: false,
        reassign: false,
        note: false,
        collab: false,
      }
    },

    methods: {
      toggle: function (modal) {
        this.modals[modal] = ! this.modals[modal]
      },

      close: function ({modalName}) {
        if (this.modals[modalName]) {
          this.toggle(modalName)
        }
      }
    }
  });
</script>
