export default class UrlUtil {
    public static getBaseAppUrl(params: string = ""): string {
        return `${import.meta.env.VITE_APP_URL}/${params}`;
    }

    public static getBaseApiUrl(params: string = ""): string {
        return `${import.meta.env.VITE_API_URL}/${params}`;
    }

    public static getParams(url: string): Record<string, string> {
        const searchParams = new URL(url).searchParams;
        const params: Record<string, string> = {};

        for (const [key, value] of searchParams.entries()) {
            params[key] = value;
        }

        return params;
    }
}
