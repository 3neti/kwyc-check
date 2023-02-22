<script setup>

import PrimaryButton from '@/Components/PrimaryButton.vue';
import Organization from '@/Components/Organization.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import DropDown from '@/Components/Dropdown.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm, Head} from "@inertiajs/vue3";

defineProps({organizations: Object, channels: Array, formats: Array, pkgs: Object});

const form = useForm({
    name: '',
    channel: '',
    format: '',
    address: '',
    command: '',
    pkg: null,
});
</script>

<template>
    <Head title="Organization"/>

    <AppLayout>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <form @submit.prevent="form.post(route('organizations.store'), { onSuccess: () => form.reset() })">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="channel" value="Channel" />
                    <DropDown align="left" width="60">
                        <template #trigger>
                            <div class="relative">
                                <button type="button"
                                        class="inline-flex justify-between w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        id="channels-options-menu" aria-haspopup="true" aria-expanded="true">
                                    <span v-if="form.channel === ''">Select Channel</span>
                                    <span v-else>{{ form.channel }}</span>
                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                         fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5 8a1 1 0 011.707 0L10 11.293l3.293-3.294a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <template #content>
                            <div class="py-1" role="none">
                                <button v-for="channel in channels"
                                        type="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                        role="menuitem" @click="form.channel = channel">
                                    {{ channel }}
                                </button>
                            </div>
                        </template>
                    </DropDown>
                    <InputError class="mt-2" :message="form.errors.channel" />
                </div>
                <div>
                    <InputLabel for="format" value="Format" />
                    <DropDown align="left" width="60">
                        <template #trigger>
                            <div class="relative">
                                <button type="button"
                                        class="inline-flex justify-between w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        id="format-options-menu" aria-haspopup="true" aria-expanded="true">
                                    <span v-if="form.format === ''">Select Format</span>
                                    <span v-else>{{ form.format }}</span>
                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                         fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5 8a1 1 0 011.707 0L10 11.293l3.293-3.294a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <template #content>
                            <div class="py-1" role="none">
                                <button v-for="format in formats"
                                        type="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                        role="menuitem" @click="form.format = format">
                                    {{ format }}
                                </button>
                            </div>
                        </template>
                    </DropDown>
                    <InputError class="mt-2" :message="form.errors.channel" />
                </div>
                <div>
                    <InputLabel for="address" value="Address" />
                    <TextInput
                        id="address"
                        v-model="form.address"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="address"
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>
                <div>
                    <InputLabel for="command" value="Command" />
                    <TextInput
                        id="command"
                        v-model="form.command"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="command"
                    />
                    <InputError class="mt-2" :message="form.errors.command" />
                </div>
                <div>
                    <InputLabel for="pkg" value="Package" />
                    <DropDown align="left" width="60">
                        <template #trigger>
                            <div class="relative">
                                <button type="button"
                                        class="inline-flex justify-between w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        id="package-options-menu" aria-haspopup="true" aria-expanded="true">
                                    <span v-if="form.pkg === null">Select Package</span>
                                    <span v-else>{{ form.pkg.name }}</span>
                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                         fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5 8a1 1 0 011.707 0L10 11.293l3.293-3.294a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <template #content>
                            <div class="py-1" role="none">
                                <button v-for="pkg in pkgs" :key="pkg.code"
                                        type="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                        role="menuitem" @click="form.pkg = pkg">
                                    {{ pkg.name }}
                                </button>
                            </div>
                        </template>
                    </DropDown>
                    <InputError class="mt-2" :message="form.errors.pkgs" />
                </div>

                <PrimaryButton class="mt-4">New Organization</PrimaryButton>
            </form>
            <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
                <Organization
                    v-for="organization in organizations"
                    :key="organization.id"
                    :organization="organization"
                />
            </div>
        </div>
    </AppLayout>
</template>
