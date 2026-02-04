import { defineStore } from "pinia";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import router from "@/router";

export interface ChatOption {
    label: string;
    value: string;
    action?: string; // 'fetch_products' | 'link' | 'flow'
    payload?: any;
    icon?: string;
}

export interface ChatMessage {
    type: 'bot' | 'user' | 'product_list' | 'product_detail';
    text?: string;
    data?: any; // For product list or detail
    time: string;
}

interface ChatState {
    isOpen: boolean;
    isTyping: boolean;
    messages: ChatMessage[];
    currentOptions: ChatOption[];
    hasInitialized: boolean;
}

export const useChatStore = defineStore("chat", {
    state: (): ChatState => ({
        isOpen: false,
        isTyping: false,
        messages: [],
        currentOptions: [],
        hasInitialized: false
    }),

    actions: {
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && !this.hasInitialized) {
                this.initializeChat();
            }
        },

        getCurrentTime() {
            return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        addMessage(msg: Partial<ChatMessage>) {
            this.messages.push({
                type: msg.type || 'bot',
                text: msg.text,
                data: msg.data,
                time: this.getCurrentTime()
            });
        },

        initializeChat() {
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
                this.addMessage({ text: "Hi there! 👋 Welcome to Alcess Tech. I'm your digital assistant. What are you looking for today?", type: 'bot' });
                this.setMainOptions();
                this.hasInitialized = true;
            }, 600);
        },

        setMainOptions() {
            this.currentOptions = [
                { label: "Best Laptops", value: "laptops", action: 'fetch_products', payload: 'laptops', icon: 'pi pi-desktop' },
                { label: "Browse All Products", value: "all", action: 'browse_all', icon: 'pi pi-search' },
                { label: "Warranty Info", value: "warranty", action: 'flow', icon: 'pi pi-shield' },
                { label: "Store Location", value: "location", action: 'flow', icon: 'pi pi-map-marker' },
                { label: "Shipping", value: "shipping", action: 'flow', icon: 'pi pi-truck' }
            ];
        },

        async handleOption(option: ChatOption) {
            // 1. Add User Message
            this.addMessage({ text: option.label, type: 'user' });
            this.currentOptions = [];

            // 2. Handle Action
            if (option.action === 'link') {
                if (option.value === 'browse_link') {
                    router.push({ name: 'customer.browse-products' });
                } else if (option.value === 'facebook_link') {
                    window.open(option.payload, '_blank');
                    this.setMainOptions();
                } else if (option.value === 'view_product_page') {
                    router.push({ name: 'customer.product-info.index', params: { id: option.payload } });
                }
                return;
            }

            this.isTyping = true;

            // Artificial delay for realism
            const delay = 800 + Math.random() * 500;

            if (option.action === 'fetch_products') {
                await this.handleFetchProducts(option.payload);
            } else if (option.action === 'browse_all') {
                await this.handleFetchProducts(null); // Fetch all/search endpoint
            } else if (option.action === 'view_details') {
                await this.fetchProductDetails(option.payload);
            } else {
                setTimeout(() => {
                    this.handleFlow(option.value);
                    this.isTyping = false;
                }, delay);
            }
        },

        async handleFetchProducts(category: string | null) {
            const service = useAxiosUtil<null, any>();

            try {
                let endpoint = 'best-selling';
                if (!category) endpoint = 'products/search?search=';

                await service.get(endpoint);

                if (service.request.status === 200 && service.request.data) {
                    let products: ProductInterface[] = [];

                    if (category && category !== 'all') {
                        // Some endpoints return array directly
                        products = service.request.data as unknown as ProductInterface[];
                    } else {
                        // Search endpoint usually returns { data: [...] } or just array depending on backend
                        const responseData = service.request.data;
                        products = Array.isArray(responseData) ? responseData : (responseData.data || []);
                    }

                    if (products.length === 0) {
                        this.addMessage({ text: "I couldn't find any products matching that criteria right now.", type: 'bot' });
                    } else {
                        const text = category
                            ? "Here are some top picks:"
                            : "Here are available products. Click one for full details:";

                        this.addMessage({ text: text, type: 'bot' });
                        this.addMessage({ type: 'product_list', data: products.slice(0, 10) });
                    }

                    this.currentOptions = [
                        { label: "View Full Catalog", value: "browse_link", action: 'link', icon: 'pi pi-th-large' },
                        { label: "Back to Menu", value: "root", action: 'flow', icon: 'pi pi-arrow-left' }
                    ];
                } else {
                    throw new Error("Failed to fetch products");
                }
            } catch (e) {
                console.error(e);
                this.addMessage({ text: "I'm having a little trouble reaching our catalog. You can browse directly here:", type: 'bot' });
                this.currentOptions = [{ label: "Browse Catalog", value: "browse_link", action: 'link', icon: 'pi pi-external-link' }];
            } finally {
                this.isTyping = false;
            }
        },

        async fetchProductDetails(productId: number) {
            const service = useAxiosUtil<null, ProductInterface>();
            try {
                // Using find-product endpoint as identified in plan
                await service.get(`find-product/${productId}`);

                if (service.request.status === 200 && service.request.data) {
                    const product = service.request.data;

                    setTimeout(() => {
                        this.isTyping = false;
                        this.addMessage({
                            type: 'product_detail',
                            data: product
                        });

                        this.currentOptions = [
                            { label: "View Full Page", value: "view_product_page", action: 'link', payload: productId, icon: 'pi pi-external-link' },
                            { label: "Back to Products", value: "all", action: 'browse_all', icon: 'pi pi-arrow-left' }
                        ];
                    }, 500);
                }
            } catch (e) {
                this.isTyping = false;
                this.addMessage({ text: "I couldn't load the details for that product. Trying opening its page directly.", type: 'bot' });
                this.currentOptions = [{ label: "View Page", value: "view_product_page", action: 'link', payload: productId, icon: 'pi pi-external-link' }];
            }
        },

        handleFlow(flowKey: string) {
            const flows: any = {
                root: {
                    text: "How else can I help you?",
                    options: [] // Will call setMainOptions
                },
                warranty: {
                    text: "All our brand new items come with a <b>1-year official manufacturer warranty</b>. We also offer a 7-day replacement for factory defects.",
                    options: [
                        { label: "How to claim?", value: "claim_warranty", action: 'flow', icon: 'pi pi-question-circle' },
                        { label: "Back to Menu", value: "root", action: 'flow', icon: 'pi pi-arrow-left' }
                    ]
                },
                claim_warranty: {
                    text: "To claim warranty, simply bring the item and your receipt to our Davao branch, or contact our support team via Facebook.",
                    options: [
                        { label: "Store Location", value: "location", action: 'flow', icon: 'pi pi-map-marker' },
                        { label: "Message Support", value: "facebook_link", action: 'link', payload: 'https://www.facebook.com/alcesslaptopstore', icon: 'pi pi-facebook' }
                    ]
                },
                location: {
                    text: "We are located at <b>Alcess Tech, Davao City</b>. Open Mon-Sat, 9AM to 6PM.",
                    options: [
                        { label: "Back to Menu", value: "root", action: 'flow', icon: 'pi pi-arrow-left' }
                    ]
                },
                shipping: {
                    text: "We ship nationwide! 🚚 <br>• Metro Manila: 5-7 days <br>• Mindanao: 1-3 days <br>• Visayas: 3-5 days",
                    options: [
                        { label: "Check Rates", value: "rates", action: 'flow', icon: 'pi pi-dollar' },
                        { label: "Back to Menu", value: "root", action: 'flow', icon: 'pi pi-arrow-left' }
                    ]
                },
                rates: {
                    text: "Shipping is free for orders above ₱50,000! For smaller items, it starts at ₱150.",
                    options: [
                        { label: "Start Shopping", value: "browse_link", action: 'link', icon: 'pi pi-shopping-bag' },
                        { label: "Back to Menu", value: "root", action: 'flow', icon: 'pi pi-arrow-left' }
                    ]
                }
            };

            const response = flows[flowKey] || flows.root;
            this.addMessage({ text: response.text, type: 'bot' });

            if (flowKey === 'root') {
                this.setMainOptions();
            } else {
                this.currentOptions = response.options;
            }
        },

        setContext(routeName: string) {
            if (!this.hasInitialized) return;

            // Reset to main options first
            this.setMainOptions();

            // Add context specific options
            if (routeName === 'customer.cart') {
                this.currentOptions.unshift({ label: "Shipping Rates", value: "shipping", action: 'flow', icon: 'pi pi-truck' });
            } else if (routeName === 'customer.product-info.index') {
                this.currentOptions.unshift({ label: "Warranty for this item", value: "warranty", action: 'flow', icon: 'pi pi-shield' });
            }
        },

        handleNotification(notification: any) {
            // Ensure chat is initialized so messages don't get lost or weird state
            if (!this.hasInitialized) {
                this.messages.push({
                    type: 'bot',
                    text: "Welcome back. I have an update regarding your order.",
                    time: this.getCurrentTime()
                });
                this.hasInitialized = true;
            }

            // Professional phrasing based on status/content
            let messageText = `<b>Update:</b> ${notification.title}<br/><span class="text-xs text-slate-500">${notification.message}</span>`;
            let options: ChatOption[] = [];

            // We can infer context from the message or adds logic if we had status codes. 
            // For now, assume most are order updates.

            this.addMessage({ text: messageText, type: 'bot' });

            options = [
                { label: "View Order Details", value: "view_order", action: 'link', icon: 'pi pi-file' },
                { label: "Track Shipment", value: "track_order", action: 'link', icon: 'pi pi-map' }
            ];

            // If we are currently "typing", stop it to show this urgent message
            this.isTyping = false;
            this.currentOptions = options;

            // Optional: Auto-open chat if closed? Maybe too intrusive. 
            // Let's just update the state. The user will see the badge (if we implemented one) or opens it.
            // But user requested "proactive", so opening it might be expected *if* it's important.
            // For now, let's keep it closed to not annoy, but ensuring it's ready when opened.
            // actually, let's set unread indicator logic if we had one.
            // Since we don't have a specific "unread" badge for chat icon yet (only the ping animation), 
            // the message will be there when they open it.
        }
    }
});
