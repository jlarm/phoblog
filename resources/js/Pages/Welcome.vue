<script setup>
import { Deferred } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    posts: Array,
});

const currentSlides = ref({});

// Initialize currentSlides for each post
const initializeSlides = () => {
    if (props.posts) {
        props.posts.forEach((post) => {
            if (!(post.id in currentSlides.value)) {
                currentSlides.value[post.id] = 0;
            }
        });
    }
};

// Watch for posts changes and initialize slides
watch(() => props.posts, initializeSlides, { immediate: true });

const nextSlide = (postId, images) => {
    currentSlides.value[postId] = (currentSlides.value[postId] + 1) % images.length;
};

const prevSlide = (postId, images) => {
    currentSlides.value[postId] = (currentSlides.value[postId] - 1 + images.length) % images.length;
};
</script>

<template>
    <div class="mx-auto my-10 max-w-2xl space-y-10">
        <Deferred data="posts">
            <template #fallback> Loading... </template>
            <article v-for="post in posts" :key="post.id" class="relative">
                <div class="relative aspect-video overflow-hidden">
                    <template v-if="post.images.length > 1">
                        <div class="relative h-full w-full">
                            <TransitionGroup name="slide" tag="div" class="relative h-full w-full">
                                <img
                                    v-for="(image, index) in post.images"
                                    :key="image"
                                    :src="image"
                                    class="absolute left-0 top-0 h-full w-full object-cover transition-opacity duration-300"
                                    :class="{
                                        'opacity-100': post.id in currentSlides && currentSlides[post.id] === index,
                                        'opacity-0': !(post.id in currentSlides) || currentSlides[post.id] !== index,
                                    }"
                                    alt=""
                                />
                            </TransitionGroup>
                        </div>

                        <div class="absolute inset-0 flex items-center justify-between p-4">
                            <button
                                @click="prevSlide(post.id, post.images)"
                                class="size-10 rounded-full bg-black/50 text-white hover:bg-black/70"
                            >
                                ←
                            </button>
                            <button
                                @click="nextSlide(post.id, post.images)"
                                class="size-10 rounded-full bg-black/50 text-white hover:bg-black/70"
                            >
                                →
                            </button>
                        </div>
                    </template>

                    <img v-else :src="post.images[0]" class="h-full w-full object-cover" alt="" />
                </div>
                <p class="mt-1 text-center font-mono text-sm">
                    {{
                        new Date(post.created_at).toLocaleDateString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                        })
                    }}
                </p>
            </article>
        </Deferred>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: opacity 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
}
</style>
