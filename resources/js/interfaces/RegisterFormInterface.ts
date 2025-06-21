interface RegisterFormInterface {
    full_name: string | null;
    email: string | null;
    password: string | null;
    password_confirmation: string | null;
}

interface RegisterFormErrorInterface {
    full_name: string[];
    email: string[];
    password: string[];
    password_confirmation: string[];
}

export { RegisterFormInterface, RegisterFormErrorInterface };
