<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed, watch } from 'vue';

const file = ref(null);
const fileName = ref(null);
const fileInput = ref(null);
const query = ref('');
const results = ref([]);
const documents = ref([]);
const uploading = ref(false);
const fetchingDocuments = ref(false);
const selectedDocument = ref(null);
const searchModalOpen = ref(false);
const searchSubmitted = ref(false);
const searching = ref(false);
const uploadError = ref('');

const selectFile = (event) => {
	file.value = event.target.files[0];
};

const clearFileInput = () => {
	if (fileInput.value) {
		fileInput.value.value = '';
	}
};

const submitUpload = async () => {
	try {
		if (file.value) {
			uploading.value = true;
			const data = new FormData();
			data.append('file', file.value);

			// Fetch the CSRF token from the server
			const response = await fetch('/csrf-token');
			const json = await response.json();
			const token = json.csrfToken;

			const uploadResponse = await fetch('/upload', {
				method: 'POST',
				headers: {
					// Include the CSRF token as a request header
					'X-CSRF-TOKEN': token,
				},
				body: data,
			});

			const uploadData = await uploadResponse.json();

			if (uploadData.success) {
				// Fetch updated list of documents
				await fetchDocuments();
				uploadError.value = '';
			} else {
				uploadError.value = uploadData.error;
			}
		}
	} catch (error) {
		console.error(error);
	} finally {
		clearFileInput();
		fileName.value = '';
		uploading.value = false;
	}
};


const openSearchModal = (document) => {
	// Set the selected document
	selectedDocument.value = document;

	// Open the search modal
	searchModalOpen.value = true;
};

const closeSearchModal = () => {
	// Clear the search query
	query.value = '';

	// Clear the search results
	results.value = [];

	searchModalOpen.value = false;
};

const submitSearch = async () => {
	try {
		searchSubmitted.value = true;
		searching.value = true;
		const response = await fetch(`/search?document=${selectedDocument.value.id}&query=${encodeURIComponent(query.value)}`);
		const data = await response.json();

		results.value = data.results;
	} catch (error) {
		console.error(error);
	} finally {
		searching.value = false;
	}
};

const formattedResults = computed(() => {
	return results.value.map((result) => {
		// Limit the length of the snippet to 255 characters
		let snippet = result.snippet.substring(0, 600);

		// Add three dots before and after the snippet
		snippet = '...' + snippet + '...';

		// Create a regular expression that matches the search query with optional spaces between the words
		const regex = new RegExp(`(${query.value.replace(/\s+/g, '\\s+')})`, 'gi');

		// Replace all occurrences of the search query with a highlighted version
		snippet = snippet.replace(regex, '<span class="text-blue-600 font-bold">$1</span>');

		return {
			term: query.value,
			page: result.page,
			snippet: snippet,
		};
	});
});



const fetchDocuments = async () => {
	fetchingDocuments.value = true;

	try {
		const response = await fetch('/documents');
		const data = await response.json();

		documents.value = data.documents;
	} catch (error) {
		console.error(error);
	} finally {
		fetchingDocuments.value = false;
	}
};

onMounted(fetchDocuments);

watch(query, () => {
	searchSubmitted.value = false;
});

</script>

<template>
	<AppLayout title="Dashboard">
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">
				Dashboard
			</h2>
		</template>

		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
					<form @submit.prevent="submitUpload" class="flex flex-row items-center p-4 bg-white rounded-md gap-3"
						enctype="multipart/form-data">
						<input
							class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200"
							type="file" id="file" name="file" accept=".pdf" ref="fileInput" @change="selectFile" />
						<button type="submit"
							class="relative flex items-center justify-center px-4 py-2 font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 min-w-[8rem] min-h-[2.5rem]">
							<span v-if="!uploading" class="flex items-center justify-center">Upload</span>
							<span v-if="uploading" class="absolute inset-0 flex items-center justify-center">
								<span class="spinner"></span>
							</span>
						</button>
					</form>

					<div v-if="uploadError" class="text-red-600 px-4">{{ uploadError }}</div>

					<div class="mt-8" v-if="documents.length > 0">
						<h3 class="mb-4 text-lg font-medium text-blue-600 px-4">Your Documents</h3>

						<div v-if="fetchingDocuments" class="flex items-center justify-center p-4">
							<span class="spinner"></span>
						</div>

						<table v-if="!fetchingDocuments" class="w-full table-auto">
							<thead>
								<tr>
									<th class="px-4 py-2 text-left">Name</th>
									<th class="px-4 py-2 text-left">Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="document in documents" :key="document.id">
									<td class="border px-4 py-2">{{ document.original_name }}</td>
									<td class="border px-4 py-2">
										<svg @click.prevent="openSearchModal(document)" xmlns="http://www.w3.org/2000/svg" fill="none"
											viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 search">
											<path stroke-linecap="round" stroke-linejoin="round"
												d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
										</svg>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div v-if="!fetchingDocuments && documents.length === 0" class="text-center my-8text-gray-600">
						You don't have any files.
					</div>

				</div>
			</div>
		</div>

		<div v-if="searchModalOpen && selectedDocument"
			class="fixed inset-0 z-10 flex items-center justify-center bg-black bg-opacity-50">
			<div class="rounded-lg relative w-full max-w-screen-lg h-full max-h-[80vh] overflow-y-auto">
				<button class="absolute z-10 top-[1rem] right-[1rem] w-6 h-6 text-gray-100 bg-slate-600 rounded-full text-xs" @click="closeSearchModal">X</button>
				<div class="modal-header">
					<h3 class="text-lg font-medium mb-4">{{ selectedDocument.original_name }}</h3>
					<form @submit.prevent="submitSearch" class="flex items-center mb-4 gap-2">
						<input type="text" v-model="query" class="border border-gray-300 rounded-md px-2 py-1 flex-grow" />
						<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Search</button>
					</form>
				</div>

				<div v-if="searchSubmitted && !searching && results.length === 0" class="text-center text-gray-600 bg-white py-4">
					No results found.
				</div>

				<ul class="bg-white divide-y divide-gray-200">
					<li v-for="(result, index) in formattedResults" :key="index" class="px-4 py-4 sm:px-6">
						<div class="flex items-center justify-between">
							<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
								Page {{ result.page }}
							</span>
						</div>
						<div class="mt-2 sm:flex sm:justify-between">
							<div class="sm:flex">
								<p class="text-sm text-gray-500" v-html="result.snippet"></p>
							</div>
						</div>
					</li>
				</ul>

			</div>
		</div>

	</AppLayout>
</template>

<style>
@keyframes spinner {
	to {
		transform: rotate(360deg);
	}
}

.spinner {
	display: block;
	width: 1em;
	height: 1em;
	margin: auto;
	border: 0.2em solid transparent;
	border-top-color: currentColor;
	border-radius: 50%;
	animation: spinner 0.6s linear infinite;
}

.modal-header {
	position: sticky;
	top: 0;
	background-color: #f3f3f3;
	padding: 1rem;
	border-bottom: 1px solid #e5e5e5;
}

.search {
	cursor: pointer;
}
</style>