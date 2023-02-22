<script setup>

import relativeTime from 'dayjs/plugin/relativeTime';
import AppLayout from '@/Layouts/AppLayout.vue';
import dayjs from 'dayjs';

dayjs.extend(relativeTime);

defineProps({
    wallet: Object,
    balance: {
        type: Float32Array
    },
    updated_at: {
        type: Date
    }
});
const formatter = new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
});

</script>

<template>
    <AppLayout title="Wallet">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Wallet
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                Your balance is <a class="oldstyle-nums text-2xl font-black">{{ formatter.format(balance) }}</a> as of {{ dayjs(updated_at).fromNow() }}.
            </div>
        </div>
    </AppLayout>
</template>
