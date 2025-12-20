<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

// --- IMPORT VIDEO AND POSTER ASSETS ---
// Import video (.mp4)
import teaser1_video from "@/assets/video/2026 teaser 1.mp4";
import teaser2_video from "@/assets/video/2026 teaser 2.mp4";
import newTeaser1_video from "@/assets/video/2026 new teaser 1.mp4";
import newTeaser2_video from "@/assets/video/2026 new teaser 2.mp4";
import newTeaser3_video from "@/assets/video/2026 new teaser 3.mp4";
import avp1_video from "@/assets/video/2026 avp 1.mp4";
import avp2_video from "@/assets/video/2026 avp 2.mp4";

// Import (.jpg)
import teaser1_poster from "@/assets/thumbnail/2026 teaser 1.jpg";
import teaser2_poster from "@/assets/thumbnail/2026 teaser 2.jpg";
import newTeaser1_poster from "@/assets/thumbnail/2026 new teaser 1.jpg";
import newTeaser2_poster from "@/assets/thumbnail/2026 new teaser 2.jpg";
import newTeaser3_poster from "@/assets/thumbnail/2026 new teaser 3.jpg";
import avp1_poster from "@/assets/thumbnail/2026 avp 1.jpg";
import avp2_poster from "@/assets/thumbnail/2026 avp 2.jpg";

let owl2 = null;

// Video list now contains objects linking video source and poster image
const videos = ref([
    { video: teaser1_video, poster: teaser1_poster },
    { video: teaser2_video, poster: teaser2_poster },
    { video: newTeaser1_video, poster: newTeaser1_poster },
    { video: newTeaser2_video, poster: newTeaser2_poster },
    { video: newTeaser3_video, poster: newTeaser3_poster },
    { video: avp1_video, poster: avp1_poster },
    { video: avp2_video, poster: avp2_poster },
]);

// Track the active video index
const activeIndex = ref(0);

// --- Function to sync right column height with left carousel ---
const syncRightHeight = () => {
    const leftDiv = document.querySelector(".owl-2");
    const rightDiv = document.querySelector(".right-thumbs-container");
    if (leftDiv && rightDiv) {
        rightDiv.style.height = `${leftDiv.offsetHeight}px`;
    }
};

onMounted(() => {
    if (!window.$) {
        console.error("jQuery not loaded");
        return;
    }

    const $carousel = $(".owl-2");

    // Initialize Owl Carousel
    owl2 = $carousel.owlCarousel({
        items: 1,
        loop: true,
        margin: 20,
        smartSpeed: 800,
        nav: false,
        dots: false,
        autoplay: false,
        stagePadding: 0,
    });

    // Custom arrows
    $(".bg-left-half123").on("click", () => owl2.trigger("prev.owl.carousel"));
    $(".bg-right-half").on("click", () => owl2.trigger("next.owl.carousel"));

    // Update activeIndex on carousel change
    $carousel.on("changed.owl.carousel", (e) => {
        // Correctly calculate the non-cloned item index (standard for Owl Carousel with loop)
        activeIndex.value = e.item.index - 2 < 0 ? 0 : e.item.index - 2;
        if (activeIndex.value >= videos.value.length) {
            activeIndex.value = activeIndex.value % videos.value.length;
        }

        // Pause all videos
        $("video.video-main").each(function () {
            this.pause();
        });

        // Play main video
        setTimeout(() => {
            // Find the video in the active Owl item
            const mainVideo = $(".owl-item.active").find(".video-main")[0];
            if (mainVideo) {
                // Ensure the main video is unmuted and can play (if controls are shown)
                mainVideo.muted = false;
                mainVideo.play();
            }
            syncRightHeight();
        }, 200);
    });

    // Auto-next when main video ends
    $(document).on("ended", ".owl-item.active .video-main", () => {
        owl2.trigger("next.owl.carousel");
    });

    // Play first video and sync height on load
    setTimeout(() => {
        const firstVideo = $(".owl-item.active").find(".video-main")[0];
        if (firstVideo) {
            firstVideo.muted = false; // Unmute first video on play
            firstVideo.play();
        }
        syncRightHeight();
    }, 300);

    // Adjust right height on window resize
    window.addEventListener("resize", syncRightHeight);
});

