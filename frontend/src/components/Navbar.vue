<template>
  <Menubar :model="items" class="flex justify-content-center ">
          <template #start>
            <img src="/src/assets/logo.png" alt="logo" height="50" style="padding-top: 5px;"/>
          </template>
          <template #item="{ item, props, hasSubmenu, root }">
              <a v-ripple class="flex align-items-center" v-bind="props.action">
                  <span :class="item.icon" />
                  <span class="ml-2">{{ item.label }}</span>
              </a>
          </template>
          <template #end>
          </template>
      </Menubar>
</template>

<script setup>
import { ref } from "vue";

const items = ref();
</script>

<style>
.p-menuitem-link{
gap: 4px;
}
</style>

