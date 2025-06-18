export default class StrUtil {
    public static padStart(value: number, length: number, pad: string): string {
        return value.toString().padStart(length, pad);
    }
}
