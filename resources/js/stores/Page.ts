import { UserInterface } from "@/interfaces/UserInterface";
import { reactive } from "vue";

const Page: {
    loaded: boolean;
    user: UserInterface | null;
    sidebar: boolean;
    menu: boolean;
    triggerReload: boolean;
} = reactive({
    loaded: false,
    user: null,
    sidebar: localStorage.getItem("sidebar") === "true",
    menu: localStorage.getItem("static") === "true",
    triggerReload: false,
});

export default Page;
