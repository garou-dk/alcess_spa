interface ChangeUserStatusFormInterface {
    is_active: number | null;
}

interface ChangeUserStatusFormErrorInterface {
    is_active: string[];
}

export { ChangeUserStatusFormInterface, ChangeUserStatusFormErrorInterface };
