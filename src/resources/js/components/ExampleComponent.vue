<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">Example Component</div>

          <div class="card-body">
            {{ messageValue }}
          </div>

          <h1>sdsdsd <span>dsdsds</span></h1>
          <p>sdsd</p>

          <base-button :title="'Press Me'"></base-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  props: {
    message: String,
  },
  components: {},
  setup(props) {
    const messageValue = ref(props.message);

    return {
      messageValue,
    };
  },
};
</script>
