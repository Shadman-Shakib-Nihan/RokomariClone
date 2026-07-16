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

type Attribute = {
    id: number;
    name: string;
    slug: string;
    input_type: string;
    unit: string | null;
    options_count: number;
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
    attributes: PaginatedData<Attribute>;
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
        admin.attributes.index.url(),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 300);

watch(search, (val) => {
    debouncedSearch(val);
});

function confirmDelete(attribute: Attribute) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${attribute.name}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.attributes.destroy.url(attribute.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Attribute deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete attribute.',
                );
            }
        },
    });
}

function visitPage(url: string | null) {
    if (!url) return;
    router.get(url, {}, { preserveState: true, preserveScroll: true, replace: true });
}

const inputTypeLabels: Record<string, string> = {
    text: 'Text',
    select: 'Select',
    boolean: 'Yes/No',
    date: 'Date',
    number: 'Number',
};
</script>

<template>
    <Head title="Attributes" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Attributes', href: admin.attributes.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Attributes" description="Manage your product attributes" />

            <Card>
                <CardHeader>
                    <CardTitle>All Attributes</CardTitle>
                    <CardAction>
                        <Link :href="admin.attributes.create.url()">
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
                                placeholder="Search attributes..."
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 pr-4 font-medium">Name</th>
                                    <th class="py-3 pr-4 font-medium">Slug</th>
                                    <th class="py-3 pr-4 font-medium">Input Type</th>
                                    <th class="py-3 pr-4 font-medium">Unit</th>
                                    <th class="py-3 pr-4 font-medium">Options</th>
                                    <th class="py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="attribute in attributes.data"
                                    :key="attribute.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4 font-medium">
                                        {{ attribute.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ attribute.slug }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge variant="outline">
                                            {{ inputTypeLabels[attribute.input_type] ?? attribute.input_type }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ attribute.unit ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <Badge variant="secondary">
                                            {{ attribute.options_count }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.attributes.edit.url(attribute.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(attribute)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="attributes.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No attributes found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="attributes.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ attributes.from }}–{{ attributes.to }} of
                            {{ attributes.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!attributes.prev_page_url"
                                @click="visitPage(attributes.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!attributes.next_page_url"
                                @click="visitPage(attributes.next_page_url)"
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
