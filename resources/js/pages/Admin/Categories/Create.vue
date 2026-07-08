<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
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
import { Checkbox } from '@/components/ui/checkbox';
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

type ParentOption = {
    id: number;
    name: string;
};

type Props = {
    parents: ParentOption[];
};

defineProps<Props>();

const form = useForm({
    parent_id: '0',
    name: '',
    slug: '',
    image: null as File | null,
    sort_order: 0,
    is_active: true,
});

const previewUrl = computed(() => {
    if (!form.image) return null;
    return URL.createObjectURL(form.image);
});

const fileInputRef = ref<HTMLInputElement | null>(null);

function clearImage() {
    form.image = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
}

function submit() {
    // Ensure parent_id is null for top-level
    form.parent_id = form.parent_id === '0' ? null : form.parent_id;

    form.post(admin.categories.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Category created successfully.');
        },
        onError: () => {
            toast.error('Failed to create category.');
        },
    });
}
</script>

<template>
    <Head title="Create Category" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Categories', href: admin.categories.index.url() },
            { title: 'Create', href: admin.categories.create.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                title="Create Category"
                description="Add a new product category"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Category Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Category name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    placeholder="category-slug"
                                />
                                <InputError :message="form.errors.slug" />
                            </div>

                            <div class="space-y-2">
                                <Label for="parent_id">Parent Category</Label>
                                <Select v-model="form.parent_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="None (top level)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">
                                            None (top level)
                                        </SelectItem>
                                        <SelectItem
                                            v-for="parent in parents"
                                            :key="parent.id"
                                            :value="String(parent.id)"
                                        >
                                            {{ parent.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.parent_id" />
                            </div>

                            <div class="space-y-2">
                                <Label>Image</Label>
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
                                            @click="clearImage"
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
                                            {{ form.image ? 'Change' : 'Choose' }} Image
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
                                    @input="form.image = ($event.target as HTMLInputElement).files?.[0] ?? null"
                                />
                                <InputError :message="form.errors.image" />
                            </div>

                            <div class="space-y-2">
                                <Label for="sort_order">Sort Order</Label>
                                <Input
                                    id="sort_order"
                                    v-model="form.sort_order"
                                    type="number"
                                    placeholder="0"
                                />
                                <InputError :message="form.errors.sort_order" />
                            </div>

                            <div class="flex items-end gap-2 pb-2">
                                <Checkbox
                                    id="is_active"
                                    v-model:checked="form.is_active"
                                />
                                <Label for="is_active">Active</Label>
                                <InputError :message="form.errors.is_active" />
                            </div>
                        </div>

                        <div v-if="form.progress" class="w-full">
                            <div
                                class="h-2 rounded-full bg-muted"
                            >
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
                                {{ form.processing ? 'Creating...' : 'Create Category' }}
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
