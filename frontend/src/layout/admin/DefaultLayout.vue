<template>
  <div class="flex flex-row h-screen w-full">
    <Sidebar
      :toggle="toggleMenu"
      :menu-expanded="menuOpen"
      :class="[
        menuOpen ? 'w-[12rem] md:w-[20rem]' : 'w-[2.5rem] md:w-[3rem]',
        'z-30',
      ]"
    />
    <div class="ml-[2.5rem] md:ml-[3rem] grow">
      <Navbar @toggle="toggleMenu" :menu-expanded="menuOpen" class="z-20" />
      <div class="bg-[#e4e6ea] overflow-auto w-full h-[calc(100vh-3.25rem)]">
        <Toast />
        <div class="p-4 min-h-full max-h-max w-full">
          <router-view class="z-10 h-full w-full"></router-view>
        </div>
      </div>
    </div>
    <!-- Overlay -->
    <div
      :class="{
        'absolute z-20 top-0 left-0 bottom-0 right-0 bg-black opacity-30':
          menuOpen,
      }"
    ></div>
  </div>
</template>
<script setup>
import Sidebar from "../../components/admin/Sidebar.vue";
import Navbar from "../../components/admin/Navbar.vue";
import Alert from "../../components/Alert.vue";
import Toast from "../../components/Toast.vue";
import { useStore } from "vuex";
import { ref } from "vue";

const store = useStore();

let menuOpen = ref(false);

function toggleMenu() {
  setTimeout(() => {
    menuOpen.value = !menuOpen.value;
  }, 50);
}
</script>
