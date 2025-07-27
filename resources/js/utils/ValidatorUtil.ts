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

    public static isValidImageFormat(file: File): boolean {
        const allowedFormats = ["image/jpeg", "image/jpg", "image/png"];
        return allowedFormats.includes(file.type);
    }

    public static isValidImageSize(file: File): boolean {
        const maxFileSize = 2 * 1024 * 1024;
        return file.size <= maxFileSize;
    }

    public static isImageOrMp4 (file: File): string | false {
        const allowedFormats = ["image/jpeg", "image/jpg", "image/png", "video/mp4"];
        return allowedFormats.includes(file.type) ? file.type : false;
    }

    public static checkSizeValidation(file: File, size: number): boolean {
        const maxFileSize = size * 1024 * 1024;
        return file.size <= maxFileSize;
    }
}

export default ValidatorUtil;
