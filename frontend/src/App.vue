<template>
  <div>
    <header>
      <navbar />
    </header>
    <main class="mt-5">
      <b-container fluid="lg">
        <message />
        <router-view />
      </b-container>
    </main>
  </div>
</template>

<script>
import Navbar from "@/components/Navbar.vue";
import Message from "@/components/Message.vue";

export default {
  components: {
    Navbar,
    Message,
  },
};
</script>

<style >
.card-body {
  font-size: 14px;
}
</style>