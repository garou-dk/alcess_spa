<template>
  <div ref="container" class="binary-text-container font-mono">
    <span v-for="(char, index) in displayChars" :key="index" :class="{ 'is-binary': char.isBinary }">
      {{ char.value }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps<{
  text: string;
}>();

const displayChars = ref<{ value: string; isBinary: boolean }[]>([]);
const container = ref<HTMLElement | null>(null);
let animationFrame: number;
let startTime: number;

const initDisplay = () => {
  displayChars.value = props.text.split('').map(() => ({
    value: Math.random() > 0.5 ? '0' : '1',
    isBinary: true
  }));
};

const characters = '01';

const animate = (timestamp: number) => {
  if (!startTime) startTime = timestamp;
  const progress = timestamp - startTime;

  // Faster initial scrambling, then slow down as it reveals
  const revealProgress = Math.min(progress / 2000, 1); // 2 seconds reveal

  displayChars.value = props.text.split('').map((targetChar, index) => {
    // Determine if this character should be revealed already
    const shouldReveal = revealProgress > (index / props.text.length);
    
    if (shouldReveal) {
      return { value: targetChar, isBinary: false };
    } else {
      // Still scrambling
      return { 
        value: Math.random() > 0.5 ? '0' : '1', 
        isBinary: true 
      };
    }
  });

  if (revealProgress < 1) {
    animationFrame = requestAnimationFrame(animate);
  } else {
    // Add a slight "throb" or occasional bit flip to the revealed text? 
    // Or just stop. User asked for "moving binary".
    // Let's keep a subtle occasional flip of random characters at the end if we want it "moving"
    startSubtleMovement();
  }
};

let movementInterval: any;
const startSubtleMovement = () => {
  movementInterval = setInterval(() => {
    if (Math.random() > 0.8) {
      const randomIndex = Math.floor(Math.random() * props.text.length);
      const originalChar = props.text[randomIndex];
      
      // Momentarily flip to binary
      displayChars.value[randomIndex] = { value: Math.random() > 0.5 ? '0' : '1', isBinary: true };
      
      setTimeout(() => {
        displayChars.value[randomIndex] = { value: originalChar, isBinary: false };
      }, 150);
    }
  }, 200);
};

onMounted(() => {
  initDisplay();
  animationFrame = requestAnimationFrame(animate);
});

onUnmounted(() => {
  cancelAnimationFrame(animationFrame);
  if (movementInterval) clearInterval(movementInterval);
});
</script>

<style scoped>
.binary-text-container {
  display: inline-block;
}

.is-binary {
  color: #10b981; /* Emerald-500 */
  text-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
  opacity: 0.8;
}
</style>
