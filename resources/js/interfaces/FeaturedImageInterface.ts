interface FeaturedImageInterface {
    featured_image_id: number;
    product_id: number;
    featured_image: string;
    thumbnail: string;
}

interface FeaturedImageFormInterface {
    featured_image: File | null;
}

interface FeaturedImageFormErrorInterface {
    featured_image: string[];
}

export {
    FeaturedImageInterface,
    FeaturedImageFormInterface,
    FeaturedImageFormErrorInterface,
};
