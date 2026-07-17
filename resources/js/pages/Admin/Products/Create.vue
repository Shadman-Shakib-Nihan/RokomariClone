<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ImagePlus, Plus, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

type Category = {
    id: number;
    name: string;
    parent_id: number | null;
};

type Brand = {
    id: number;
    name: string;
};

type Publisher = {
    id: number;
    name: string;
};

type Author = {
    id: number;
    name: string;
};

type AttributeOption = {
    id: number;
    value: string;
    color_hex: string | null;
    sort_order: number;
};

type AttributeItem = {
    id: number;
    name: string;
    slug: string;
    input_type: string;
    unit: string | null;
    options: AttributeOption[];
};

type Props = {
    categories: Category[];
    brands: Brand[];
    publishers: Publisher[];
    authors: Author[];
    attributes: AttributeItem[];
};

const props = defineProps<Props>();

const form = useForm({
    category_id: '',
    brand_id: null,
    publisher_id: null,
    name: '',
    slug: '',
    description: '',
    weight: '',
    barcode: '',
    featured: false,
    status: 'draft',
    published_at: '',
    meta_title: '',
    meta_description: '',

    images: [] as File[],
    existing_images: [] as { id: number; url: string; is_primary: boolean }[],
    deleted_image_ids: [] as number[],

    authors: [] as { author_id: number; sort_order: number }[],

    attributes: [] as {
        attribute_id: number;
        attribute_option_id: number | null;
        value_text: string | null;
        value_number: string | null;
        value_boolean: boolean | null;
        value_date: string | null;
    }[],

    variants: [] as {
        sku: string;
        price: string;
        discount_price: string;
        stock_quantity: string;
        is_default: boolean;
        attribute_option_ids: number[];
        images: File[];
    }[],
    deleted_variant_ids: [] as number[],
});

const imagePreviews = ref<{ url: string; file: File }[]>([]);
const variantImagePreviews = ref<{
    [variantIndex: number]: { url: string; file: File }[];
}>({});

function addImages(files: FileList | null) {
    if (!files) return;
    for (const file of Array.from(files)) {
        form.images.push(file);
        imagePreviews.value.push({ url: URL.createObjectURL(file), file });
    }
}

function removeImage(index: number) {
    const preview = imagePreviews.value[index];
    URL.revokeObjectURL(preview.url);
    imagePreviews.value.splice(index, 1);
    form.images.splice(index, 1);
}

const fileInputRef = ref<HTMLInputElement | null>(null);

const selectedCategoryAttrs = computed(() => {
    const catId = Number(form.category_id);
    if (!catId) return props.attributes;
    return props.attributes;
});

watch(
    () => form.category_id,
    () => {
        form.attributes = selectedCategoryAttrs.value.map((attr) => ({
            attribute_id: attr.id,
            attribute_option_id: null,
            value_text: null,
            value_number: null,
            value_boolean: null,
            value_date: null,
        }));
    },
    { immediate: true },
);

function addVariant() {
    form.variants.push({
        sku: '',
        price: '',
        discount_price: '',
        stock_quantity: '0',
        is_default: form.variants.length === 0,
        attribute_option_ids: [],
        images: [],
    });
}

function removeVariant(index: number) {
    form.variants.splice(index, 1);
    if (variantImagePreviews.value[index]) {
        variantImagePreviews.value[index].forEach((p) =>
            URL.revokeObjectURL(p.url),
        );
        delete variantImagePreviews.value[index];
    }
}

function addVariantImages(variantIndex: number, files: FileList | null) {
    if (!files) return;
    for (const file of Array.from(files)) {
        form.variants[variantIndex].images.push(file);
        if (!variantImagePreviews.value[variantIndex]) {
            variantImagePreviews.value[variantIndex] = [];
        }
        variantImagePreviews.value[variantIndex].push({
            url: URL.createObjectURL(file),
            file,
        });
    }
}

function removeVariantImage(variantIndex: number, imageIndex: number) {
    const previews = variantImagePreviews.value[variantIndex];
    if (previews) {
        URL.revokeObjectURL(previews[imageIndex].url);
        previews.splice(imageIndex, 1);
    }
    form.variants[variantIndex].images.splice(imageIndex, 1);
}

function addAuthor(authorId: number) {
    if (form.authors.some((a) => a.author_id === authorId)) return;
    form.authors.push({ author_id: authorId, sort_order: form.authors.length });
}

function removeAuthor(authorId: number) {
    form.authors = form.authors.filter((a) => a.author_id !== authorId);
}

function submit() {
    form.post(admin.products.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Product created successfully.');
        },
        onError: () => {
            toast.error('Failed to create product.');
        },
    });
}
</script>

