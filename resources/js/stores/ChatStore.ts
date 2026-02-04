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
}

export interface ChatMessage {
    type: 'bot' | 'user' | 'product_list';
    text?: string;
    data?: any; // For product list
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
                { label: "💻 Best Laptops", value: "laptops", action: 'fetch_products', payload: 'laptops' },
                { label: "📱 Phones & Tablets", value: "phones", action: 'fetch_products', payload: 'phones' }, // Placeholder for now
                { label: "🛡️ Warranty Info", value: "warranty", action: 'flow' },
                { label: "📍 Store Location", value: "location", action: 'flow' },
                { label: "🚚 Shipping", value: "shipping", action: 'flow' }
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
                }
                return;
            }

            this.isTyping = true;

            // Artificial delay for realism
            const delay = 800 + Math.random() * 500;

            if (option.action === 'fetch_products') {
                await this.handleFetchProducts(option.payload);
            } else {
                setTimeout(() => {
                    this.handleFlow(option.value);
                    this.isTyping = false;
                }, delay);
            }
        },

        async handleFetchProducts(category: string) {
            const service = useAxiosUtil<null, ProductInterface[]>();
            // Using 'best-selling' as a proxy for "Best Laptops" for now since we don't have a direct category ID mapping here without fetching categories first.
            // In a real scenario, we'd map "laptops" to ID 1, etc.
            // For now, let's just fetch best selling as a "recommendation"

            try {
                await service.get('best-selling'); // or products/search?category_id=...
                if (service.request.status === 200 && service.request.data) {
                    const products: ProductInterface[] = service.request.data;

                    setTimeout(() => {
                        this.isTyping = false;
                        this.addMessage({ text: "Here are some of our top picks based on what's popular right now:", type: 'bot' });
                        this.addMessage({ type: 'product_list', data: products.slice(0, 5) }); // Limit to 5

                        this.currentOptions = [
                            { label: "View All Products", value: "browse_link", action: 'link' },
                            { label: "⬅️ Back to Menu", value: "root", action: 'flow' }
                        ];
                    }, 500);
                }
            } catch (e) {
                this.isTyping = false;
                this.addMessage({ text: "I'm having trouble connecting to our product database right now. Please try browsing directly.", type: 'bot' });
                this.setMainOptions();
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
                        { label: "How to claim?", value: "claim_warranty", action: 'flow' },
                        { label: "⬅️ Back to Menu", value: "root", action: 'flow' }
                    ]
                },
                claim_warranty: {
                    text: "To claim warranty, simply bring the item and your receipt to our Davao branch, or contact our support team via Facebook.",
                    options: [
                        { label: "Store Location", value: "location", action: 'flow' },
                        { label: "Message Support", value: "facebook_link", action: 'link', payload: 'https://www.facebook.com/alcesslaptopstore' }
                    ]
                },
                location: {
                    text: "We are located at <b>Alcess Tech, Davao City</b>. Open Mon-Sat, 9AM to 6PM.",
                    options: [
                        { label: "⬅️ Back to Menu", value: "root", action: 'flow' }
                    ]
                },
                shipping: {
                    text: "We ship nationwide! 🚚 <br>• Metro Manila: 5-7 days <br>• Mindanao: 1-3 days <br>• Visayas: 3-5 days",
                    options: [
                        { label: "Check Rates", value: "rates", action: 'flow' },
                        { label: "⬅️ Back to Menu", value: "root", action: 'flow' }
                    ]
                },
                rates: {
                    text: "Shipping is free for orders above ₱50,000! For smaller items, it starts at ₱150.",
                    options: [
                        { label: "Start Shopping", value: "browse_link", action: 'link' },
                        { label: "⬅️ Back to Menu", value: "root", action: 'flow' }
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
                this.currentOptions.unshift({ label: "🚚 Shipping Rates", value: "shipping", action: 'flow' });
            } else if (routeName === 'customer.product-info.index') {
                this.currentOptions.unshift({ label: "🛡️ Warranty for this item", value: "warranty", action: 'flow' });
            }
        }
    }
});
