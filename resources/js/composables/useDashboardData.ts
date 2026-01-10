import { ref, reactive } from 'vue';
import { getCurrentInstance } from 'vue';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useEcho } from '@laravel/echo-vue';

interface DashboardData {
    revenue: any;
    pending_orders: any;
    inventory_value: any;
    top_sellers: any;
    monthly_report: any;
    category_sales: any;
    stock_in_out: any;
    pending_orders_list: any;
}

const dashboardData = reactive<DashboardData>({
    revenue: null,
    pending_orders: null,
    inventory_value: null,
    top_sellers: null,
    monthly_report: null,
    category_sales: null,
    stock_in_out: null,
    pending_orders_list: null,
});

const isLoaded = ref(false);
const isLoading = ref(false);
const error = ref<string | null>(null);
const isUpdating = ref(false); // Visual indicator for real-time updates

// WebSocket connection for real-time updates
let echoConnection: any = null;
let refreshInterval: number | null = null;

export function useDashboardData() {
    const loadService = useAxiosUtil<null, DashboardData>();

    const loadDashboardData = async () => {
        if (isLoaded.value) {
            return; // Data already loaded, use cached version
        }

        isLoading.value = true;
        error.value = null;

        try {
            await loadService.get('admin/dashboard/aggregated');
            
            if (loadService.request.status === 200 && loadService.request.data) {
                Object.assign(dashboardData, loadService.request.data);
                isLoaded.value = true;
                
                // Setup WebSocket listeners after initial load
                setupWebSocketListeners();
                
                // Setup periodic refresh as backup (every 2 minutes)
                setupPeriodicRefresh();
            } else {
                error.value = loadService.request.message ?? 'Failed to load dashboard data';
            }
        } catch (err) {
            error.value = 'An error occurred while loading dashboard data';
        } finally {
            isLoading.value = false;
        }
    };

    const refreshDashboardData = async () => {
        isLoaded.value = false;
        await loadDashboardData();
    };

    const setupWebSocketListeners = () => {
        if (echoConnection) {
            return; // Already setup
        }

        try {
            // Only setup WebSocket if we're in a component context
            if (typeof getCurrentInstance === 'function' && getCurrentInstance()) {
                echoConnection = useEcho(
                    'dashboard-stats',
                    ['.dashboard.stats.update'],
                    (data: any) => {
                        // Show update indicator
                        isUpdating.value = true;
                        
                        // Update dashboard data in real-time
                        Object.assign(dashboardData, data);
                        
                        // Hide update indicator after 2 seconds
                        setTimeout(() => {
                            isUpdating.value = false;
                        }, 2000);
                    }
                );
            }
            // Removed the warning as it's not an error - just means no real-time updates for this instance
        } catch (err) {
            console.warn('WebSocket connection failed, dashboard will not update in real-time:', err);
        }
    };

    const setupPeriodicRefresh = () => {
        if (refreshInterval) {
            return; // Already setup
        }

        // Refresh dashboard stats every 2 minutes as backup
        refreshInterval = window.setInterval(async () => {
            try {
                const statsService = useAxiosUtil<null, DashboardData>();
                await statsService.get('admin/dashboard/aggregated');
                
                if (statsService.request.status === 200 && statsService.request.data) {
                    // Update all dashboard data
                    Object.assign(dashboardData, statsService.request.data);
                }
            } catch (err) {
                console.warn('Periodic dashboard refresh failed:', err);
            }
        }, 120000); // 2 minutes
    };

    const cleanup = () => {
        if (echoConnection && echoConnection.leave) {
            echoConnection.leave();
            echoConnection = null;
        }
        
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    };

    return {
        dashboardData,
        isLoaded,
        isLoading,
        isUpdating,
        error,
        loadDashboardData,
        refreshDashboardData,
        cleanup,
    };
}