<template>
    <Head title="Create Product" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Products', href: admin.products.index.url() },
            { title: 'Create', href: admin.products.create.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading title="Create Product" description="Add a new product" />

            <form @submit.prevent="submit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Product name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    placeholder="product-slug"
                                />
                                <InputError :message="form.errors.slug" />
                            </div>

                            <div class="space-y-2">
                                <Label for="category_id">Category</Label>
                                <Select v-model="form.category_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select category"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="cat in categories"
                                            :key="cat.id"
                                            :value="String(cat.id)"
                                        >
                                            {{ cat.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="form.errors.category_id"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="brand_id">Brand</Label>
                                <Select v-model="form.brand_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select brand"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem :value="null"
                                            >None</SelectItem
                                        >
                                        <SelectItem
                                            v-for="brand in brands"
                                            :key="brand.id"
                                            :value="String(brand.id)"
                                        >
                                            {{ brand.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.brand_id" />
                            </div>

                            <div class="space-y-2">
                                <Label for="publisher_id">Publisher</Label>
                                <Select v-model="form.publisher_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select publisher"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem :value="null"
                                            >None</SelectItem
                                        >
                                        <SelectItem
                                            v-for="publisher in publishers"
                                            :key="publisher.id"
                                            :value="String(publisher.id)"
                                        >
                                            {{ publisher.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="form.errors.publisher_id"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select status"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft"
                                            >Draft</SelectItem
                                        >
                                        <SelectItem value="active"
                                            >Active</SelectItem
                                        >
                                        <SelectItem value="inactive"
                                            >Inactive</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.status" />
                            </div>

                            <div class="space-y-2">
                                <Label for="barcode">Barcode</Label>
                                <Input
                                    id="barcode"
                                    v-model="form.barcode"
                                    placeholder="ISBN or barcode"
                                />
                                <InputError :message="form.errors.barcode" />
                            </div>

                            <div class="space-y-2">
                                <Label for="weight">Weight</Label>
                                <Input
                                    id="weight"
                                    v-model="form.weight"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                />
                                <InputError :message="form.errors.weight" />
                            </div>

                            <div class="space-y-2">
                                <Label for="published_at">Published At</Label>
                                <Input
                                    id="published_at"
                                    v-model="form.published_at"
                                    type="date"
                                />
                                <InputError
                                    :message="form.errors.published_at"
                                />
                            </div>

                            <div class="flex items-end gap-2 pb-2">
                                <input
                                    id="featured"
                                    type="checkbox"
                                    :checked="form.featured"
                                    @change="
                                        form.featured = (
                                            $event.target as HTMLInputElement
                                        ).checked
                                    "
                                    class="peer size-4 shrink-0 rounded-[4px] border border-input shadow-xs transition-shadow outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground dark:aria-invalid:ring-destructive/40"
                                />
                                <Label for="featured">Featured</Label>
                                <InputError :message="form.errors.featured" />
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Product description"
                                class="min-h-32 w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:aria-invalid:ring-destructive/40"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Images</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-3">
                            <div
                                v-for="(preview, index) in imagePreviews"
                                :key="preview.url"
                                class="relative size-28 overflow-hidden rounded-lg border"
                            >
                                <img
                                    :src="preview.url"
                                    alt="Preview"
                                    class="size-full object-cover"
                                />
                                <button
                                    type="button"
                                    class="absolute top-1 right-1 rounded-full bg-black/60 p-0.5 text-white"
                                    @click="removeImage(index)"
                                >
                                    <X class="size-3" />
                                </button>
                                <Badge
                                    v-if="index === 0"
                                    class="absolute bottom-1 left-1 text-[10px]"
                                    variant="default"
                                >
                                    Primary
                                </Badge>
                            </div>

                            <label
                                class="flex size-28 cursor-pointer items-center justify-center rounded-lg border border-dashed text-muted-foreground hover:border-primary hover:text-primary"
                            >
                                <ImagePlus class="size-6" />
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    multiple
                                    class="hidden"
                                    @input="
                                        addImages(
                                            ($event.target as HTMLInputElement)
                                                .files,
                                        )
                                    "
                                />
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-muted-foreground">
                            JPG, PNG or WebP. Max 2MB each. First image is
                            primary.
                        </p>
                        <InputError :message="form.errors.images" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Authors</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <template
                                v-for="author in authors"
                                :key="author.id"
                            >
                                <Badge
                                    :variant="
                                        form.authors.some(
                                            (a) => a.author_id === author.id,
                                        )
                                            ? 'default'
                                            : 'outline'
                                    "
                                    class="cursor-pointer"
                                    @click="
                                        form.authors.some(
                                            (a) => a.author_id === author.id,
                                        )
                                            ? removeAuthor(author.id)
                                            : addAuthor(author.id)
                                    "
                                >
                                    {{ author.name }}
                                    <span
                                        v-if="
                                            form.authors.some(
                                                (a) =>
                                                    a.author_id === author.id,
                                            )
                                        "
                                        class="ml-1"
                                        >✕</span
                                    >
                                </Badge>
                            </template>
                        </div>
                        <InputError :message="form.errors.authors" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Attributes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="selectedCategoryAttrs.length === 0"
                            class="text-sm text-muted-foreground"
                        >
                            Select a category to view its attributes.
                        </div>
                        <div v-else class="grid gap-4 md:grid-cols-2">
                            <div
                                v-for="(attr, index) in selectedCategoryAttrs"
                                :key="attr.id"
                                class="space-y-2"
                            >
                                <Label>
                                    {{ attr.name }}
                                    <span
                                        v-if="attr.unit"
                                        class="text-xs text-muted-foreground"
                                        >({{ attr.unit }})</span
                                    >
                                </Label>

                                <div v-if="attr.input_type === 'select'">
                                    <Select
                                        v-model="
                                            form.attributes[index]
                                                .attribute_option_id
                                        "
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                :placeholder="`Select ${attr.name}`"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem :value="null"
                                                >None</SelectItem
                                            >
                                            <SelectItem
                                                v-for="option in attr.options"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <div
                                                        v-if="option.color_hex"
                                                        class="size-4 rounded border"
                                                        :style="{
                                                            backgroundColor:
                                                                option.color_hex,
                                                        }"
                                                    />
                                                    {{ option.value }}
                                                </div>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div v-if="attr.input_type === 'number'">
                                    <Input
                                        v-model="
                                            form.attributes[index].value_number
                                        "
                                        type="number"
                                        step="0.01"
                                        :placeholder="`Enter ${attr.name}`"
                                    />
                                </div>

                                <div v-if="attr.input_type === 'date'">
                                    <Input
                                        v-model="
                                            form.attributes[index].value_date
                                        "
                                        type="date"
                                    />
                                </div>

                                <div
                                    v-if="attr.input_type === 'boolean'"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="
                                            form.attributes[index]
                                                .value_boolean === true
                                        "
                                        @change="
                                            form.attributes[
                                                index
                                            ].value_boolean = (
                                                $event.target as HTMLInputElement
                                            ).checked
                                        "
                                        class="peer size-4 shrink-0 rounded-[4px] border border-input shadow-xs transition-shadow outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground dark:aria-invalid:ring-destructive/40"
                                    />
                                    <span class="text-sm">{{
                                        form.attributes[index].value_boolean
                                            ? 'Yes'
                                            : 'No'
                                    }}</span>
                                </div>

                                <div v-if="attr.input_type === 'text'">
                                    <Input
                                        v-model="
                                            form.attributes[index].value_text
                                        "
                                        :placeholder="`Enter ${attr.name}`"
                                    />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.attributes" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Variants</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="form.variants.length === 0"
                            class="mb-4 text-sm text-muted-foreground"
                        >
                            No variants yet. Add at least one variant if this
                            product has options (e.g. sizes, colors).
                        </div>

                        <div
                            v-for="(variant, vIndex) in form.variants"
                            :key="vIndex"
                            class="mb-6 rounded-lg border p-4"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="font-medium">
                                    Variant {{ vIndex + 1 }}
                                </h4>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-red-500"
                                    @click="removeVariant(vIndex)"
                                >
                                    <X class="size-4" />
                                    Remove
                                </Button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-4">
                                <div class="space-y-2">
                                    <Label :for="`variant-sku-${vIndex}`"
                                        >SKU</Label
                                    >
                                    <Input
                                        :id="`variant-sku-${vIndex}`"
                                        v-model="variant.sku"
                                        placeholder="SKU"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`variant-price-${vIndex}`"
                                        >Price</Label
                                    >
                                    <Input
                                        :id="`variant-price-${vIndex}`"
                                        v-model="variant.price"
                                        type="number"
                                        step="0.01"
                                        placeholder="0.00"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`variant-discount-${vIndex}`"
                                        >Discount Price</Label
                                    >
                                    <Input
                                        :id="`variant-discount-${vIndex}`"
                                        v-model="variant.discount_price"
                                        type="number"
                                        step="0.01"
                                        placeholder="0.00"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`variant-stock-${vIndex}`"
                                        >Stock</Label
                                    >
                                    <Input
                                        :id="`variant-stock-${vIndex}`"
                                        v-model="variant.stock_quantity"
                                        type="number"
                                        placeholder="0"
                                    />
                                </div>

                                <div class="flex items-end gap-2 pb-2">
                                    <input
                                        :id="`variant-default-${vIndex}`"
                                        type="checkbox"
                                        :checked="variant.is_default"
                                        @change="
                                            variant.is_default = (
                                                $event.target as HTMLInputElement
                                            ).checked
                                        "
                                        class="peer size-4 shrink-0 rounded-[4px] border border-input shadow-xs transition-shadow outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground dark:aria-invalid:ring-destructive/40"
                                    />
                                    <Label :for="`variant-default-${vIndex}`"
                                        >Default</Label
                                    >
                                </div>
                            </div>

                            <div
                                v-if="
                                    selectedCategoryAttrs.some(
                                        (a) => a.input_type === 'select',
                                    )
                                "
                                class="mt-3 space-y-2"
                            >
                                <Label>Attribute Options</Label>
                                <div class="flex flex-wrap gap-2">
                                    <template
                                        v-for="attr in selectedCategoryAttrs.filter(
                                            (a) => a.input_type === 'select',
                                        )"
                                        :key="attr.id"
                                    >
                                        <span
                                            class="w-full text-xs font-medium text-muted-foreground"
                                            >{{ attr.name }}</span
                                        >
                                        <Badge
                                            v-for="option in attr.options"
                                            :key="option.id"
                                            :variant="
                                                variant.attribute_option_ids.includes(
                                                    option.id,
                                                )
                                                    ? 'default'
                                                    : 'outline'
                                            "
                                            class="cursor-pointer"
                                            @click="
                                                const idx =
                                                    variant.attribute_option_ids.indexOf(
                                                        option.id,
                                                    );
                                                if (idx === -1) {
                                                    variant.attribute_option_ids.push(
                                                        option.id,
                                                    );
                                                } else {
                                                    variant.attribute_option_ids.splice(
                                                        idx,
                                                        1,
                                                    );
                                                }
                                            "
                                        >
                                            <div
                                                v-if="option.color_hex"
                                                class="mr-1 size-3 rounded border"
                                                :style="{
                                                    backgroundColor:
                                                        option.color_hex,
                                                }"
                                            />
                                            {{ option.value }}
                                        </Badge>
                                    </template>
                                </div>
                            </div>

                            <div class="mt-3 space-y-2">
                                <Label>Variant Images</Label>
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        v-for="(
                                            preview, imgIndex
                                        ) in variantImagePreviews[vIndex]"
                                        :key="preview.url"
                                        class="relative size-20 overflow-hidden rounded-lg border"
                                    >
                                        <img
                                            :src="preview.url"
                                            alt="Preview"
                                            class="size-full object-cover"
                                        />
                                        <button
                                            type="button"
                                            class="absolute top-0.5 right-0.5 rounded-full bg-black/60 p-0.5 text-white"
                                            @click="
                                                removeVariantImage(
                                                    vIndex,
                                                    imgIndex,
                                                )
                                            "
                                        >
                                            <X class="size-2.5" />
                                        </button>
                                    </div>

                                    <label
                                        class="flex size-20 cursor-pointer items-center justify-center rounded-lg border border-dashed text-muted-foreground hover:border-primary hover:text-primary"
                                    >
                                        <ImagePlus class="size-5" />
                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            multiple
                                            class="hidden"
                                            @input="
                                                addVariantImages(
                                                    vIndex,
                                                    (
                                                        $event.target as HTMLInputElement
                                                    ).files,
                                                )
                                            "
                                        />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <Button
                            type="button"
                            variant="outline"
                            @click="addVariant"
                        >
                            <Plus class="size-4" />
                            Add Variant
                        </Button>
                        <InputError :message="form.errors.variants" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>SEO</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="meta_title">Meta Title</Label>
                                <Input
                                    id="meta_title"
                                    v-model="form.meta_title"
                                    placeholder="SEO title"
                                />
                                <InputError :message="form.errors.meta_title" />
                            </div>

                            <div class="space-y-2">
                                <Label for="meta_description"
                                    >Meta Description</Label
                                >
                                <textarea
                                    id="meta_description"
                                    v-model="form.meta_description"
                                    placeholder="SEO description"
                                    class="min-h-20 w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:aria-invalid:ring-destructive/40"
                                />
                                <InputError
                                    :message="form.errors.meta_description"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex items-center gap-2">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Product' }}
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
        </div>
    </AppLayout>
</template>
