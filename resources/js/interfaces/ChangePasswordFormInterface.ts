interface ChangePasswordFormInterface {
    password: string | null
    password_confirmation: string | null
}

interface ChangePasswordFormErrorInterface {
    password: string[]
    password_confirmation: string[]
}

export { ChangePasswordFormInterface, ChangePasswordFormErrorInterface };