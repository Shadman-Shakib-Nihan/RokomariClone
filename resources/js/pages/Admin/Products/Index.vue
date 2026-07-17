<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Pencil, Plus, Trash2, Search } from '@lucide/vue';
import { toast } from 'vue-sonner';
import { useDebounceFn } from '@vueuse/core';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardAction,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';

type Product = {
    id: number;
    name: string;
    slug: string;
    status: string;
    featured: boolean;
    barcode: string | null;
    category: { id: number; name: string } | null;
    brand: { id: number; name: string } | null;
    publisher: { id: number; name: string } | null;
};

type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
    prev_page_url: string | null;
    next_page_url: string | null;
};

type Option = {
    id: number;
    name: string;
};

type Props = {
    products: PaginatedData<Product>;
    filters: {
        search: string;
        status: string;
        category_id: string;
        brand_id: string;
        publisher_id: string;
    };
    categories: Option[];
    brands: Option[];
    publishers: Option[];
};

const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');
const statusFilter = ref<string | null>(props.filters.status ?? null);
const categoryFilter = ref<string | null>(props.filters.category_id ?? null);
const brandFilter = ref<string | null>(props.filters.brand_id ?? null);
const publisherFilter = ref<string | null>(props.filters.publisher_id ?? null);

watch(
    () => props.filters.search,
    (val) => {
        if (val !== search.value) {
            search.value = val ?? '';
        }
    },
);

const debouncedSearch = useDebounceFn((value: string) => {
    router.get(
        admin.products.index.url(),
        {
            search: value || undefined,
            status: statusFilter.value || undefined,
            category_id: categoryFilter.value || undefined,
            brand_id: brandFilter.value || undefined,
            publisher_id: publisherFilter.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 300);

watch(search, (val) => {
    debouncedSearch(val);
});

function applyFilters() {
    router.get(
        admin.products.index.url(),
        {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
            category_id: categoryFilter.value || undefined,
            brand_id: brandFilter.value || undefined,
            publisher_id: publisherFilter.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function confirmDelete(product: Product) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${product.name}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.products.destroy.url(product.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Product deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete product.',
                );
            }
        },
    });
}

function visitPage(url: string | null) {
    if (!url) return;
    router.get(url, {}, { preserveState: true, preserveScroll: true, replace: true });
}

const statusBadgeVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    active: 'default',
    draft: 'secondary',
    inactive: 'outline',
};
</script>

<template>
    <Head title="Products" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Products', href: admin.products.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Products" description="Manage your products" />

            <Card>
                <CardHeader>
                    <CardTitle>All Products</CardTitle>
                    <CardAction>
                        <Link :href="admin.products.create.url()">
                            <Button>
                                <Plus class="size-4" />
                                Create
                            </Button>
                        </Link>
                    </CardAction>
                </CardHeader>
                <CardContent>
                    <div class="mb-4 grid gap-3 md:grid-cols-5">
                        <div class="relative">
                            <Search class="text-muted-foreground absolute left-3 top-1/2 size-4 -translate-y-1/2" />
                            <Input
                                v-model="search"
                                placeholder="Search products..."
                                class="pl-9"
                            />
                        </div>

                        <Select v-model="statusFilter" @update:model-value="applyFilters">
                            <SelectTrigger>
                                <SelectValue placeholder="All statuses" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">All statuses</SelectItem>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="active">Active</SelectItem>
                                <SelectItem value="inactive">Inactive</SelectItem>
                            </SelectContent>
                        </Select>

                        <Select v-model="categoryFilter" @update:model-value="applyFilters">
                            <SelectTrigger>
                                <SelectValue placeholder="All categories" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">All categories</SelectItem>
                                <SelectItem
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="String(cat.id)"
                                >
                                    {{ cat.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <Select v-model="brandFilter" @update:model-value="applyFilters">
                            <SelectTrigger>
                                <SelectValue placeholder="All brands" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">All brands</SelectItem>
                                <SelectItem
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    :value="String(brand.id)"
                                >
                                    {{ brand.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <Select v-model="publisherFilter" @update:model-value="applyFilters">
                            <SelectTrigger>
                                <SelectValue placeholder="All publishers" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">All publishers</SelectItem>
                                <SelectItem
                                    v-for="publisher in publishers"
                                    :key="publisher.id"
                                    :value="String(publisher.id)"
                                >
                                    {{ publisher.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 pr-4 font-medium">Name</th>
                                    <th class="py-3 pr-4 font-medium">Category</th>
                                    <th class="py-3 pr-4 font-medium">Brand</th>
                                    <th class="py-3 pr-4 font-medium">Status</th>
                                    <th class="py-3 pr-4 font-medium">Featured</th>
                                    <th class="py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="product in products.data"
                                    :key="product.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4 font-medium">
                                        {{ product.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ product.category?.name ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ product.brand?.name ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge :variant="statusBadgeVariant[product.status] ?? 'secondary'">
                                            {{ product.status }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge :variant="product.featured ? 'default' : 'secondary'">
                                            {{ product.featured ? 'Yes' : 'No' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.products.edit.url(product.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(product)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No products found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="products.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ products.from }}–{{ products.to }} of
                            {{ products.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!products.prev_page_url"
                                @click="visitPage(products.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!products.next_page_url"
                                @click="visitPage(products.next_page_url)"
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
