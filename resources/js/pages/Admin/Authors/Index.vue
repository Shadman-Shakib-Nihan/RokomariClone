<script setup lang="ts">
/**
 * ── DATA FLOW OVERVIEW ──────────────────────────────────────────
 *
 * 1. This page is rendered by the `index()` method in:
 *    app/Http/Controllers/Admin/AuthorController.php:20
 *    → Queries the `authors` table with optional search (WHERE name LIKE ?)
 *    → Paginates results (10 per page), ordered by latest first
 *    → Returns `{ authors: PaginatedData<Author>, filters: { search } }`
 *
 * 2. Search input triggers debounced GET requests to:
 *    Route: `admin.authors.index` → GET /admin/authors?search=...
 *    → The controller re-queries and re-renders this page.
 *
 * 3. Delete sends DELETE to:
 *    Route: `admin.authors.destroy` → DELETE /admin/authors/{author}
 *    → AuthorController::destroy() removes the record from the `authors` table.
 *
 * 4. "Create" links to:  admin.authors.create  →  GET /admin/authors/create
 *    "Edit"   links to:  admin.authors.edit    →  GET /admin/authors/{author}/edit
 *
 * ── COMPONENT STRUCTURE ─────────────────────────────────────────
 */

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

// ── TYPES ───────────────────────────────────────────────────────

type Author = {
    id: number;
    name: string;
    slug: string;
    photo: string | null;
    biography: string | null;
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
    authors: PaginatedData<Author>;
    filters: {
        search: string;
    };
};

const props = defineProps<Props>();

// ── SEARCH ──────────────────────────────────────────────────────
// Debounced search input. On change, re-fetches the index page
// with the search query parameter → AuthorController::index()
// → WHERE name LIKE '%search%' → paginated results.

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
        admin.authors.index.url(),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 300);

watch(search, (val) => {
    debouncedSearch(val);
});

// ── HELPERS ─────────────────────────────────────────────────────

function photoUrl(author: Author): string | null {
    if (!author.photo) return null;
    if (author.photo.startsWith('http')) return author.photo;
    return `/storage/${author.photo}`;
}

function confirmDelete(author: Author) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${author.name}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.authors.destroy.url(author.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Author deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete author.',
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
    <Head title="Authors" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Authors', href: admin.authors.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Authors" description="Manage your book authors" />

            <Card>
                <CardHeader>
                    <CardTitle>All Authors</CardTitle>
                    <CardAction>
                        <Link :href="admin.authors.create.url()">
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
                                placeholder="Search authors..."
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 pr-4 font-medium">Photo</th>
                                    <th class="py-3 pr-4 font-medium">Name</th>
                                    <th class="py-3 pr-4 font-medium">Slug</th>
                                    <th class="py-3 pr-4 font-medium">Biography</th>
                                    <th class="py-3 pr-4 font-medium">Status</th>
                                    <th class="py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="author in authors.data"
                                    :key="author.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4">
                                        <img
                                            v-if="photoUrl(author)"
                                            :src="photoUrl(author)!"
                                            :alt="author.name"
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
                                        {{ author.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ author.slug }}
                                    </td>
                                    <td class="max-w-xs truncate py-3 pr-4 text-muted-foreground">
                                        {{ author.biography || '—' }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge
                                            :variant="author.is_active ? 'default' : 'secondary'"
                                        >
                                            {{ author.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.authors.edit.url(author.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(author)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="authors.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No authors found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="authors.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ authors.from }}–{{ authors.to }} of
                            {{ authors.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!authors.prev_page_url"
                                @click="visitPage(authors.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!authors.next_page_url"
                                @click="visitPage(authors.next_page_url)"
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
