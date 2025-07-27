interface ProductSpecificationInterface {
    specification_id: number;
    product_id: number;
    specification_name: string;
    specification_value: string;
}

interface ProductSpecificationFormInterface {
    specification_name: string | null;
    specification_value: string | null;
}

interface ProductSpecificationFormErrorInterface {
    specification_name: string[];
    specification_value: string[];
}

export {
    ProductSpecificationInterface,
    ProductSpecificationFormInterface,
    ProductSpecificationFormErrorInterface,
};
