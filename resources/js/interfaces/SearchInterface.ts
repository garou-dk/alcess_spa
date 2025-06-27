interface SearchInterface {
    search: string | null;
    page: number | null;
    limit: number | null;
}

interface SearchErrorInterface {
    search: string[];
    page: string[];
    limit: string[];
}

export { SearchInterface, SearchErrorInterface };
