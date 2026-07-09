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
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';

type Brand = {
    id: number;
    name: string;
    slug: string;
    logo_url: string | null;
    description: string | null;
    website: string | null;
    is_active: boolean;
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

type Props = {
    brands: PaginatedData<Brand>;
    filters: {
        search: string;
    };
};

const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');

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
        admin.brands.index.url(),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 300);

watch(search, (val) => {
    debouncedSearch(val);
});

function confirmDelete(brand: Brand) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${brand.name}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.brands.destroy.url(brand.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Brand deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete brand.',
                );
            }
        },
    });
}

function visitPage(url: string | null) {
    if (!url) return;
    router.get(url, {}, { preserveState: true, preserveScroll: true, replace: true });
}
</script>

<template>
    <Head title="Brands" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Brands', href: admin.brands.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Brands" description="Manage your product brands" />

            <Card>
                <CardHeader>
                    <CardTitle>All Brands</CardTitle>
                    <CardAction>
                        <Link :href="admin.brands.create.url()">
                            <Button>
                                <Plus class="size-4" />
                                Create
                            </Button>
                        </Link>
                    </CardAction>
                </CardHeader>
                <CardContent>
                    <div class="mb-4">
                        <div class="relative">
                            <Search class="text-muted-foreground absolute left-3 top-1/2 size-4 -translate-y-1/2" />
                            <Input
                                v-model="search"
                                placeholder="Search brands..."
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 pr-4 font-medium">Logo</th>
                                    <th class="py-3 pr-4 font-medium">Name</th>
                                    <th class="py-3 pr-4 font-medium">Slug</th>
                                    <th class="py-3 pr-4 font-medium">Website</th>
                                    <th class="py-3 pr-4 font-medium">Status</th>
                                    <th class="py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="brand in brands.data"
                                    :key="brand.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4">
                                        <img
                                            v-if="brand.logo_url"
                                            :src="brand.logo_url"
                                            :alt="brand.name"
                                            class="size-10 rounded-lg border object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex size-10 items-center justify-center rounded-lg border bg-muted text-xs text-muted-foreground"
                                        >
                                            —
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4 font-medium">
                                        {{ brand.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ brand.slug }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        <a
                                            v-if="brand.website"
                                            :href="brand.website"
                                            target="_blank"
                                            class="hover:text-primary underline underline-offset-2"
                                        >
                                            {{ brand.website }}
                                        </a>
                                        <span v-else>—</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge
                                            :variant="brand.is_active ? 'default' : 'secondary'"
                                        >
                                            {{ brand.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.brands.edit.url(brand.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(brand)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="brands.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No brands found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="brands.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ brands.from }}–{{ brands.to }} of
                            {{ brands.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!brands.prev_page_url"
                                @click="visitPage(brands.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!brands.next_page_url"
                                @click="visitPage(brands.next_page_url)"
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