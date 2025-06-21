enum RoleEnum {
    ADMIN = 'Admin',
    STAFF = 'Staff',
    CUSTOMER = 'Customer'
};

const getStoreRoles = () : RoleEnum[] => {
    return [
        RoleEnum.ADMIN,
        RoleEnum.STAFF,
    ]
}

const getStoreCustomers = () : (RoleEnum | null)[] => {
    return [
        RoleEnum.CUSTOMER,
        null,
    ];
}

export {
    RoleEnum, getStoreRoles, getStoreCustomers
}