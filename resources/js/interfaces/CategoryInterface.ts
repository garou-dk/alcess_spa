interface CategoryInterface {
    category_id: number;
    category_name: string;
    deleted_at: string | null;
    editable: number;
    category_image: string | null;
    category_slug: string;
    is_popular?: boolean | number;
    total_sales?: number;
}

interface CategoryFormInterface {
    category_name: string | null;
    category_image: null | File;
}

interface CategoryFormErrorInterface {
    category_name: string[];
    category_image: string[];
}

interface CategoryChangeNameFormInterface {
    category_name: string | null;
}

interface CategoryChangeNameFormErrorInterface {
    category_name: string[];
}

interface CategoryChangeImageFormInterface {
    category_image: null | File;
}

interface CategoryChangeImageFormErrorInterface {
    category_image: string[];
}

export {
    CategoryInterface,
    CategoryFormInterface,
    CategoryFormErrorInterface,
    CategoryChangeNameFormInterface,
    CategoryChangeNameFormErrorInterface,
    CategoryChangeImageFormInterface,
    CategoryChangeImageFormErrorInterface,
};
