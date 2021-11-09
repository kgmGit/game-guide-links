<template>
  <b-button variant="outline-primary" @click.prevent="onClick">
    <font-awesome-icon v-show="liked" :icon="['fas', 'thumbs-up']" />
    <font-awesome-icon v-show="!liked" :icon="['far', 'thumbs-up']" />
    {{ count }}
  </b-button>
</template>

<script>
export default {
  props: {
    count: {
      type: Number,
    },
    liked: {
      type: Boolean,
    },
  },
  methods: {
    onClick() {
      this.$emit("click");
    },
  },
};
</script>