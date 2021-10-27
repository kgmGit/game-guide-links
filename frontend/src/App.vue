<template>
  <div>
    <header>
      <navbar />
    </header>
    <main class="mt-5">
      <div class="container">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script>
import Navbar from "@/components/Navbar.vue";

export default {
  components: {
    Navbar,
  },
};
</script>
