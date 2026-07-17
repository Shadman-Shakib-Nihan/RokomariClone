<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import InputError from '@/components/InputError.vue';



type InputTypeOption = {
    value: string;
    label: string;
};

type CategoryOption = {
    id: number;
    name: string;
    parent_id: number | null;
};

type CategoryFormItem = {
    category_id: number;
    sort_order: number;
    is_required: boolean;
    is_filterable: boolean;
};

type Props = {
    inputTypes: InputTypeOption[];
    categories: CategoryOption[];
};

defineProps<Props>();

const form = useForm({
    name: '',
    slug: '',
    input_type: '',
    unit: '',
    categories: [] as CategoryFormItem[],
});

function toggleCategory(categoryId: number, checked: boolean | 'indeterminate') {
    if (checked) {
        form.categories.push({
            category_id: categoryId,
            sort_order: form.categories.length,
            is_required: false,
            is_filterable: false,
        });
    } else {
        form.categories = form.categories.filter((c) => c.category_id !== categoryId);
    }
}

function isCategorySelected(categoryId: number): boolean {
    return form.categories.some((c) => c.category_id === categoryId);
}

function getCategoryItem(categoryId: number): CategoryFormItem | undefined {
    return form.categories.find((c) => c.category_id === categoryId);
}

function submit() {
    form.post(admin.attributes.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Attribute created successfully.');
        },
        onError: () => {
            toast.error('Failed to create attribute.');
        },
    });
}
</script>

<template>
    <Head title="Create Attribute" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Attributes', href: admin.attributes.index.url() },
            { title: 'Create', href: admin.attributes.create.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                title="Create Attribute"
                description="Add a new product attribute"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Attribute Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Attribute name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    placeholder="attribute-slug"
                                />
                                <InputError :message="form.errors.slug" />
                            </div>

                            <div class="space-y-2">
                                <Label for="input_type">Input Type</Label>
                                <Select v-model="form.input_type">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select input type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="type in inputTypes"
                                            :key="type.value"
                                            :value="type.value"
                                        >
                                            {{ type.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.input_type" />
                            </div>

                            <div class="space-y-2">
                                <Label for="unit">Unit</Label>
                                <Input
                                    id="unit"
                                    v-model="form.unit"
                                    placeholder="e.g. cm, kg, ml"
                                />
                                <InputError :message="form.errors.unit" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Attribute' }}
                            </Button>
                            <Button
                                variant="outline"
                                type="button"
                                @click="form.reset()"
                            >
                                Reset
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Categories</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground mb-4 text-sm">
                        Select which categories this attribute applies to.
                    </p>
                    <div class="space-y-4">
                        <div
                            v-for="cat in categories"
                            :key="cat.id"
                            class="flex items-start gap-4 rounded-lg border p-3"
                        >
                            <input
                                type="checkbox"
                                :checked="isCategorySelected(cat.id)"
                                @change="(e) => toggleCategory(cat.id, (e.target as HTMLInputElement).checked)"
                                class="peer border-input data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive mt-1 size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <div class="flex-1 space-y-2">
                                <Label class="font-medium">{{ cat.name }}</Label>
                                <div
                                    v-if="isCategorySelected(cat.id)"
                                    class="flex flex-wrap items-center gap-4"
                                >
                                    <div class="flex items-center gap-2">
                                        <Label class="text-muted-foreground text-xs">Sort Order</Label>
                                        <Input
                                            type="number"
                                            min="0"
                                            class="w-20"
                                            :model-value="getCategoryItem(cat.id)?.sort_order ?? 0"
                                            @update:model-value="
                                                (val) => {
                                                    const item = getCategoryItem(cat.id);
                                                    if (item) item.sort_order = Number(val);
                                                }
                                            "
                                        />
                                    </div>
                                    <label class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            :checked="getCategoryItem(cat.id)?.is_required ?? false"
                                            @change="
                                                (e) => {
                                                    const item = getCategoryItem(cat.id);
                                                    if (item)
                                                        item.is_required = (e.target as HTMLInputElement).checked;
                                                }
                                            "
                                            class="size-4 rounded border"
                                        />
                                        <span class="text-xs">Required</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            :checked="getCategoryItem(cat.id)?.is_filterable ?? false"
                                            @change="
                                                (e) => {
                                                    const item = getCategoryItem(cat.id);
                                                    if (item)
                                                        item.is_filterable = (e.target as HTMLInputElement).checked;
                                                }
                                            "
                                            class="size-4 rounded border"
                                        />
                                        <span class="text-xs">Filterable</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.categories" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
