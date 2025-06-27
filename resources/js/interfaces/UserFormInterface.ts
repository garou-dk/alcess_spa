interface UserFormInterface {
    full_name: string | null;
    email: string | null;
    password: string | null;
    password_confirmation: string | null;
    image: null | File;
    role_id: string | null;
}

interface UserFormErrorInterface {
    full_name: string[];
    email: string[];
    password: string[];
    password_confirmation: string[];
    image: string[];
    role_id: string[];
}

export { UserFormInterface, UserFormErrorInterface };
