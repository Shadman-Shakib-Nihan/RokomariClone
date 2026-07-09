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

type Publisher = {
    id: number;
    name: string;
    slug: string;
    logo: string | null;
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
    publishers: PaginatedData<Publisher>;
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
        admin.publishers.index.url(),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 300);

watch(search, (val) => {
    debouncedSearch(val);
});

function logoUrl(publisher: Publisher): string | null {
    if (!publisher.logo) return null;
    if (publisher.logo.startsWith('http')) return publisher.logo;
    return `/storage/${publisher.logo}`;
}

function confirmDelete(publisher: Publisher) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${publisher.name}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.publishers.destroy.url(publisher.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Publisher deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete publisher.',
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
    <Head title="Publishers" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Publishers', href: admin.publishers.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Publishers" description="Manage your publishers" />

            <Card>
                <CardHeader>
                    <CardTitle>All Publishers</CardTitle>
                    <CardAction>
                        <Link :href="admin.publishers.create.url()">
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
                                placeholder="Search publishers..."
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
                                    v-for="publisher in publishers.data"
                                    :key="publisher.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4">
                                        <img
                                            v-if="logoUrl(publisher)"
                                            :src="logoUrl(publisher)!"
                                            :alt="publisher.name"
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
                                        {{ publisher.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ publisher.slug }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        <a
                                            v-if="publisher.website"
                                            :href="publisher.website"
                                            target="_blank"
                                            class="hover:text-primary underline underline-offset-2"
                                        >
                                            {{ publisher.website }}
                                        </a>
                                        <span v-else>—</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge
                                            :variant="publisher.is_active ? 'default' : 'secondary'"
                                        >
                                            {{ publisher.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.publishers.edit.url(publisher.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(publisher)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="publishers.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No publishers found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="publishers.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ publishers.from }}–{{ publishers.to }} of
                            {{ publishers.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!publishers.prev_page_url"
                                @click="visitPage(publishers.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!publishers.next_page_url"
                                @click="visitPage(publishers.next_page_url)"
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