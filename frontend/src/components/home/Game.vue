<template>
  <b-link
    :to="{
      name: 'GameDetail',
      params: { title: game.title },
    }"
  >
    <b-card>
      <b-card-text>{{ game.title }}</b-card-text>
    </b-card>
  </b-link>
</template>

<script>
export default {
  props: {
    game: {
      type: Object,
    },
  },
};
</script>
