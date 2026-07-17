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

type AttributeOption = {
    id: number;
    name: string;
    input_type: string;
};

type Props = {
    attributes: AttributeOption[];
    attributeId: number | null;
};

const props = defineProps<Props>();

const form = useForm({
    attribute_id: props.attributeId ? String(props.attributeId) : '',
    value: '',
    sort_order: 0,
    color_hex: '',
});

function submit() {
    form.post(admin.attributeOptions.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Option created successfully.');
        },
        onError: () => {
            toast.error('Failed to create option.');
        },
    });
}
</script>

<template>
    <Head title="Create Option" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Admin', href: '#' },
            { title: 'Attributes', href: admin.attributes.index.url() },
            { title: 'Options', href: '#' },
            { title: 'Create', href: admin.attributeOptions.create.url() },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Heading
                title="Create Option"
                description="Add a new option for a select-type attribute"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Option Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="attribute_id">Attribute</Label>
                                <Select v-model="form.attribute_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select attribute" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="attr in attributes"
                                            :key="attr.id"
                                            :value="String(attr.id)"
                                        >
                                            {{ attr.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.attribute_id" />
                            </div>

                            <div class="space-y-2">
                                <Label for="value">Value</Label>
                                <Input
                                    id="value"
                                    v-model="form.value"
                                    placeholder="Option value"
                                />
                                <InputError :message="form.errors.value" />
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

                            <div class="space-y-2">
                                <Label for="color_hex">Color (HEX)</Label>
                                <div class="flex items-center gap-2">
                                    <div
                                        v-if="form.color_hex"
                                        class="size-8 shrink-0 rounded-md border"
                                        :style="{ backgroundColor: form.color_hex }"
                                    />
                                    <Input
                                        id="color_hex"
                                        v-model="form.color_hex"
                                        placeholder="#ff0000"
                                        class="flex-1"
                                    />
                                </div>
                                <InputError :message="form.errors.color_hex" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Option' }}
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
