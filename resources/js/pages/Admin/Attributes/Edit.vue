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

type Attribute = {
    id: number;
    name: string;
    slug: string;
    input_type: string;
    unit: string | null;
};

type Props = {
    attribute: Attribute;
    inputTypes: InputTypeOption[];
};

const props = defineProps<Props>();

const form = useForm({
    name: props.attribute.name,
    slug: props.attribute.slug,
    input_type: props.attribute.input_type,
    unit: props.attribute.unit ?? '',
});

function submit() {
    form.put(admin.attributes.update.url(props.attribute.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Attribute updated successfully.');
        },
        onError: () => {
            toast.error('Failed to update attribute.');
        },
    });
}
</script>

<template>
    <Head :title="`Edit: ${attribute.name}`" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Attributes', href: admin.attributes.index.url() },
            { title: 'Edit', href: admin.attributes.edit.url(attribute.id) },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                :title="`Edit: ${attribute.name}`"
                description="Update attribute details"
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
                                {{ form.processing ? 'Updating...' : 'Update Attribute' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
