import { RoleInterface } from "@/interfaces/RoleInterface";

interface UserInterface {
    user_id: number;
    full_name: string;
    email: string;
    email_verified_at: string;
    role_id: string;
    image: string | null;
    role: RoleInterface;
}

export { UserInterface };