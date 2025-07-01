interface ChangeProfileFormInterface {
    image: File | null;
}

interface ChangeProfileFormErrorInterface {
    image: string[];
}

export { ChangeProfileFormInterface, ChangeProfileFormErrorInterface };
