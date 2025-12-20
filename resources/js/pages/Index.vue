<script setup>
// import { onMounted, nextTick } from "vue";
import Aboutus from "@/components/landing/Aboutus.vue";
import Banner from "@/components/landing/Banner.vue";
import Careers from "@/components/landing/Careers.vue";
import Contact from "@/components/landing/Contact.vue";
import Footer from "@/components/landing/Footer.vue";
import Goal from "@/components/landing/Goal.vue";
import New from "@/components/landing/New.vue";
// import Portfolio from "@/components/landing/Portfolio.vue";
import Events from "@/components/landing/Events.vue";
import Services from "@/components/landing/Services.vue";
import Technology from "@/components/landing/Technology.vue";

// onMounted(() => {
//     nextTick(() => {
//         const sections = document.querySelectorAll("div[id]");
//         const mobileLinks = document.querySelectorAll(".mobile-link");
//         const navLinks = document.querySelectorAll(".xl\\:flex a");
//         const menuBtn = document.getElementById("menu-btn");
//         const mobileMenu = document.getElementById("mobile-menu");
//         const navbar = document.getElementById("navbar");
//         const scrollThreshold = 50;

//         // -------------------- NAVBAR BG ON SCROLL --------------------
//         function toggleNavbarBackground() {
//             if (window.scrollY > scrollThreshold) {
//                 navbar.classList.add("bg-black");
//                 navbar.classList.remove("text-white");
//             } else {
//                 navbar.classList.remove("bg-black");
//                 navbar.classList.add("text-white");
//             }
//         }

//         window.addEventListener("scroll", toggleNavbarBackground);
//         toggleNavbarBackground();

//         // Utility function to clean href into an ID
//         function extractId(href) {
//             return href.replace("/", "").replace("#", "");
//         }

//         // -------------------- MOBILE NAV LINK --------------------
//         function activateMobileLink() {
//             let scrollY = window.scrollY + 150;
//             let currentId = "";

//             sections.forEach((section) => {
//                 const top = section.offsetTop;
//                 const height = section.offsetHeight;
//                 if (scrollY >= top && scrollY < top + height) {
//                     currentId = section.getAttribute("id");
//                 }
//             });

//             mobileLinks.forEach((link) => {
//                 const href = extractId(link.getAttribute("href"));
//                 link.classList.remove("active");
//                 if (href === currentId) link.classList.add("active");
//             });
//         }

//         mobileLinks.forEach((link) => {
//             link.addEventListener("click", function (e) {
//                 e.preventDefault();

//                 const targetId = extractId(this.getAttribute("href"));
//                 const target = document.getElementById(targetId);

//                 if (target) {
//                     window.scrollTo({
//                         top: target.offsetTop - 70,
//                         behavior: "smooth",
//                     });
//                 }

//                 mobileMenu.classList.add("hidden");
//             });
//         });

//         window.addEventListener("scroll", activateMobileLink);
//         activateMobileLink();

//         // -------------------- DESKTOP NAV LINK --------------------
//         function activateLink() {
//             let scrollY = window.scrollY + 150;
//             let currentId = "";

//             sections.forEach((section) => {
//                 const top = section.offsetTop;
//                 const height = section.offsetHeight;
//                 if (scrollY >= top && scrollY < top + height) {
//                     currentId = section.getAttribute("id");
//                 }
//             });

//             navLinks.forEach((link) => {
//                 const underline = link.querySelector("span span");
//                 const href = extractId(link.getAttribute("href"));

//                 link.classList.remove("text-brand-blue");
//                 link.classList.add("text-white");
//                 underline?.classList.remove("w-full");

//                 if (href === currentId) {
//                     link.classList.add("text-brand-blue");
//                     link.classList.remove("text-white");
//                     underline?.classList.add("w-full");
//                 }
//             });
//         }

//         window.addEventListener("scroll", activateLink);
//         activateLink();

//         // -------------------- MOBILE MENU TOGGLE --------------------
//         menuBtn?.addEventListener("click", () => {
//             mobileMenu.classList.toggle("hidden");
//         });
//     });
// });
</script>

<template>
    <!-- Start Navbar -->
    <Banner />
    <!-- End Navbar -->

    <!-- Start About Us -->
    <Aboutus />
    <!-- End About Us -->

    <!-- Start Services -->
    <Services />
    <!-- End Services -->

    <!-- Start Technology -->
    <Technology />
    <!-- End Technology -->

    <!-- Start Goal -->
    <Goal />
    <!-- End Goal -->

    <!-- Start Portfolio -->
    <Events />
    <!-- End Portfolio -->

    <!-- Start Careers -->
    <Careers />
    <!-- End Careers -->

    <!-- Start New -->
    <New />
    <!-- End New -->

    <!-- Start Contact -->
    <Contact />
    <!-- End Contact -->

    <!-- Start Footer -->
    <Footer />
    <!-- End Footer -->
</template>
