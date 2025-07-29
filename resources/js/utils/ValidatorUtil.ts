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

    public static isImageOrMp4(file: File): string | false {
        const allowedFormats = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "video/mp4",
        ];
        return allowedFormats.includes(file.type) ? file.type : false;
    }

    public static checkSizeValidation(file: File, size: number): boolean {
        const maxFileSize = size * 1024 * 1024;
        return file.size <= maxFileSize;
    }

    public static getFileType(fileName: string): (string | null) {
        const parts = fileName.split('.');

        const extension = parts.length > 1 ? parts.pop()?.toLowerCase() : null;
        
        const fileTypes: Record<string, string> = {
            'jpg': 'image',
            'jpeg': 'image',
            'png': 'image',
            'mp4': 'video',
        };

        return extension && fileTypes[extension] ? fileTypes[extension] : null;
    }
}

export default ValidatorUtil;
