<template>
  <b-button variant="outline-warning" @click.prevent="onClick">
    <font-awesome-icon :icon="['fas', 'phone-square-alt']" />
  </b-button>
</template>

<script>
export default {
  methods: {
    onClick() {
      this.$emit("click");
    },
  },
};
</script>