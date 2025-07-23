import {
    SearchErrorInterface,
    SearchInterface,
} from "@/interfaces/SearchInterface";
import { UnitInterface } from "@/interfaces/UnitInterface";
import { CategoryInterface } from "@/interfaces/CategoryInterface";
import { ProductSpecificationInterface } from "@/interfaces/ProductSpecificationInterface";

interface ProductSearchInterface extends SearchInterface {
    category_id: number | null;
    status: number | null;
    low_stock: boolean | null;
    available_online: boolean | null;
}

interface ProductSearchErrorInterface extends SearchErrorInterface {
    category_id: string[];
    status: string[];
    low_stock: string[];
    available_online: string[];
}

interface ProductInterface {
    product_id: number;
    product_name: string;
    description: string;
    category_id: number;
    unit_id: number;
    product_image: string | null;
    product_price: number;
    product_quantity: number;
    low_stock_threshold: number;
    is_active: number;
    sku: string | null;
    available_online: number;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
    unit: UnitInterface;
    category: CategoryInterface;
    specifications: ProductSpecificationInterface[];
}

interface AddProductFormInterface {
    product_name: string | null;
    description: string | null;
    category_id: number | null;
    unit_id: number | null;
    product_price: number | null;
    product_quantity: number | null;
    low_stock_threshold: number | null;
    is_active: number | null;
    sku: string | null;
    available_online: number | null;
}

interface AddProductFormErrorInterface {
    product_name: string[];
    description: string[];
    category_id: string[];
    unit_id: string[];
    product_price: string[];
    product_quantity: string[];
    low_stock_threshold: string[];
    is_active: string[];
    sku: string[];
    available_online: string[];
}

interface UpdateProductImageFormInterface {
    product_image: File | null;
}

interface UpdateProductImageFormErrorInterface {
    product_image: string[];
}

export {
    ProductSearchInterface,
    ProductSearchErrorInterface,
    ProductInterface,
    AddProductFormInterface,
    AddProductFormErrorInterface,
    UpdateProductImageFormInterface,
    UpdateProductImageFormErrorInterface,
};