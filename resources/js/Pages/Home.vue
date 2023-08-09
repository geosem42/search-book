<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Home" />
    <div class="container flex flex-col items-center justify-center min-h-screen mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-4">Welcome to 'search-book'</h1>
        <p class="text-center text-gray-700 mb-2">This is a simple application that allows you to upload PDF files and search for any words, terms, phrases, etc.</p>
        <p class="text-center text-gray-700 mb-8">It displays a list of matches while showing snippets and information about where in the document the match was found.</p>
        <div class="flex justify-center">
            <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md mr-4">Login</a>
            <a href="/register" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Register</a>
        </div>
    </div>
</template>
