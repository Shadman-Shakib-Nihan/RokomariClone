<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ImagePlus, X } from '@lucide/vue';
import { computed, ref } from 'vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import InputError from '@/components/InputError.vue';

type CategoryOption = {
    id: number;
    name: string;
};

type Props = {
    categories: CategoryOption[];
};

defineProps<Props>();

const form = useForm({
    name: '',
    slug: '',
    logo: null as File | null,
    description: '',
    website: '',
    is_active: true,
    categories: [] as number[],
});

const previewUrl = computed(() => {
    if (!form.logo) return null;
    return URL.createObjectURL(form.logo);
});

const fileInputRef = ref<HTMLInputElement | null>(null);

function clearLogo() {
    form.logo = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
}

function toggleCategory(categoryId: number) {
    const categoryIndex = form.categories.indexOf(categoryId);

    if (categoryIndex === -1) {
        form.categories.push(categoryId);

        return;
    }

    form.categories.splice(categoryIndex, 1);
}

function submit() {
    form.post(admin.brands.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Brand created successfully.');
        },
        onError: () => {
            toast.error('Failed to create brand.');
        },
    });
}
</script>

<template>
    <Head title="Create Brand" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Brands', href: admin.brands.index.url() },
            { title: 'Create', href: admin.brands.create.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                title="Create Brand"
                description="Add a new product brand"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Brand Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Brand name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    placeholder="brand-slug"
                                />
                                <InputError :message="form.errors.slug" />
                            </div>

                            <div class="space-y-2">
                                <Label for="website">Website</Label>
                                <Input
                                    id="website"
                                    v-model="form.website"
                                    placeholder="https://example.com"
                                />
                                <InputError :message="form.errors.website" />
                            </div>

                            <div class="space-y-2">
                                <Label>Logo</Label>
                                <div class="flex items-center gap-4">
                                    <div
                                        v-if="previewUrl"
                                        class="relative size-24 overflow-hidden rounded-lg border"
                                    >
                                        <img
                                            :src="previewUrl"
                                            alt="Preview"
                                            class="size-full object-cover"
                                        />
                                        <button
                                            type="button"
                                            class="absolute right-1 top-1 rounded-full bg-black/60 p-0.5 text-white"
                                            @click="clearLogo"
                                        >
                                            <X class="size-3" />
                                        </button>
                                    </div>
                                    <div
                                        v-else
                                        class="flex size-24 cursor-pointer items-center justify-center rounded-lg border border-dashed text-muted-foreground hover:border-primary hover:text-primary"
                                        @click="fileInputRef?.click()"
                                    >
                                        <ImagePlus class="size-6" />
                                    </div>
                                    <div class="space-y-1">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="fileInputRef?.click()"
                                        >
                                            {{ form.logo ? 'Change' : 'Choose' }} Logo
                                        </Button>
                                        <p class="text-xs text-muted-foreground">
                                            JPG, PNG or WebP. Max 2MB.
                                        </p>
                                    </div>
                                </div>
                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @input="form.logo = ($event.target as HTMLInputElement).files?.[0] ?? null"
                                />
                                <InputError :message="form.errors.logo" />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="description">Description</Label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    placeholder="Brand description"
                                    class="border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                />
                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="space-y-2">
                                <Label>Categories</Label>
                                <div
                                    v-if="categories.length > 0"
                                    class="flex max-h-40 flex-wrap gap-3 overflow-y-auto rounded-md border p-3"
                                >
                                    <label
                                        v-for="cat in categories"
                                        :key="cat.id"
                                        class="flex cursor-pointer items-center gap-2 text-sm"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="form.categories.includes(cat.id)"
                                            @change="toggleCategory(cat.id)"
                                            class="border-input peer size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50"
                                        />
                                        {{ cat.name }}
                                    </label>
                                </div>
                                <p
                                    v-else
                                    class="text-muted-foreground text-sm"
                                >
                                    No categories available.
                                </p>
                                <InputError :message="form.errors.categories" />
                            </div>

                            <div class="flex items-end gap-2 pb-2">
                                <input
                                    id="is_active"
                                    type="checkbox"
                                    :checked="form.is_active"
                                    @change="form.is_active = ($event.target as HTMLInputElement).checked"
                                    class="peer border-input data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <Label for="is_active">Active</Label>
                                <InputError :message="form.errors.is_active" />
                            </div>
                        </div>

                        <div v-if="form.progress" class="w-full">
                            <div class="h-2 rounded-full bg-muted">
                                <div
                                    class="h-2 rounded-full bg-primary transition-all"
                                    :style="{ width: `${form.progress.percentage}%` }"
                                />
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Uploading: {{ form.progress.percentage }}%
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Brand' }}
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
        </div>
    </AppLayout>
</template>
