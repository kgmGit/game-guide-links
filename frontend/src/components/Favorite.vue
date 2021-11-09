<template>
  <b-button variant="outline-success" @click.prevent="onClick">
    <font-awesome-icon v-show="favorited" :icon="['fas', 'heart']" />
    <font-awesome-icon v-show="!favorited" :icon="['far', 'heart']" />
    {{ count }}
  </b-button>
</template>

<script>
export default {
  props: {
    count: {
      type: Number,
    },
    favorited: {
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