// Click thumbnail to change main video
function selectVideo(index) {
    activeIndex.value = index;
    // Calculate the Owl Carousel index (index + 2 for standard loop: true)
    const owlIndex = index + 2;
    owl2.trigger("to.owl.carousel", [owlIndex, 300]);
    setTimeout(() => syncRightHeight(), 350); // adjust after change
}

onBeforeUnmount(() => {
    $(".bg-left-half123").off("click");
    $(".bg-right-half").off("click");
    $(document).off("ended", ".owl-item.active .video-main");
    window.removeEventListener("resize", syncRightHeight);
});
</script>

<template>
    <div
        id="events"
        class="bg-[url('@/assets/nbg5.jpg')] bg-cover bg-no-repeat py-10 scroll-mt-22"
    >
        <div class="w-full max-w-[1320px] px-5 pb-4 mx-auto">
            <h1 class="text-white font-bold text-4xl text-center">
                2026 EVENTS
            </h1>

            <div
                class="w-fit px-2 bg-gradient-to-r from-blue-500 to-red-500 mt-5 mx-auto text-center"
            >
                <h1 class="text-white font-bold py-2 text-2xl">
                    Stay updated with our latest news and activities, and always
                    visit our website for more.
                </h1>
            </div>

            <div class="grid grid-cols-12 gap-5 mt-10">
                <div class="lg:col-span-8 col-span-12 left-carousel">
                    <div class="relative">
                        <button
                            class="absolute top-1/2 sm:start-4 start-1 -translate-y-1/2 z-10 bg-brand-blue text-white bg-left-half123 rounded-full sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] border-2 border-white opacity-50"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <button
                            class="absolute top-1/2 sm:end-4 end-1 -translate-y-1/2 z-10 bg-brand-blue text-white bg-right-half rounded-full sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] border-2 border-white opacity-50"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <div class="owl-carousel owl-2">
                            <div class="item" v-for="(v, i) in videos" :key="i">
                                <div
                                    class="w-full grid place-items-center border-2 border-brand-red h-full rounded-xl bg-black sm:p-3"
                                >
                                    <video
                                        :src="v.video"
                                        :poster="v.poster"
                                        class="video-main w-full h-auto block rounded-xl"
                                        playsinline
                                        muted
                                        controls
                                    ></video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="lg:col-span-4 col-span-12 border right-thumbs-container lg:block hidden"
                >
                    <div
                        class="bg-black border-2 p-2 border-brand-red rounded-xl"
                    >
                        <div
                            class="right-thumbs h-[500px] overflow-x-auto [scrollbar-width:thin] [scrollbar-color:var(--color-brand-blue)_black] [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar]:rounded-full [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-black [&::-webkit-scrollbar-thumb]:bg-[var(--color-brand-blue)] [&::-webkit-scrollbar-thumb]:rounded-full"
                        >
                            <div class="w-full grid gap-4">
                                <div
                                    v-for="(v, i) in videos"
                                    :key="i"
                                    v-if="i !== activeIndex"
                                    @click="selectVideo(i)"
                                    class="cursor-pointer"
                                >
                                    <video
                                        :src="v.video"
                                        :poster="v.poster"
                                        class="video-main w-full bg-brand-blue h-auto block rounded-xl border-2 border-brand-blue"
                                        preload="metadata"
                                    ></video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-black border-2 p-2 border-brand-red rounded-xl lg:hidden block mt-5"
            >
                <div
                    class="right-thumbs h-auto overflow-x-auto snap-x snap-mandatory [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                >
                    <div class="flex sm:gap-4 gap-2 sm:p-1">
                        <div
                            v-for="(v, i) in videos"
                            :key="i"
                            v-if="i !== activeIndex"
                            @click="selectVideo(i)"
                            class="cursor-pointer flex-shrink-0 sm:w-64 w-40 snap-center"
                        >
                            <video
                                :src="v.video"
                                :poster="v.poster"
                                class="video-main w-full bg-brand-blue h-auto block rounded-xl border-2 border-brand-blue"
                                preload="metadata"
                            ></video>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.gradient-border-image {
    border: 4px solid transparent; /* Required for border-image to work */
    -webkit-border-image: linear-gradient(to right, #3b82f6, #ef4444) 1;
    border-image: linear-gradient(to right, #3b82f6, #ef4444) 1;
}
</style>
