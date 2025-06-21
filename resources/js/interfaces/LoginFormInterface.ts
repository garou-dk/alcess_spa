interface LoginFormInterface {
    email: string | null;
    password: string | null;
}

interface LoginFormErrorInterface {
    email: string[];
    password: string[];
}

export { LoginFormInterface, LoginFormErrorInterface };
