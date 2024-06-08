<template>
  <Navbar style="height: 60px;" class="navbar-container" />
  <div style="margin-top: 75px;">
    <FileForm @uploadSuccess="refreshFilesImported" class="pb-4 border" />
    <FilesImported class="pb-4" :key="refreshKey" />
  </div>
</template>

<script setup>
import Navbar from "/src/components/Navbar.vue";
import FileForm from "/src/sections/FileForm.vue";
import FilesImported from "/src/sections/FilesImported.vue";
import { ref } from 'vue';

const refreshKey = ref(0); // This will trigger re-rendering of FilesImported component

const refreshFilesImported = () => {
  refreshKey.value++; // Incrementing refreshKey to trigger re-rendering of FilesImported component
}
</script>

<style scoped>
.main-content {
  max-height: 100vh;
  /* Mudar aqui para altura exata da section :) */
}

.navbar-container {
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 999;
}
</style>
