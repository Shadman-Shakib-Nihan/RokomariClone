<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { toast } from 'vue-sonner';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { ref, watch } from 'vue';

type AttributeItem = {
    id: number;
    name: string;
};

type Option = {
    id: number;
    attribute_id: number;
    value: string;
    sort_order: number | null;
    color_hex: string | null;
    attribute: { id: number; name: string } | null;
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
    options: PaginatedData<Option>;
    attributes: AttributeItem[];
    filters: {
        attribute_id: number | null;
    };
};

const props = defineProps<Props>();

const selectedAttributeId = ref(props.filters.attribute_id ? String(props.filters.attribute_id) : '0');

watch(selectedAttributeId, (val) => {
    router.get(
        admin.attributeOptions.index.url(),
        { attribute_id: val === '0' ? undefined : val },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

function confirmDelete(option: Option) {
    if (
        !window.confirm(
            `Are you sure you want to delete "${option.value}"?`,
        )
    ) {
        return;
    }

    router.delete(admin.attributeOptions.destroy.url(option.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Option deleted successfully.');
        },
        onError: (errors) => {
            if (typeof errors === 'string') {
                toast.error(errors);
            } else {
                toast.error(
                    Object.values(errors).flat().join(', ') ||
                        'Failed to delete option.',
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
    <Head title="Attribute Options" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Attributes', href: admin.attributes.index.url() },
            { title: 'Options', href: admin.attributeOptions.index.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Attribute Options" description="Manage select-type attribute values" />

            <Card>
                <CardHeader>
                    <CardTitle>All Options</CardTitle>
                    <CardAction>
                        <Link :href="admin.attributeOptions.create.url()">
                            <Button>
                                <Plus class="size-4" />
                                Create
                            </Button>
                        </Link>
                    </CardAction>
                </CardHeader>
                <CardContent>
                    <div class="mb-4">
                        <Select v-model="selectedAttributeId">
                            <SelectTrigger class="w-full max-w-xs">
                                <SelectValue placeholder="All attributes" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="0">
                                    All attributes
                                </SelectItem>
                                <SelectItem
                                    v-for="attr in attributes"
                                    :key="attr.id"
                                    :value="String(attr.id)"
                                >
                                    {{ attr.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 pr-4 font-medium">Attribute</th>
                                    <th class="py-3 pr-4 font-medium">Value</th>
                                    <th class="py-3 pr-4 font-medium">Color</th>
                                    <th class="py-3 pr-4 font-medium">Order</th>
                                    <th class="py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="option in options.data"
                                    :key="option.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ option.attribute?.name ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4 font-medium">
                                        {{ option.value }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <div v-if="option.color_hex" class="flex items-center gap-2">
                                            <div
                                                class="size-5 rounded border"
                                                :style="{ backgroundColor: option.color_hex }"
                                            />
                                            <span class="text-muted-foreground text-xs font-mono">
                                                {{ option.color_hex }}
                                            </span>
                                        </div>
                                        <span v-else class="text-muted-foreground">—</span>
                                    </td>
                                    <td class="py-3 pr-4 text-muted-foreground">
                                        {{ option.sort_order ?? '—' }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="admin.attributeOptions.edit.url(option.id)"
                                            >
                                                <Button variant="ghost" size="icon-sm">
                                                    <Pencil class="size-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                @click="confirmDelete(option)"
                                            >
                                                <Trash2 class="size-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="options.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="text-muted-foreground py-8 text-center"
                                    >
                                        No options found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="options.last_page > 1"
                        class="flex items-center justify-between gap-4 pt-4"
                    >
                        <p class="text-muted-foreground text-sm">
                            Showing {{ options.from }}–{{ options.to }} of
                            {{ options.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!options.prev_page_url"
                                @click="visitPage(options.prev_page_url)"
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!options.next_page_url"
                                @click="visitPage(options.next_page_url)"
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
