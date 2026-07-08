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

type Category = {
    id: number;
    parent_id: number | null;
    name: string;
    slug: string;
    image_url: string | null;
    sort_order: number;
    is_active: boolean;
    parent: { id: number; name: string } | null;
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
    categories: PaginatedData<Category>;
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
        admin.categories.index.url(),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 300);

watch(search, (val) => {
    debouncedSearch(val);
});

function confirmDelete(category: Category) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${category.name}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.categories.destroy.url(category.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Category deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete category.',
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
    <Head title="Categories" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Categories', href: admin.categories.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Categories" description="Manage your product categories" />

            <Card>
                <CardHeader>
                    <CardTitle>All Categories</CardTitle>
                    <CardAction>
                        <Link :href="admin.categories.create.url()">
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
                                placeholder="Search categories..."
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 pr-4 font-medium">Image</th>
                                    <th class="py-3 pr-4 font-medium">Name</th>
                                    <th class="py-3 pr-4 font-medium">Parent</th>
                                    <th class="py-3 pr-4 font-medium">Slug</th>
                                    <th class="py-3 pr-4 font-medium">Status</th>
                                    <th class="py-3 pr-4 font-medium">Order</th>
                                    <th class="py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="category in categories.data"
                                    :key="category.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4">
                                        <img
                                            v-if="category.image_url"
                                            :src="category.image_url"
                                            :alt="category.name"
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
                                        {{ category.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ category.parent?.name ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ category.slug }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge
                                            :variant="category.is_active ? 'default' : 'secondary'"
                                        >
                                            {{ category.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ category.sort_order }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.categories.edit.url(category.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(category)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="categories.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No categories found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="categories.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ categories.from }}–{{ categories.to }} of
                            {{ categories.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!categories.prev_page_url"
                                @click="visitPage(categories.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!categories.next_page_url"
                                @click="visitPage(categories.next_page_url)"
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
