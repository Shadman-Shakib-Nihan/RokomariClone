<script setup lang="ts">
/**
 * ── DATA FLOW OVERVIEW ──────────────────────────────────────────
 *
 * 1. This page is rendered by the `create()` method in:
 *    app/Http/Controllers/Admin/AuthorController.php:43
 *    → It calls `Inertia::render('Admin/Authors/Create')` (no props).
 *
 * 2. On form submit, data is POSTed to:
 *    Route: `admin.authors.store` → POST /admin/authors
 *    Controller: `AuthorController::store()` (AuthorController.php:51)
 *
 * 3. The `store()` method receives a `StoreAuthorRequest` (validates
 *    input), stores the photo file via `Storage::disk('public')`,
 *    and creates a new `Author` record in the `authors` table.
 *
 * 4. After success, it flashes a toast and redirects to the authors
 *    index page (admin.authors.index).
 *
 * 5. The `authors` table schema (SQLite):
 *    id, name, slug, biography, photo (file path), is_active,
 *    created_at, updated_at, deleted_at (soft deletes).
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

// ── FORM STATE ──────────────────────────────────────────────────
// This data is sent to StoreAuthorRequest → AuthorController::store()
// → Author::create(...) → `authors` table.

const form = useForm({
    name: '',
    slug: '',
    photo: null as File | null,
    biography: '',
    is_active: true,
});

// ── PHOTO PREVIEW LOGIC ─────────────────────────────────────────

const previewUrl = computed(() => {
    if (!form.photo) return null;
    return URL.createObjectURL(form.photo);
});

const fileInputRef = ref<HTMLInputElement | null>(null);

function clearPhoto() {
    form.photo = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
}

// ── SUBMIT ──────────────────────────────────────────────────────
// POSTs form data (including photo as multipart) to:
//   Controller: AuthorController::store()  [app/Http/Controllers/Admin/AuthorController.php:51]
//   Route:      admin.authors.store        [POST /admin/authors]
//   Validation: StoreAuthorRequest         [app/Http/Requests/StoreAuthorRequest.php]
//   Model:      Author::create(...)        [app/Models/Author.php]
//   Table:      authors (SQLite)

function submit() {
    form.post(admin.authors.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Author created successfully.');
        },
        onError: () => {
            toast.error('Failed to create author.');
        },
    });
}
</script>

<template>
    <Head title="Create Author" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Authors', href: admin.authors.index.url() },
            { title: 'Create', href: admin.authors.create.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                title="Create Author"
                description="Add a new book author"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Author Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <!--
                                name → StoreAuthorRequest validates "required|string|max:255"
                                → AuthorController::store() → Author::create(['name' => ...])
                                → INSERT INTO authors (name, ...)
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
                                slug → StoreAuthorRequest validates "required|unique:authors,slug"
                                → If empty, controller auto-generates from name via Str::slug()
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
                                photo → Uploaded as File
                                → Controller stores via $request->file('photo')->store('authors', 'public')
                                → File saved to storage/app/public/authors/
                                → Path saved in authors.photo column
                                → Served via Storage::url() / symlinked from public/storage
                            -->
                            <div class="space-y-2">
                                <Label>Photo</Label>
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
                                biography → StoreAuthorRequest validates "nullable|string"
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
                                {{ form.processing ? 'Creating...' : 'Create Author' }}
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
