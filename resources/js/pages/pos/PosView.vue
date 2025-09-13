<template>
    <div>
        <div class="grid grid-cols-2 gap-4 px-7 py-5 md:grid-cols-3">
            <div class="rounded bg-white p-4 shadow">
                <p class="text-lg text-gray-500">Daily Sales</p>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="rounded bg-white p-4 shadow">
                <p class="text-lg text-gray-500">Transaction Today</p>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="rounded bg-white p-4 shadow">
                <p class="text-lg text-gray-500">Items in cart</p>
                <p class="text-3xl font-bold text-orange-500">0</p>
            </div>
        </div>
        <div class="flex flex-wrap px-7 py-5">
            <div class="w-full p-2 lg:w-3/5">
                <div class="rounded-lg bg-white p-4 shadow-md">
                    <form id="search-form" class="flex flex-wrap">
                        <div class="grow p-2">
                            <InputForm
                                :errors="[]"
                                id="search"
                                label-name="Search"
                                tag="label"
                            >
                                <InputGroup>
                                    <InputGroupAddon>
                                        <i class="pi pi-search" />
                                    </InputGroupAddon>
                                    <InputText
                                        id="search"
                                        placeholder="Search by Product Name or ID"
                                        fluid
                                    />
                                </InputGroup>
                            </InputForm>
                        </div>
                        <div class="p-2">
                            <InputForm
                                :errors="[]"
                                id="category"
                                label-name="Category"
                                tag="label"
                            >
                                <Select
                                    id="category"
                                    placeholder="All Categories"
                                    show-clear
                                />
                            </InputForm>
                        </div>
                    </form>

                    <hr class="mb-4" />

                    <div
                        id="results"
                        class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3"
                    ></div>
                </div>
            </div>
            <div class="w-full p-2 lg:w-2/5">
                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div id="cart" class="flex h-full flex-col">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Cart</h3>
                            <span class="text-sm text-gray-500"
                                >Items:
                                <span id="cart-item-count">0</span></span
                            >
                        </div>

                        <div class="mb-2">
                            <InputForm
                                :errors="[]"
                                id="payment-method"
                                label-name="Payment Method"
                                tag="label"
                                class="mb-2"
                            >
                                <Select
                                    :options="[
                                        { label: 'Cash', value: 'cash' },
                                        { label: 'GCash', value: 'gcash' },
                                        { label: 'Credit', value: 'credit' },
                                        { label: 'Debit', value: 'debit' },
                                        { label: 'Maya', value: 'maya' },
                                        {
                                            label: 'Home Credit',
                                            value: 'home credit',
                                        },
                                    ]"
                                    placeholder="Select Payment Method"
                                    id="payment-method"
                                    option-label="label"
                                    option-value="value"
                                />
                            </InputForm>
                            <p class="text-sm text-gray-500">
                                Subtotal: <span id="cart-subtotal">0.00</span>
                            </p>
                            <p class="text-lg font-semibold">
                                Total: <span id="cart-total">0.00</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts"></script>
