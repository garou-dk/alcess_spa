class ValidatorUtil {
    public static isEmpty(value: unknown | unknown[]): boolean {
        return !value;
    }

    public static isInteger(value: unknown): boolean {
        return Number.isInteger(value as number);
    }

    public static isValidPhoneNumber(value: string): boolean {
        const regex = /^09\d{9}$/;
        return regex.test(value);
    }
}

export default ValidatorUtil;
