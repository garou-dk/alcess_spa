import { ref, onMounted, onUnmounted, computed, getCurrentInstance } from 'vue'

/**
 * Global responsive composable that preserves desktop layout completely
 * Only applies responsive changes to tablet and mobile devices
 */
export function useResponsive() {
  const width = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)
  const height = ref(typeof window !== 'undefined' ? window.innerHeight : 768)

  // Device categories - Desktop (1024px+) is preserved, only mobile/tablet get responsive treatment
  const isMobile = computed(() => width.value < 768)      // < 768px
  const isTablet = computed(() => width.value >= 768 && width.value < 1024) // 768px - 1023px  
  const isDesktop = computed(() => width.value >= 1024)   // >= 1024px (ORIGINAL LAYOUT PRESERVED)

  const updateDimensions = () => {
    if (typeof window !== 'undefined') {
      width.value = window.innerWidth
      height.value = window.innerHeight
    }
  }

  // Only setup event listeners if we're in a component context
  const instance = getCurrentInstance()
  if (instance) {
    onMounted(() => {
      updateDimensions()
      if (typeof window !== 'undefined') {
        window.addEventListener('resize', updateDimensions)
      }
    })

    onUnmounted(() => {
      if (typeof window !== 'undefined') {
        window.removeEventListener('resize', updateDimensions)
      }
    })
  } else {
    // If not in component context, just update once
    updateDimensions()
  }

  return {
    // Reactive values
    width,
    height,
    isMobile,
    isTablet,
    isDesktop,
    
    /**
     * Check if should use card layout instead of table
     */
    shouldUseCardLayout: computed(() => !isDesktop.value),
    
    /**
     * Get responsive classes - Desktop always returns desktop/default classes
     * Only mobile/tablet get special treatment
     */
    getResponsiveClasses: (classes: {
      mobile?: string
      tablet?: string
      desktop?: string
      default?: string
    }) => {
      // Desktop always uses desktop or default classes (preserves original layout)
      if (isDesktop.value) {
        return classes.desktop || classes.default || ''
      }
      // Only mobile/tablet get responsive treatment
      if (isMobile.value && classes.mobile) return classes.mobile
      if (isTablet.value && classes.tablet) return classes.tablet
      return classes.default || ''
    },

    /**
     * Responsive padding - Desktop unchanged, mobile/tablet optimized
     */
    getResponsivePadding: () => {
      if (isDesktop.value) return 'p-5' // Original desktop padding
      if (isTablet.value) return 'p-4'  // Tablet padding
      return 'p-3' // Mobile padding
    },

    /**
     * Responsive margins - Desktop unchanged, mobile/tablet optimized  
     */
    getResponsiveMargin: () => {
      if (isDesktop.value) return 'mb-5' // Original desktop margin
      if (isTablet.value) return 'mb-4'  // Tablet margin
      return 'mb-3' // Mobile margin
    },

    /**
     * Responsive text sizes - Desktop unchanged, mobile/tablet optimized
     */
    getResponsiveTextSize: (baseSize: 'sm' | 'base' | 'lg' | 'xl' = 'base') => {
      if (isDesktop.value) {
        // Desktop keeps original sizes
        const desktopSizes = {
          sm: 'text-sm',
          base: 'text-base', 
          lg: 'text-lg',
          xl: 'text-xl'
        }
        return desktopSizes[baseSize]
      }
      
      // Mobile/tablet get smaller sizes
      if (isMobile.value) {
        const mobileSizes = {
          sm: 'text-xs',
          base: 'text-sm',
          lg: 'text-base', 
          xl: 'text-lg'
        }
        return mobileSizes[baseSize]
      }
      
      // Tablet sizes
      const tabletSizes = {
        sm: 'text-sm',
        base: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl'
      }
      return tabletSizes[baseSize]
    },

    /**
     * Responsive gaps - Desktop unchanged, mobile/tablet optimized
     */
    getResponsiveGap: () => {
      if (isDesktop.value) return 'gap-5' // Original desktop gap
      if (isTablet.value) return 'gap-4'  // Tablet gap
      return 'gap-3' // Mobile gap
    },

    /**
     * Hide columns on mobile/tablet while keeping desktop unchanged
     */
    getColumnVisibility: (hideOn: 'mobile' | 'tablet' | 'both' = 'mobile') => {
      if (hideOn === 'mobile') return 'hidden sm:table-cell'
      if (hideOn === 'tablet') return 'hidden lg:table-cell'
      if (hideOn === 'both') return 'hidden lg:table-cell'
      return ''
    }
  }
}