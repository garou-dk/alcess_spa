export default class DateUtil {
    public static formatToMonthDayYear(text: string): string {
        const date = new Date(text);
        const month = date.toLocaleString("default", { month: "long" });
        const day = date.getDate();
        const year = date.getFullYear();
        return `${month} ${day}, ${year}`;
    }

    public static timeString(date: Date): string {
        const hour = date.getHours();
        const minute = date.getMinutes();
        const hourString = hour < 10 ? `0${hour}` : `${hour}`;
        const minuteString = minute < 10 ? `0${minute}` : `${minute}`;
        return `${hourString}:${minuteString}`;
    }

    public static formatYYYYMMDD(date: Date): string {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        const localDateString = `${year}-${month}-${day}`;
        return localDateString;
    }

    public static timeStringAMPM(time: string): string {
        const [hour, minute] = time.split(":").map(Number);
        const ampm = hour >= 12 ? "PM" : "AM";
        const hourString = (hour % 12 || 12).toString().padStart(2, "0");
        const minuteString = minute.toString().padStart(2, "0");
        return `${hourString}:${minuteString} ${ampm}`;
    }

    public static getYear(date: Date): number {
        return date.getFullYear();
    }

    public static getMonth(date: Date): number {
        return date.getMonth() + 1;
    }

    public static getFirstDayOfMonth(date: Date): Date {
        return new Date(date.getFullYear(), date.getMonth(), 1);
    }

    public static getLastDayOfMonth(date: Date): Date {
        return new Date(date.getFullYear(), date.getMonth() + 1, 0);
    }

    public static formatToMonthDayYearTime(text: string): string {
        const date = new Date(text);
        const month = date.toLocaleString("default", { month: "long" });
        const day = date.getDate();
        const year = date.getFullYear();
        const hour = date.getHours();
        const minute = date.getMinutes();
        const ampm = hour >= 12 ? "PM" : "AM";
        const hourString = (hour % 12 || 12).toString();
        const minuteString = minute.toString().padStart(2, "0");
        return `${month} ${day}, ${year} at ${hourString}:${minuteString} ${ampm}`;
    }
}
