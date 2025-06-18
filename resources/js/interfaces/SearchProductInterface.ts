interface SearchProductInterface {
    search: string | null;
}

interface SearchErrorInterface {
    search: string[];
}

export { SearchProductInterface, SearchErrorInterface };