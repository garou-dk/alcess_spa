export default class CurrencyUtil {
    public static formatCurrency(value: number): string {
        return value.toLocaleString("en-US", {
            style: "currency",
            currency: "PHP",
        });
    }
}
