import { RoleInterface } from "@/interfaces/RoleInterface";
import {
    SearchErrorInterface,
    SearchInterface,
} from "@/interfaces/SearchInterface";

interface UserInterface {
    user_id: number;
    full_name: string;
    email: string;
    email_verified_at: string;
    role_id: number;
    image: string | null;
    is_active: boolean;
    role: RoleInterface;
}

interface UserSearchInterface extends SearchInterface {
    role_id: number | null;
    status: number | null;
}

interface UserSearchErrorInterface extends SearchErrorInterface {
    role_id: string[];
    status: string[];
}

export { UserInterface, UserSearchInterface, UserSearchErrorInterface };
