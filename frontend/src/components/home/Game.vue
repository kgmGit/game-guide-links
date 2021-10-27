<template>
  <div>
    <b-card>
      <b-card-text>{{ game.name }}</b-card-text>
    </b-card>
  </div>
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