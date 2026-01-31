import { RoleEnum } from "@/enums/RoleEnum";
import ProfileView from "@/pages/customer/ProfileView.vue";

export default {
    path: "profile",
    name: "customer.profile",
    component: ProfileView,
    meta: {
        access: [RoleEnum.CUSTOMER],
        pageName: "Profile",
        pageSubName: "Manage your account security",
    },
};
