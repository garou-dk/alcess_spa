import { reactive, computed, readonly } from 'vue'

export interface ProgressiveLoadState {
  isLoading: boolean
  isPrimaryLoaded: boolean
  isSecondaryLoaded: boolean
  isComplete: boolean
  error: string | null
  retryCount: number
}

export interface LoadDataOptions {
  primaryLoader: () => Promise<any>
  secondaryLoader?: () => Promise<any>
  maxRetries?: number
  retryDelay?: number
}

/**
 * Composable for progressive data loading with non-blocking async pattern
 * Enables instant page loads by loading critical data first, then secondary data
 */
export function useProgressiveLoad() {
  // Reactive state for loading flags
  const state = reactive<ProgressiveLoadState>({
    isLoading: false,
    isPrimaryLoaded: false,
    isSecondaryLoaded: false,
    isComplete: false,
    error: null,
    retryCount: 0
  })

  // Computed properties for convenience
  const isLoading = computed(() => state.isLoading)
  const isPrimaryLoaded = computed(() => state.isPrimaryLoaded)
  const isSecondaryLoaded = computed(() => state.isSecondaryLoaded)
  const isComplete = computed(() => state.isComplete)
  const error = computed(() => state.error)
  const retryCount = computed(() => state.retryCount)

  // Store the current loaders for retry functionality
  let currentOptions: LoadDataOptions | null = null

  /**
   * Non-blocking loadData function that enables instant page loads
   * Initiates both primary and secondary data loading asynchronously
   */
  function loadData(options: LoadDataOptions): void {
    currentOptions = options
    state.isLoading = true
    state.error = null
    state.retryCount = 0

    // Start primary data loading immediately (non-blocking)
    loadPrimaryData(options.primaryLoader)

    // Start secondary data loading if provided (non-blocking)
    if (options.secondaryLoader) {
      loadSecondaryData(options.secondaryLoader)
    } else {
      // If no secondary loader, mark as complete when primary is done
      state.isSecondaryLoaded = true
      updateCompleteState()
    }
  }

  /**
   * Async function to load primary data (critical for initial page render)
   */
  async function loadPrimaryData(loader: () => Promise<any>): Promise<any> {
    try {
      const result = await loader()
      state.isPrimaryLoaded = true
      updateCompleteState()
      return result
    } catch (err) {
      state.error = err instanceof Error ? err.message : 'Failed to load primary data'
      state.isLoading = false
      throw err
    }
  }

  /**
   * Async function to load secondary data (non-critical, can load after primary)
   */
  async function loadSecondaryData(loader: () => Promise<any>): Promise<any> {
    try {
      const result = await loader()
      state.isSecondaryLoaded = true
      updateCompleteState()
      return result
    } catch (err) {
      // Secondary data failure doesn't block the page
      console.warn('Secondary data loading failed:', err)
      state.isSecondaryLoaded = true // Mark as "loaded" to not block completion
      updateCompleteState()
      throw err
    }
  }

  /**
   * Retry function with error handling and exponential backoff
   */
  async function retry(): Promise<void> {
    if (!currentOptions) {
      throw new Error('No previous load operation to retry')
    }

    const maxRetries = currentOptions.maxRetries || 3
    const retryDelay = currentOptions.retryDelay || 1000

    if (state.retryCount >= maxRetries) {
      throw new Error(`Maximum retry attempts (${maxRetries}) exceeded`)
    }

    state.retryCount++
    state.error = null

    // Exponential backoff delay
    const delay = retryDelay * Math.pow(2, state.retryCount - 1)
    await new Promise(resolve => setTimeout(resolve, delay))

    // Reset state for retry
    state.isPrimaryLoaded = false
    state.isSecondaryLoaded = false
    state.isComplete = false
    state.isLoading = true

    try {
      // Retry primary data loading
      await loadPrimaryData(currentOptions.primaryLoader)

      // Retry secondary data loading if it exists
      if (currentOptions.secondaryLoader) {
        await loadSecondaryData(currentOptions.secondaryLoader)
      } else {
        state.isSecondaryLoaded = true
        updateCompleteState()
      }
    } catch (err) {
      state.error = err instanceof Error ? err.message : 'Retry failed'
      state.isLoading = false
      throw err
    }
  }

  /**
   * Update the complete state when both primary and secondary data are loaded
   */
  function updateCompleteState(): void {
    if (state.isPrimaryLoaded && state.isSecondaryLoaded) {
      state.isComplete = true
      state.isLoading = false
    }
  }

  /**
   * Reset the state to initial values
   */
  function reset(): void {
    state.isLoading = false
    state.isPrimaryLoaded = false
    state.isSecondaryLoaded = false
    state.isComplete = false
    state.error = null
    state.retryCount = 0
    currentOptions = null
  }

  return {
    // State
    state: readonly(state),
    isLoading,
    isPrimaryLoaded,
    isSecondaryLoaded,
    isComplete,
    error,
    retryCount,
    // Methods
    loadData,
    loadPrimaryData,
    loadSecondaryData,
    retry,
    reset
  }
}

export default useProgressiveLoad
