<script setup>

import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkin from '@/Components/Checkin.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm, Head} from "@inertiajs/vue3";

defineProps({checkins: Object});

const form = useForm({
    mobile: '',
});
</script>

<template>
    <Head title="Checkin"/>

    <AppLayout>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <form @submit.prevent="form.post(route('checkins.store'), { onSuccess: () => form.reset() })">
                <div>
                    <InputLabel for="mobile" value="Mobile" />
                    <TextInput
                        id="mobile"
                        v-model="form.mobile"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                        autocomplete="mobile"
                    />
                    <InputError class="mt-2" :message="form.errors.mobile" />
                </div>
                <PrimaryButton class="mt-4">New Checkin</PrimaryButton>
            </form>
            <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
                <Checkin
                    v-for="checkin in checkins"
                    :key="checkin.uuid"
                    :checkin="checkin"
                />
            </div>
        </div>
    </AppLayout>
</template>
