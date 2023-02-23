<script setup>

import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm, Head} from "@inertiajs/vue3";

defineProps({campaigns: Object});

const form = useForm({
    repository: '',
    organization: '',
    admin: '',
});
</script>

<template>
    <Head title="Organization"/>

    <AppLayout>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="bg-white rounded-md shadow overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <tr class="text-left font-bold">
                        <th class="pb-4 pt-6 px-6">Campaign</th>
                        <th class="pb-4 pt-6 px-6">Repository</th>
                        <th class="pb-4 pt-6 px-6">Organization</th>
                        <th class="pb-4 pt-6 px-6">Admin</th>
                    </tr>
                    <tr v-for="campaign in campaigns" :key="campaign.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            {{ campaign.active }}
                        </td>
                        <td class="border-t">
                            {{ campaign.repository.name }}
                        </td>
                        <td class="border-t">
                            {{ campaign.repository.organization.name }}
                        </td>
                        <td class="border-t">
                            {{ campaign.repository.organization.admin.name }}
                        </td>
                    </tr>
                    <tr v-if="campaigns.length === 0">
                        <td class="px-6 py-4 border-t" colspan="4">No campaigns found.</td>
                    </tr>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
