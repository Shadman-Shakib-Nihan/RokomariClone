<script setup lang="ts">
/**
 * ── DATA FLOW OVERVIEW ──────────────────────────────────────────
 *
 * 1. This page is rendered by the `edit()` method in:
 *    app/Http/Controllers/Admin/AuthorController.php:83
 *    → Route Model Binding fetches the `Author` by {author} ID from the `authors` table.
 *    → Passes the full `Author` model as a prop to this page.
 *
 * 2. On form submit, data is PUT to:
 *    Route: `admin.authors.update` → PUT /admin/authors/{author}
 *    Controller: `AuthorController::update()` (AuthorController.php:93)
 *
 * 3. The `update()` method receives an `UpdateAuthorRequest` (validates input),
 *    optionally deletes the old photo and stores a new one via `Storage::disk('public')`,
 *    then calls `$author->update(...)` on the existing model.
 *
 * 4. After success, it flashes a toast and redirects to the authors index page.
 *
 * ── COMPONENT STRUCTURE ─────────────────────────────────────────
 */

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

// ── TYPES ───────────────────────────────────────────────────────

type Author = {
    id: number;
    name: string;
    slug: string;
    photo: string | null;
    biography: string | null;
    is_active: boolean;
};

type Props = {
    author: Author;
};

const props = defineProps<Props>();

// ── FORM STATE ──────────────────────────────────────────────────
// Pre-filled from the `author` prop (fetched from `authors` table via Route Model Binding).
// On submit, this data is sent to UpdateAuthorRequest → AuthorController::update()
// → $author->update(...) → UPDATE `authors` SET ... WHERE id = ?

const form = useForm({
    name: props.author.name,
    slug: props.author.slug,
    photo: null as File | null,
    biography: props.author.biography ?? '',
    is_active: props.author.is_active,
});

// ── PHOTO LOGIC ─────────────────────────────────────────────────
// existingPhotoUrl: the current photo from the database (if any), served via public/storage.
// previewUrl: a local blob URL for a newly selected file (before upload).

const existingPhotoUrl = computed(() => {
    if (!props.author.photo) return null;
    if (props.author.photo.startsWith('http')) return props.author.photo;
    return `/storage/${props.author.photo}`;
});

const previewUrl = computed(() => {
    if (form.photo) return URL.createObjectURL(form.photo);
    return null;
});

const fileInputRef = ref<HTMLInputElement | null>(null);

function clearPhoto() {
    form.photo = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
}

// ── SUBMIT ──────────────────────────────────────────────────────
// PUTs form data (including photo as multipart) to:
//   Controller: AuthorController::update()  [app/Http/Controllers/Admin/AuthorController.php:93]
//   Route:      admin.authors.update         [PUT /admin/authors/{author}]
//   Validation: UpdateAuthorRequest          [app/Http/Requests/UpdateAuthorRequest.php]
//   Model:      $author->update(...)         [app/Models/Author.php]
//   Table:      authors (SQLite)

function submit() {
    form.put(admin.authors.update.url(props.author.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Author updated successfully.');
        },
        onError: () => {
            toast.error('Failed to update author.');
        },
    });
}
</script>

<template>
    <Head :title="`Edit: ${author.name}`" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Authors', href: admin.authors.index.url() },
            { title: 'Edit', href: admin.authors.edit.url(author.id) },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                :title="`Edit: ${author.name}`"
                description="Update author details"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Author Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <!--
                                name → UpdateAuthorRequest validates "required|string|max:255"
                                → AuthorController::update() → $author->update(['name' => ...])
                                → UPDATE authors SET name = ? WHERE id = ?
                            -->
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Author name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <!--
                                slug → UpdateAuthorRequest validates "required|unique:authors,slug,{id}"
                                → Controller auto-generates via Str::slug() if submitted value is empty
                                → Stored in authors.slug column
                            -->
                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    placeholder="author-slug"
                                />
                                <InputError :message="form.errors.slug" />
                            </div>

                            <!--
                                photo → Uploaded as File (optional, only when user picks a new one)
                                → Controller deletes old photo from storage if it exists
                                → Stores new file via $request->file('photo')->store('authors', 'public')
                                → File saved to storage/app/public/authors/
                                → New path saved in authors.photo column
                                → If no new file selected, the existing photo path is kept
                            -->
                            <div class="space-y-2">
                                <Label>Photo</Label>
                                <div class="flex items-center gap-4">
                                    <div
                                        v-if="previewUrl || (existingPhotoUrl && !form.photo)"
                                        class="relative size-24 overflow-hidden rounded-lg border"
                                    >
                                        <img
                                            :src="previewUrl ?? existingPhotoUrl ?? ''"
                                            alt="Preview"
                                            class="size-full object-cover"
                                        />
                                        <button
                                            type="button"
                                            class="absolute right-1 top-1 rounded-full bg-black/60 p-0.5 text-white"
                                            @click="clearPhoto"
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
                                            {{ form.photo ? 'Change' : 'Choose' }} Photo
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
                                    @input="form.photo = ($event.target as HTMLInputElement).files?.[0] ?? null"
                                />
                                <InputError :message="form.errors.photo" />
                            </div>

                            <!--
                                biography → UpdateAuthorRequest validates "nullable|string"
                                → Stored in authors.biography column (TEXT field)
                            -->
                            <div class="space-y-2 md:col-span-2">
                                <Label for="biography">Biography</Label>
                                <textarea
                                    id="biography"
                                    v-model="form.biography"
                                    placeholder="Author biography"
                                    class="border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                />
                                <InputError :message="form.errors.biography" />
                            </div>

                            <!--
                                is_active → Controller reads via $request->boolean('is_active')
                                → Stored in authors.is_active column (TINYINT / boolean)
                            -->
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

                        <!-- Upload progress bar -->
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
                                {{ form.processing ? 'Updating...' : 'Update Author' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
